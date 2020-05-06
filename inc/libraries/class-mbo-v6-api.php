<?php
namespace MZ_Mindbody\Inc\Libraries;

use MZ_Mindbody as NS;
use MZ_Mindbody\Inc\Common as Common;


/*
 * All Interface methods for MBO v6 API, via wordpress wrapper for CURL
 *
 *
 */
class MBO_V6_API {
	
	private $tokenRequestTries = 6;
		
	protected $headersBasic = [
		'Content-Type' => 'application/json; charset=utf-8',
		'Api-Key' => '',
		'SiteId' => '-99'
	];
	
	protected $apiMethods = array();
	
	protected $extraCredentials = array();

	/**
	* Initialize the apiServices and apiMethods arrays
	*/
	public function __construct($mbo_dev_credentials = array()) {
	
		// set credentials into headers
		if (!empty($mbo_dev_credentials)) {
			//if(!empty($mbo_dev_credentials['mz_mbo_app_name'])) {
			//	$this->headersBasic['App-Name'] = $mbo_dev_credentials['mz_mbo_app_name'];
				// If this matches actual app name, requests fail ;
			//}
			if(!empty($mbo_dev_credentials['mz_mbo_api_key'])) {
				$this->headersBasic['Api-Key'] = $mbo_dev_credentials['mz_mbo_api_key'];
			}
			if(!empty($mbo_dev_credentials['mz_source_name'])) {
				$this->extraCredentials['SourceName'] = $mbo_dev_credentials['mz_source_name'];
			}
			if(!empty($mbo_dev_credentials['mz_mindbody_password'])) {
				$this->extraCredentials['Password'] = $mbo_dev_credentials['mz_mindbody_password'];
			}
			if(!empty($mbo_dev_credentials['mz_mindbody_siteID'])) {
				if(is_array($mbo_dev_credentials['mz_mindbody_siteID'])) {
					$this->headersBasic['SiteIDs'] = $mbo_dev_credentials['mz_mindbody_siteID'][0];
				} else if(is_numeric($mbo_dev_credentials['mz_mindbody_siteID'])) {
					$this->headersBasic['SiteId'] = $mbo_dev_credentials['mz_mindbody_siteID'];
				}
			}
		}
		
		// set apiServices array with Mindbody endpoints
		// moved to a separate file for convenience.
		$mbo_methods = new MBO_V6_API_METHODS($this->headersBasic);
		
		$this->apiMethods = $mbo_methods->methods;
	}
	
	/**
	* magic method will search $this->apiMethods array for $name and call the
	* appropriate Mindbody API method if found
	*/
	public function __call($name, $arguments) {
	
		$restMethod = '';
				
		foreach($this->apiMethods as $apiServiceName => $apiMethods) {
			if ( array_key_exists( $name, $apiMethods ) ) {
				$restMethod = $apiMethods[$name];
			}
		}

		if (empty($restMethod)) {
			return 'Nonexistent Method';
		};

		$this->track_daily_api_calls();
		
		if (!$this->api_call_limiter()) {
			return 'Too many API Calls.';
		}

		if ((isset(NS\Inc\Core\MZ_Mindbody_Api::$advanced_options['log_api_calls'])) && (NS\Inc\Core\MZ_Mindbody_Api::$advanced_options['log_api_calls'] == 'on')):
			$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1];
			$caller_details = $trace['function'];
			if (isset($trace['class'])){
				$caller_details .= ' in:' . $trace['class'];
			}
			NS\MZMBO()->helpers->api_log($restMethod . ' caller:' . $caller_details);
		endif;
		
		if(empty($arguments)) {
			return $this->callMindbodyService($restMethod);
		} else {
			switch(count($arguments)) {
				case 1:
					return $this->callMindbodyService($restMethod, $arguments[0]);
				case 2:
					return $this->callMindbodyService($restMethod, $arguments[0], $arguments[1]);
			}
		}
	}

	/**
	** return the results of a Mindbody API method
	**
	** string $serviceName   - Mindbody Soap service name
	** string $methodName    - Mindbody API method name
	** array $requestData    - Optional: parameters to API methods
	** boolean $returnObject - Optional: Return the SOAP response object
	*/
	protected function callMindbodyService($restMethod, $requestData = array()) {
		
		if ($restMethod['name'] == 'TokenIssue') {
			// If this is a token request, make a separate call so that we 
			// don't create a loop here
			
			$token = $this->tokenRequest( $restMethod );
			
			return $token;
		} 
		
		
		// Certain methods don't require credentials
		$method_without_username = [
			'AddClient'
		];
		
		
		// Certain methods want json strings
		$encoded_request_body = [
			'AddClient',
			'SendPasswordResetEmail'
		];
		
		// Certain methods don't require credentials
		if ( !in_array($restMethod['name'], $method_without_username) ) {
		
			$request_body = array_merge( $requestData,
							array( 
									'Username' => '_' . $this->extraCredentials['SourceName'], 
									'Password' => $this->extraCredentials['Password'] 
							)
						);
					
			$request_body['Access'] = $this->tokenRequest( $restMethod );
			
		} else {
		
			$request_body = $requestData;
			
		}
		
		if ( in_array($restMethod['name'], $encoded_request_body) ) {
		
			$request_body = json_encode($request_body);
			
		}
		
		// For some reason, for this call, we don't want to convert
		// 'body' into a json string, as we do in the token request
		$response = wp_remote_post( $restMethod['endpoint'], 
			array(
				'method' => $restMethod['method'],
				'timeout' => 45,
				'httpversion' => '1.0',
				'blocking' => true,
				'headers' => $restMethod['headers'],
				'body' => $request_body,
				'data_format' => 'body',
				'cookies' => array()
			) );
		
		$response_body = json_decode($response['body'], true);
		
		// print_r('%%%% response body array keys: %%%%');
		// print_r(array_keys(json_decode($response['body'], true)));
		// print_r('%%%% response body array keys: %%%%');
		// print_r(json_decode($response['body'], true)['PaginationResponse']);
		// print_r('%%%% response body array keys: %%%%');
		// print_r(array_keys(json_decode($response['body'], true)['Classes'][0]));
		// print_r('%%%% response : %%%%');
		// print_r($response['response']);

		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			return "Something went wrong: " . $error_message;
		} else {

		if ( is_array($response_body) && array_key_exists( 'Error', $response_body ) && strpos($response_body['Error']["Message"], 'Please try again') ) {
				// OK try again after three seconds
				sleep(3);
				if($this->tokenRequestTries > 1) {
					return $this->callMindbodyService($restMethod, $requestData);
				}
				return false;
			}
			
		// return the data as an array, which is what we are used to
		return $response_body;
		
		}
	}
	
	/**
	* return the results of a Mindbody API method, specific to token
	*
	* @since 2.5.7
	*
	* As above, but specifically for token requests
	*
	* return string of MBO API Response data
	*/
	protected function tokenRequest($restMethod) {
		
		$this->tokenRequestTries--;
		
		$tm = new Common\Token_Management;
		
		$token = $tm->get_stored_token();
		
		if ( ctype_alnum($token) ) return $token;
		
		$request_body = array( 
								'Username' => '_' . $this->extraCredentials['SourceName'], 
								'Password' => $this->extraCredentials['Password'] 
						);
		
		$response = wp_remote_post( $restMethod['endpoint'], 
			array(
				'method' => 'POST',
				'timeout' => 90,
				'httpversion' => '1.0',
				'blocking' => true,
				'headers' => $restMethod['headers'],
				'body' => json_encode($request_body),
				'cookies' => array()
			) );

		$response_body = json_decode($response['body']);
	 		
		if ( is_wp_error( $response ) ) {
		
			$error_message = $response->get_error_message();
			return "Something went wrong with token request: " . $error_message;
			
		} else {
				if ( property_exists( $response_body, 'Error' ) && strpos($response_body->Error->Message, 'Please try again') ) {
					// OK try again after three seconds
					sleep(3);
					if($this->tokenRequestTries > 1) {
						return $this->tokenRequest($restMethod);
					}
					return false;
				}
				return $response_body;
		}
	}
    
    /*
     * Track API requests per day
     * 
     * There is a 1000 call limit per day on MBO, per location. 
     * Any calls above that number per location are 
     * charged at the overage rate of 1/3 cent each.
     * 
     * 
     */
    private function track_daily_api_calls() {
    	// If not set, initiate array to track mbo calls.
    	if (!$mz_mbo_api_calls = get_option('mz_mbo_api_calls')) {
    		$mz_mbo_api_calls = array(
    			'calls' => 2,
    			'today' => date("Y-m-d")
    		);
    	}
    	if ($mz_mbo_api_calls['today'] > date("Y-m-d")) {
    		// If it's a new day, reinitialize the matrix
    		$mz_mbo_api_calls = array('today' => date("Y-m-d"), 'calls' => 1);
            update_option('mz_mbo_api_calls', $mz_mbo_api_calls);
    	};
    	// Otherwise increase the call count
    	$mz_mbo_api_calls['calls'] += 1;
    	update_option('mz_mbo_api_calls', $mz_mbo_api_calls);
    }
    
    /*
     * Limit the number of API requests per day
     * 
     * There is a 1000 call limit per day on MBO, per location. 
     * Notify the admin when we get close and stop making calls
     * when we get to $3USD in overages.
     * 
     * 
     */
    private function api_call_limiter() {
    	$mz_mbo_api_calls = get_option('mz_mbo_api_calls');

    	if ($mz_mbo_api_calls['calls'] > 800) {
    		$to = get_option('admin_email');
			$subject = __( 'Large amount of MBO API Calls', 'mz-mindbody-api' );
			$message = __( 'Check your website and MBO. There seems to be an issue.', 'mz-mindbody-api' );
			$headers = array('Content-Type: text/html; charset=UTF-8');
			wp_mail( $to, $subject, $message, $headers);
    	};
    	if ($mz_mbo_api_calls['calls'] > 2000) {
    		return false;
    	};
    	return true;
    }
    
	public function debug() {

		$return = "<textarea rows='6' cols='90'>".print_r('debug will go here')."</textarea>";
		return $return;
	}

	public function makeNumericArray($data) {
		return (isset($data[0])) ? $data : array($data);
	}

}

?>
