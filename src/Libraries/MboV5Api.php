<?php
/**
 * Mindbody V5 API
 *
 * This file contains the class for interfacing with the old, v5 Mindbody API.
 *
 * @package MzMindbody
 */

namespace MZoo\MzMindbody\Libraries;

use MZoo\MzMindbody as NS;
use \Exception as Exception;

class MboV5Api extends MboApi {


	protected $client;

	protected $appointmentServiceWSDL = 'https://api.mindbodyonline.com/0_5/AppointmentService.asmx?WSDL';
	protected $classServiceWSDL       = 'https://api.mindbodyonline.com/0_5/ClassService.asmx?WSDL';
	protected $clientServiceWSDL      = 'https://api.mindbodyonline.com/0_5/ClientService.asmx?WSDL';
	protected $dataServiceWSDL        = 'https://api.mindbodyonline.com/0_5/DataService.asmx?WSDL';
	protected $saleServiceWSDL        = 'https://api.mindbodyonline.com/0_5/SaleService.asmx?WSDL';
	protected $siteServiceWSDL        = 'https://api.mindbodyonline.com/0_5/SiteService.asmx?WSDL';
	protected $staffServiceWSDL       = 'https://api.mindbodyonline.com/0_5/StaffService.asmx?WSDL';

	protected $api_methods = array();
	protected $apiServices = array();

	public $soapOptions;

	public $debugSoapErrors = true;

	/**
	 * Initialize the apiServices and api_methods arrays
	 */
	public function __construct( $source_credentials = array() ) {

		$this->soapOptions = array(
			'soap_version'   => SOAP_1_1,
			'trace'          => true,
			'exceptions'     => true,
            'cache_wsdl' => WSDL_CACHE_NONE,
			'stream_context' => stream_context_create(
				array(
					'ssl' => array(
						'crypto_method'     => STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT,
						'ciphers'           => 'SHA256',
						'verify_peer'       => false,
						'verify_peer_name'  => false,
						'allow_self_signed' => true,
					),
				)
			),
		);
		// set apiServices array with Mindbody WSDL locations
		$this->apiServices = array(
			'AppointmentService' => $this->appointmentServiceWSDL,
			'ClassService'       => $this->classServiceWSDL,
			'ClientService'      => $this->clientServiceWSDL,
			'DataService'        => $this->dataServiceWSDL,
			'SaleService'        => $this->saleServiceWSDL,
			'SiteService'        => $this->siteServiceWSDL,
			'StaffService'       => $this->staffServiceWSDL,
		);
		
		// set api_methods array with available methods from Mindbody services
		foreach ( $this->apiServices as $service_name => $serviceWSDL ) {
			try {
				$this->client = new \SoapClient( $serviceWSDL, $this->soapOptions );
			} catch ( \SoapFault $s ) {
				throw new \Exception( 'NO_API_SERVICE: ' . $s );
				error_clear_last();
			}
			$this->api_methods = array_merge(
				$this->api_methods,
				array(
					$service_name => array_map(
						function ( $n ) {
							$start = 1 + strpos( $n, ' ' );
							$end = strpos( $n, '(' );
							$length = $end - $start;
							return substr( $n, $start, $length );
						},
						$this->client->__getFunctions()
					),
				)
			);
		}

		// set source_credentials
		if ( ! empty( $source_credentials ) ) {
			if ( ! empty( $source_credentials['SourceName'] ) ) {
				$this->source_credentials['SourceName'] = $source_credentials['SourceName'];
			}
			if ( ! empty( $source_credentials['Password'] ) ) {
				$this->source_credentials['Password'] = $source_credentials['Password'];
			}
			if ( ! empty( $source_credentials['SiteIDs'] ) ) {
				if ( is_array( $source_credentials['SiteIDs'] ) ) {
					$this->source_credentials['SiteIDs'] = $source_credentials['SiteIDs'];
				} elseif ( is_numeric( $source_credentials['SiteIDs'] ) ) {
					$this->source_credentials['SiteIDs'] = array( $source_credentials['SiteIDs'] );
				}
			}
		}
	}

	/**
	 * magic method will search $this->api_methods array for $name and call the
	 * appropriate Mindbody API method if found
	 */
	public function __call( $name, $arguments ) {
		// check if method exists on one of mindbody's soap services
		$soapService = false;
		foreach ( $this->api_methods as $api_service_name => $api_methods ) {
			if ( in_array( $name, $api_methods ) ) {
				$soapService = $api_service_name;
			}
		}
		
		if ( ! empty( $soapService ) ) {
			
			$this->track_daily_api_calls();

			$within_api_call_limits = $this->api_call_limiter();

			if ( false === $within_api_call_limits ) {
				throw new Exception( 'Too many API Calls.' );
			}

			$this->maybe_log_daily_api_calls($soapService . ' ' . $name);

			if ( empty( $arguments ) ) {
				return $this->call_mindbody_service( $soapService, $name );
			} else {
				switch ( count( $arguments ) ) {
					case 1:
						return $this->call_mindbody_service( $soapService, $name, $arguments[0] );
					case 2:
						return $this->call_mindbody_service( $soapService, $name, $arguments[0], $arguments[1] );
				}
			}
		} else {
			echo "called unknown method '$name'<br />";
			return 'NO_API_SERVICE';
		}
	}

	/**
	 * * return the results of a Mindbody API method
	 * *
	 * * string $service_name   - Mindbody Soap service name
	 * * string $method_name    - Mindbody API method name
	 * * array $request_data    - Optional: parameters to API methods
	 * * boolean $return_object - Optional: Return the SOAP response object
	 */
	protected function call_mindbody_service( $service_name, $method_name, $request_data = array(), $return_object = false, $debug_errors = false ) {

		$request = array_merge( array( 'SourceCredentials' => $this->source_credentials ), $request_data );
		if ( ! empty( $this->userCredentials ) ) {
			$request = array_merge( array( 'UserCredentials' => $this->userCredentials ), $request );
		}
		try {
			$this->client = new \SoapClient( $this->apiServices[ $service_name ], $this->soapOptions );
			$result       = $this->client->$method_name( array( 'Request' => $request ) );
			if ( $return_object ) {
				return $result;
			} else {
				return json_decode( json_encode( $result ), 1 );
			}
		} catch ( \SoapFault $s ) {
			// Uncomment following line for debugging request errors.
			// NS\MZMBO()->helpers->print($s);
			// NS\MZMBO()->helpers->print($this->debugSoapErrors);
			if ( $this->debugSoapErrors && $debug_errors ) {
				echo 'ERROR: [' . $s->faultcode . '] ' . $s->faultstring;
				$this->debug();
				return false;
			}
			echo '<h1>Could not connect to MBO server.</h1>';
			return false;
		} catch ( \Exception $e ) {
			if ( $this->debugSoapErrors && $debug_errors ) {
				echo 'ERROR: ' . $e->getMessage();
				return false;
			}
		}
	}

	public function getXMLRequest() {
		return $this->client->__getLastRequest();
	}

	public function getXMLResponse() {
		return $this->client->__getLastResponse();
	}

	public function debug() {

		$return  = "<h3>Request:</h3><textarea rows='6' cols='90'>" . print_r( $this->getXMLRequest(), 1 ) . '</textarea>';
		$return .= "<h3>Response:</h3><textarea rows='6' cols='90'>" . print_r( $this->getXMLResponse(), 1 ) . '</textarea>';

		return $return;
	}

	public function make_numeric_array( $data ) {
		return ( isset( $data[0] ) ) ? $data : array( $data );
	}

	public function replace_empty_arrays_with_nulls( array $array ) {
		foreach ( $array as &$value ) {
			if ( is_array( $value ) ) {
				if ( empty( $value ) ) {
					$value = null;
				} else {
					$value = $this->replace_empty_arrays_with_nulls( $value );
				}
			}
		}
		return $array;
	}

	/**
	 * FunctionDataXml
	 *
	 * Process FunctionDataXml results
	 */
	public function FunctionDataXml() {
		$passed        = func_get_args();
		$request       = empty( $passed[0] ) ? null : $passed[0];
		$return_object = empty( $passed[1] ) ? null : $passed[1];
		$debug_errors  = empty( $passed[2] ) ? null : $passed[2];
		$data          = $this->call_mindbody_service( 'DataService', 'FunctionDataXml', $request );
		$xmlString     = $this->getXMLResponse();
		$sxe           = new \SimpleXMLElement( $xmlString );
		$sxe->registerXPathNamespace( 'mindbody', 'http://clients.mindbodyonline.com/api/0_5' );
		$res = $sxe->xpath( '//mindbody:FunctionDataXmlResponse' );
		if ( $return_object ) {
			return $res[0];
		} else {
			$arr = $this->replace_empty_arrays_with_nulls( json_decode( json_encode( $res[0] ), 1 ) );
			if ( isset( $arr['FunctionDataXmlResult']['Results']['Row'] ) && is_array( $arr['FunctionDataXmlResult']['Results']['Row'] ) ) {
				$arr['FunctionDataXmlResult']['Results']['Row'] = $this->make_numeric_array( $arr['FunctionDataXmlResult']['Results']['Row'] );
			}
			return $arr;
		}
	}
}
