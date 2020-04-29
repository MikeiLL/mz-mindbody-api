<?php
namespace MZ_Mindbody\Inc\Libraries;

use MZ_Mindbody as NS;

class MBO_V6_API {
	
	protected $endpointClasses = 'https://api.mindbodyonline.com/public/v6/class';
	protected $endpointAppointment = 'https://api.mindbodyonline.com/public/v6/appointment';
	protected $endpointClient = 'https://api.mindbodyonline.com/public/v6/client';
	protected $endpointEnrollment = 'https://api.mindbodyonline.com/public/v6/enrollment';
	protected $endpointSale = 'https://api.mindbodyonline.com/public/v6/sale';
	protected $endpointSite = 'https://api.mindbodyonline.com/public/v6/site';
	protected $endpointStaff = 'https://api.mindbodyonline.com/public/v6/staff';
	protected $endpointUserToken = 'https://api.mindbodyonline.com/public/v6/usertoken';
	
	protected $headersBasic = [
		'Content-Type' => 'application/json',
		'Api-Key' => '',
		'Siteid' => '-99'
	];
	
	protected $apiMethods = array();
	
	protected $extraCredentials = array();

	public $debugSoapErrors = true;

	/**
	* Initialize the apiServices and apiMethods arrays
	*/
	public function __construct($mbo_dev_credentials = array()) {
	
		// set credentials into headers
		if (!empty($mbo_dev_credentials)) {
			if(!empty($mbo_dev_credentials['mz_mbo_app_name'])) {
				$this->headersBasic['App-Name'] = $mbo_dev_credentials['mz_mbo_app_name'];
			}
			if(!empty($mbo_dev_credentials['mz_mbo_api_key'])) {
				$this->headersBasic['Api-Key'] = $mbo_dev_credentials['mz_mbo_api_key'];
			}
			if(!empty($mbo_dev_credentials['SourceName'])) {
				$this->extraCredentials['SourceName'] = $mbo_dev_credentials['SourceName'];
			}
			if(!empty($mbo_dev_credentials['Password'])) {
				$this->extraCredentials['Password'] = $mbo_dev_credentials['Password'];
			}
			if(!empty($mbo_dev_credentials['Siteid'])) {
				if(is_array($mbo_dev_credentials['SiteIDs'])) {
					$this->headersBasic['SiteIDs'] = $mbo_dev_credentials['SiteIDs'][0];
				} else if(is_numeric($mbo_dev_credentials['SiteIDs'])) {
					$this->headersBasic['SiteId'] = $mbo_dev_credentials['SiteIDs'];
				}
			}
		}

		// set apiServices array with Mindbody WSDL locations
		$this->apiMethods = [
			'AppointmentService' => [
				'AddApppointment' => [
										'method' => 'POST',
										'endpoint' => $this->endpointAppointment . '/addappointment',
										'headers' => $this->headersBasic
									 ],
				'GetActiveSessionTimes' => [
										'method' => 'GET',
										'endpoint' => $this->endpointAppointment . '/activesessiontimes',
										'headers' => $this->headersBasic
									 ],
				'GetAppointmentOptions' => [
										'method' => 'GET',
										'endpoint' => $this->endpointAppointment . '/appointmentoptions',
										'headers' => $this->headersBasic
									 ],
				'GetBookableItems' => [
										'method' => 'GET',
										'endpoint' => $this->endpointAppointment . '/bookableitems',
										'headers' => $this->headersBasic
									 ],
				'GetScheduleItems' => [
										'method' => 'GET',
										'endpoint' => $this->endpointAppointment . '/scheduleitems',
										'headers' => $this->headersBasic
									 ],
				'GetStaffAppointments' => [
										'method' => 'GET',
										'endpoint' => $this->endpointAppointment . '/staffappointments',
										'headers' => $this->headersBasic
									 ],
				'UpdateApppointment' => [
										'method' => 'POST',
										'endpoint' => $this->endpointAppointment . '/updateappointment',
										'headers' => $this->headersBasic
									 ]
			],
			'ClassService' => [
				'AddClientToClass' => [
										'method' => 'POST',
										'endpoint' => $this->endpointClasses . '/addclienttoclass',
										'headers' => $this->headersBasic
									 ],
				'GetClassDescriptions' => [
										'method' => 'GET',
										'endpoint' => $this->endpointClasses . '/classdescriptions',
										'headers' => $this->headersBasic
									 ],
				'GetClasses' => [
										'method' => 'GET',
										'endpoint' => $this->endpointClasses . '/classes',
										'headers' => $this->headersBasic
									 ]
			],
			'UserToken' => [
				'TokenIssue' => [
										'method' => 'POST',
										'endpoint' => $this->endpointUserToken . '/issue',
										'headers' => $this->headersBasic
									 ],
				'TokenRevoke' => [
										'method' => 'DELETE',
										'endpoint' => $this->endpointUserToken . '/revoke',
										'headers' => $this->headersBasic
									 ]
			]
		];
			
			/*
			
			classGetClassVisits	GET /public/v{version}/class/classvisits	Get information about clients booked in a class.
			classGetClasses	GET /public/v{version}/class/classes	Get scheduled classes.
			classGetWaitlistEntries	GET /public/v{version}/class/waitlistentries	Get waiting list entries.
			classRemoveClientFromClass	POST /public/v{version}/class/removeclientfromclass	Remove a client from a class.
			classRemoveFromWaitlist	POST /public/v{version}/class/removefromwaitlist	Remove a client from a waiting list.
			classSubstituteClassTeacher	POST /public/v{version}/class/substituteclassteacher	Substitute a class teacher.
		];
		*/			
	}
	
	/**
	* magic method will search $this->apiMethods array for $name and call the
	* appropriate Mindbody API method if found
	*/
	public function __call($name, $arguments) {
	
		$restMethod = [];
		
		foreach($this->apiMethods as $apiServiceName => $apiMethods) {
			if ( array_key_exists( $name, $apiMethods ) ) {
				$restMethod = $apiMethods[$name];
			}
		}

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
		
		$response = wp_remote_post( $restMethod['endpoint'], 
			array(
				'method' => $restMethod['method'],
				'timeout' => 45,
				'httpversion' => '1.0',
				'blocking' => true,
				'headers' => $restMethod['headers'],
				'body' => json_encode(array( 'Username' => '_' . $this->extraCredentials['SourceName'], 'Password' => $this->extraCredentials['Password'] )),
				'cookies' => array()
			) );

		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			return "Something went wrong: " . $error_message;
		} else {
			return json_decode($response['body']);	
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

	public function replace_empty_arrays_with_nulls(array $array) {
		foreach($array as &$value) {
			if(is_array($value)) {
				if(empty($value)) {
					$value = null;
				} else {
					$value = $this->replace_empty_arrays_with_nulls($value);
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
        $passed = func_get_args();
        $request = empty($passed[0]) ? null : $passed[0];
        $returnObject = empty($passed[1]) ? null : $passed[1];
        $debugErrors = empty($passed[2]) ? null : $passed[2];
        // NS\MZMBO()->helpers->mz_pr("request");
        // NS\MZMBO()->helpers->mz_pr($request);
        $data = $this->callMindbodyService('DataService', 'FunctionDataXml', $request);
        $xmlString = $this->getXMLResponse();
        // NS\MZMBO()->helpers->mz_pr("xmlString");
        // NS\MZMBO()->helpers->mz_pr($xmlString);
        $sxe = new \SimpleXMLElement($xmlString);
        $sxe->registerXPathNamespace("mindbody", "http://clients.mindbodyonline.com/api/0_5");
        $res = $sxe->xpath("//mindbody:FunctionDataXmlResponse");
        if($returnObject) {
            return $res[0];
        } else {
            $arr = $this->replace_empty_arrays_with_nulls(json_decode(json_encode($res[0]),1));
            if(isset($arr['FunctionDataXmlResult']['Results']['Row']) && is_array($arr['FunctionDataXmlResult']['Results']['Row'])) {
                $arr['FunctionDataXmlResult']['Results']['Row'] = $this->makeNumericArray($arr['FunctionDataXmlResult']['Results']['Row']);
            }
            return $arr;
        }
    }

}

?>
