<?php
namespace MZ_Mindbody\Inc\Libraries;

use MZ_Mindbody as NS;
use MZ_Mindbody\Inc\Common as Common;
use MZ_Mindbody\Inc\Core as Core;
use \Exception as Exception;


/*
 * All Interface methods for MBO v6 API, via wordpress wrapper for CURL
 *
 *
 */
class MBO_V6_API {
	
	private $tokenRequestTries = 6;
		
	protected $headersBasic = [
		'User-Agent' => '',
		'Content-Type' => 'application/json; charset=utf-8',
		'Api-Key' => '',
		'SiteId' => '-99'
	];
	
	protected $apiMethods = array();
	
	protected $extraCredentials = array();
	
	protected $basic_options;
	
	/*
	 * Get stored tokens when good and store new ones
	 *
	 */
	protected $token_management;
	
	
	/*
     * Shortcode Attributes
     *
     * @since 2.6.7
     * @access private
     */
	private $atts;

	/**
	* Initialize the apiServices and apiMethods arrays
	*/
	public function __construct( $mbo_dev_credentials = array(), $atts = array() ) {

        // $mbo_dev_credentials = $this->basic_options = Core\MZ_Mindbody_Api::$basic_options;
		$this->basic_options = $mbo_dev_credentials;
		
		$this->atts = $atts;
		
		$this->token_management = new Common\Token_Management;		
		
		// set credentials into headers
		if (!empty($mbo_dev_credentials)) {
			//if(!empty($mbo_dev_credentials['mz_mbo_app_name'])) {
			//	$this->headersBasic['App-Name'] = $mbo_dev_credentials['mz_mbo_app_name'];
				// If this matches actual app name, requests fail ;
			//}
			if(!empty($mbo_dev_credentials['mz_mbo_api_key'])) {
				$this->headersBasic['Api-Key'] = $mbo_dev_credentials['mz_mbo_api_key'];
			}
			if(!empty($mbo_dev_credentials['mz_mbo_app_name'])) {
				$this->headersBasic['User-Agent'] = $mbo_dev_credentials['mz_mbo_app_name'];
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
	*
	* since 2.5.7
	*
	* @param $name string that matches method in matrix of MBO API v6 Methods
	* @param $arguments array used in API request 
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
			throw new Exception('Too many API Calls.');
		}

		if ((isset(NS\Inc\Core\MZ_Mindbody_Api::$advanced_options['log_api_calls'])) && (NS\Inc\Core\MZ_Mindbody_Api::$advanced_options['log_api_calls'] == 'on')):
			$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1];
			$caller_details = $trace['function'];
			if (isset($trace['class'])){
				$caller_details .= ' in:' . $trace['class'];
			}
			NS\MZMBO()->helpers->api_log(print_r($restMethod, true) . ' caller:' . $caller_details);
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
			'SendPasswordResetEmail',
			'CheckoutShoppingCart'
		];
		
		// Certain methods don't require credentials
		if ( !in_array($restMethod['name'], $method_without_username) ) {

			$request_body = array_merge( $requestData,
							array( 
									'Username' => $this->format_username(), 
									'Password' => $this->extraCredentials['Password'],
									'Limit' => 200,
							)
						);
			
			// Maybe there's a stored token to use
			$token = $this->token_management->get_stored_token();
			
		    if ( ctype_alnum($token['AccessToken']) ) {
		        $request_body['Access'] = $token['AccessToken'];
		    } else {
		        $request_body['Access'] = $this->tokenRequest( $restMethod );
		    }
								
		} else {
		
			$request_body = $requestData;
			
		}
		
		if (!empty($this->atts['session_type_ids'])) {
		    $request_body['SessionTypeIds'] = $this->atts['session_type_ids'];
		}
		
		if ( in_array($restMethod['name'], $encoded_request_body) ) {
		
			$request_body = json_encode($request_body);
			
		}
		
		// For some reason, for this call, we don't want to convert
		// 'body' into a json string, as we do in the token request
		$response = wp_remote_request( $restMethod['endpoint'], 
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
		
		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			return "Something went wrong: " . $error_message;
		} else {

			$response_body = json_decode($response['body'], true);
			
			if ( is_array($response_body) && array_key_exists( 'Error', $response_body ) && strpos($response_body['Error']["Message"], 'Please try again') ) {
				// OK try again after three seconds
				//sleep(3);
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
	* As above, but specifically for token requests.
	*
	* Get a stored token if there's a good one availaible,
	* if not, request one from the API.
	*
	* return string of MBO API Response data
	*/
	protected function tokenRequest($restMethod) {
	
		$this->tokenRequestTries--;
						
		$request_body = array( 
								'Username' => $this->format_username(), 
								'Password' => $this->extraCredentials['Password'] 
						);
		
		$response = wp_remote_post( "https://api.mindbodyonline.com/public/v6/usertoken/issue", 
			array(
				'method' => 'POST',
				'timeout' => 90,
				'httpversion' => '1.0',
				'blocking' => true,
				'headers' => $restMethod['headers'],
				'body' => json_encode($request_body),
				'cookies' => array()
			) );
			
		if ( is_wp_error( $response ) ) {
		
			$error_message = $response->get_error_message();
			return "Something went wrong with token request: " . $error_message;
			
		} else {

				$response_body = json_decode($response['body']);
				if ( property_exists( $response_body, 'Error' ) && strpos($response_body->Error->Message, 'Please try again') ) {
					// OK try again after three seconds
					sleep(3);
					if($this->tokenRequestTries > 1) {
						return $this->tokenRequest($restMethod);
					}
					return false;
				}

				$this->token_management->save_token_to_option($response_body);
				return $response_body;
		}
	}
	
	/**
	* Return username string formatted based on if Sourcename of Staff Name
	*
	* @since 2.5.7
	* @used by tokenRequest(), callMindbodyService()
	*
	* return string of MBO API user name with our without preceding underscore
	*/
	protected function format_username() {

		if ($this->basic_options['sourcename_not_staff'] == 'on'){
			return '_' . $this->extraCredentials['SourceName'];
		} else {
			return $this->extraCredentials['SourceName'];
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
    	if ($mz_mbo_api_calls['today'] < date("Y-m-d")) {
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
        
    	// Don't limit if using sandbox
    	if ((isset(NS\MZMBO()::$basic_options['mz_mindbody_siteID'])) && (NS\MZMBO()::$basic_options['mz_mindbody_siteID'] == '-99')) return true;
    	
    	if (NS\MZMBO()::$mz_mbo_api_calls['calls'] - 1200 > NS\MZMBO()::$advanced_options['api_call_limit']) {
    		add_action( 'plugins_loaded', array($this, 'admin_call_excess_alert'), 10);
    	};
    	if (NS\MZMBO()::$mz_mbo_api_calls['calls'] > NS\MZMBO()::$advanced_options['api_call_limit']) {
    		return false;
    	};
    	return true;
    }
    
    
    /*
     * Make the admin notification via wp_mail
     * 
     * 
     */
    public function admin_call_excess_alert(){
        $to = get_option('admin_email');
        $subject = __( 'Large amount of MBO API Calls', 'mz-mindbody-api' );
        $message = sprintf(__('Check your website and MBO. There have been %1$s calls to the API so far today. You have set a maximum of %2$s in the Admin.', 'mz-mindbody-api'),
                            NS\MZMBO()::$mz_mbo_api_calls['calls'], NS\MZMBO()::$advanced_options['api_call_limit']);
        $headers = array('Content-Type: text/html; charset=UTF-8');
        wp_mail($to, $subject, $message, $headers);
    }
    
	public function debug() {
		$return = "<textarea rows='6' cols='90'>";
		$return .= print_r($this->GetClasses([
										'method' => 'GET',
										'name' => 'GetClasses', 
										'endpoint' => 'https://api.mindbodyonline.com/public/v6/class/classes',
										'headers' => $this->headersBasic
									 ]), 1);
		$return .= "</textarea>";

		return $return;
	}

	public function makeNumericArray($data) {
		return (isset($data[0])) ? $data : array($data);
	}

}

?>
