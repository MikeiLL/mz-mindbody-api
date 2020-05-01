<?php
namespace MZ_Mindbody\Inc\Libraries;

use MZ_Mindbody as NS;
use MZ_Mindbody\Inc\Common as Common;

class MBO_V6_API {
	
	protected $endpointClasses = 'https://api.mindbodyonline.com/public/v6/class';
	protected $endpointAppointment = 'https://api.mindbodyonline.com/public/v6/appointment';
	protected $endpointClient = 'https://api.mindbodyonline.com/public/v6/client';
	protected $endpointEnrollment = 'https://api.mindbodyonline.com/public/v6/enrollment';
	protected $endpointPayroll = 'https://api.mindbodyonline.com/public/v6/payroll';
	protected $endpointSale = 'https://api.mindbodyonline.com/public/v6/sale';
	protected $endpointSite = 'https://api.mindbodyonline.com/public/v6/site';
	protected $endpointStaff = 'https://api.mindbodyonline.com/public/v6/staff';
	protected $endpointUserToken = 'https://api.mindbodyonline.com/public/v6/usertoken';
	private $tokenRequestTries = 6;
	
	protected $headersBasic = [
		'Content-Type' => 'application/json',
		'Api-Key' => '',
		'SiteId' => '-99'
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
		// source: https://github.com/mindbody/API-Examples/tree/master/SDKs/PHP/SwaggerClient-php/docs/Api
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
				'classGetClassVisits' => [
										'method' => 'GET',
										'endpoint' => $this->endpointClasses . '/classvisits',
										'headers' => $this->headersBasic
									 ],
				'GetClasses' => [
										'method' => 'GET',
										'endpoint' => $this->endpointClasses . '/classes',
										'headers' => $this->headersBasic
									 ],
				'GetWaitlistEntries' => [
										'method' => 'GET',
										'endpoint' => $this->endpointClasses . '/waitlistentries',
										'headers' => $this->headersBasic
									 ],
				'RemoveClientFromClass' => [
										'method' => 'POST',
										'endpoint' => $this->endpointClasses . '/removeclientfromclass',
										'headers' => $this->headersBasic
									 ],
				'RemoveFromWaitlist' => [
										'method' => 'POST',
										'endpoint' => $this->endpointClasses . '/removefromwaitlist',
										'headers' => $this->headersBasic
									 ],
				'SubstituteClassTeacher' => [
										'method' => 'POST',
										'endpoint' => $this->endpointClasses . '/substituteclassteacher',
										'headers' => $this->headersBasic
									 ]
			],
			'ClientApi' => [
				'AddArrival' => [
										'method' => 'POST',
										'endpoint' => $this->endpointClient . '/addarrival',
										'headers' => $this->headersBasic
									 ],
				'AddClient' => [
										'method' => 'POST',
										'endpoint' => $this->endpointClient . '/addclient',
										'headers' => $this->headersBasic
									 ],
				'AddContactLog' => [
										'method' => 'POST',
										'endpoint' => $this->endpointClient . '/addcontactlog',
										'headers' => $this->headersBasic
									 ],
				'GetActiveClientMemberships' => [
										'method' => 'GET',
										'endpoint' => $this->endpointClient . '/activeclientmemberships',
										'headers' => $this->headersBasic
									 ],
				'GetClientAccountBalances' => [
										'method' => 'GET',
										'endpoint' => $this->endpointClient . '/clientaccountbalances',
										'headers' => $this->headersBasic
									 ],
				'GetClientContracts' => [
										'method' => 'GET',
										'endpoint' => $this->endpointClient . '/clientcontracts',
										'headers' => $this->headersBasic
									 ],
				'GetClientFormulaNotes' => [
										'method' => 'GET',
										'endpoint' => $this->endpointClient . '/clientformulanotes',
										'headers' => $this->headersBasic
									 ],
				'GetClientIndexes' => [
										'method' => 'GET',
										'endpoint' => $this->endpointClient . '/clientindexes',
										'headers' => $this->headersBasic
									 ],
				'GetClientPurchases' => [
										'method' => 'GET',
										'endpoint' => $this->endpointClient . '/clientpurchases',
										'headers' => $this->headersBasic
									 ],
				'GetClientReferralTypes' => [
										'method' => 'GET',
										'endpoint' => $this->endpointClient . '/clientreferraltypes',
										'headers' => $this->headersBasic
									 ],
				'GetClientServices' => [
										'method' => 'GET',
										'endpoint' => $this->endpointClient . '/clientservices',
										'headers' => $this->headersBasic
									 ],
				'GetClientVisits' => [
										'method' => 'GET',
										'endpoint' => $this->endpointClient . '/clientvisits',
										'headers' => $this->headersBasic
									 ],
				'GetClients' => [
										'method' => 'GET',
										'endpoint' => $this->endpointClient . '/clients',
										'headers' => $this->headersBasic
									 ],
				'GetContactLogs' => [
										'method' => 'GET',
										'endpoint' => $this->endpointClient . '/contactlogs',
										'headers' => $this->headersBasic
									 ],
				'GetCrossRegionalClientAssociations' => [
										'method' => 'GET',
										'endpoint' => $this->endpointClient . '/crossregionalclientassociations',
										'headers' => $this->headersBasic
									 ],
				'GetCustomClientFields' => [
										'method' => 'GET',
										'endpoint' => $this->endpointClient . '/customclientfields',
										'headers' => $this->headersBasic
									 ],
				'GetRequiredClientFields' => [
										'method' => 'GET',
										'endpoint' => $this->endpointClient . '/requiredclientfields',
										'headers' => $this->headersBasic
									 ],
				'SendPasswordResetEmail' => [
										'method' => 'POST',
										'endpoint' => $this->endpointClient . '/sendpasswordresetemail',
										'headers' => $this->headersBasic
									 ],
				'UpdateClient' => [
										'method' => 'POST',
										'endpoint' => $this->endpointClient . '/updateclient',
										'headers' => $this->headersBasic
									 ],
				'UpdateClientService' => [
										'method' => 'POST',
										'endpoint' => $this->endpointClient . '/updateclientservice',
										'headers' => $this->headersBasic
									 ],
				'UpdateClientVisit' => [
										'method' => 'POST',
										'endpoint' => $this->endpointClient . '/updateclientvisit',
										'headers' => $this->headersBasic
									 ],
				'UpdateContactLog' => [
										'method' => 'POST',
										'endpoint' => $this->endpointClient . '/updatecontactlog',
										'headers' => $this->headersBasic
									 ],
				'UploadClientDocument' => [
										'method' => 'POST',
										'endpoint' => $this->endpointClient . '/uploadclientdocument',
										'headers' => $this->headersBasic
									 ],
				'UploadClientPhoto' => [
										'method' => 'POST',
										'endpoint' => $this->endpointClient . '/uploadclientphoto',
										'headers' => $this->headersBasic
									 ]
			],
			'EnrollmentApi' => [
				'AddClientToEnrollment' => [
										'method' => 'POST',
										'endpoint' => $this->endpointEnrollment . '/addclienttoenrollment',
										'headers' => $this->headersBasic
									 ],
				'GetEnrollments' => [
										'method' => 'GET',
										'endpoint' => $this->endpointEnrollment . '/enrollments',
										'headers' => $this->headersBasic
									 ]
			],
			'PayrollApi' => [
				'GetClassPayroll' => [
										'method' => 'GET',
										'endpoint' => $this->endpointPayroll . '/classes',
										'headers' => $this->headersBasic
									 ],
				'GetTimeClock' => [
										'method' => 'GET',
										'endpoint' => $this->endpointPayroll . '/timeclock',
										'headers' => $this->headersBasic
									 ]
			],
			'SaleApi' => [
				'CheckoutShoppingCart' => [
										'method' => 'POST',
										'endpoint' => $this->endpointSale . '/checkoutshoppingcart',
										'headers' => $this->headersBasic
									 ],
				'GetAcceptedCardTypes' => [
										'method' => 'GET',
										'endpoint' => $this->endpointSale . '/acceptedcardtypes',
										'headers' => $this->headersBasic
									 ],
				'GetContracts' => [
										'method' => 'GET',
										'endpoint' => $this->endpointSale . '/contracts',
										'headers' => $this->headersBasic
									 ],
				'GetCustomPaymentMethods' => [
										'method' => 'GET',
										'endpoint' => $this->endpointSale . '/custompaymentmethods',
										'headers' => $this->headersBasic
									 ],
				'GetGiftCards' => [
										'method' => 'GET',
										'endpoint' => $this->endpointSale . '/giftcards',
										'headers' => $this->headersBasic
									 ],
				'GetProducts' => [
										'method' => 'GET',
										'endpoint' => $this->endpointSale . '/products',
										'headers' => $this->headersBasic
									 ],
				'GetSales' => [
										'method' => 'GET',
										'endpoint' => $this->endpointSale . '/sales',
										'headers' => $this->headersBasic
									 ],
				'GetServices' => [
										'method' => 'GET',
										'endpoint' => $this->endpointSale . '/services',
										'headers' => $this->headersBasic
									 ],
				'PurchaseContract' => [
										'method' => 'POST',
										'endpoint' => $this->endpointSale . '/purchasecontract',
										'headers' => $this->headersBasic
									 ],
				'PurchaseGiftCard' => [
										'method' => 'POST',
										'endpoint' => $this->endpointSale . '/purchasegiftcard',
										'headers' => $this->headersBasic
									 ]
			],
			'SiteApi' => [
				'GetActivationCode' => [
										'method' => 'GET',
										'endpoint' => $this->endpointPayroll . '/activationcode',
										'headers' => $this->headersBasic
									 ],
				'GetLocations' => [
										'method' => 'GET',
										'endpoint' => $this->endpointPayroll . '/locations',
										'headers' => $this->headersBasic
									 ],
				'GetPrograms' => [
										'method' => 'GET',
										'endpoint' => $this->endpointPayroll . '/programs',
										'headers' => $this->headersBasic
									 ],
				'GetResources' => [
										'method' => 'GET',
										'endpoint' => $this->endpointPayroll . '/resources',
										'headers' => $this->headersBasic
									 ],
				'GetSessionTypes' => [
										'method' => 'GET',
										'endpoint' => $this->endpointPayroll . '/sessiontypes',
										'headers' => $this->headersBasic
									 ],
				'GetSites' => [
										'method' => 'GET',
										'endpoint' => $this->endpointPayroll . '/sites',
										'headers' => $this->headersBasic
									 ]
			],
			'StaffApi' => [
				'GetStaff' => [
										'method' => 'GET',
										'endpoint' => $this->endpointStaff . '/staff',
										'headers' => $this->headersBasic
									 ],
				'GetStaffPermissions' => [
										'method' => 'GET',
										'endpoint' => $this->endpointStaff . '/staffpermissions',
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
		
		*/			
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
		
		if ($restMethod['endpoint'] == 'https://api.mindbodyonline.com/public/v6/usertoken/issue') {
			// If this is a token request, make a separate call so that we 
			// don't create a loop here
			
			$token = $this->tokenRequest( $restMethod );
			
			return $token;
		} 
		
		$request_body = array_merge( $requestData,
						array( 
								'Username' => '_' . $this->extraCredentials['SourceName'], 
								'Password' => $this->extraCredentials['Password'] 
						)
					);
					
		$request_body['Access'] = $this->tokenRequest( $restMethod );
		
		print_r('%%%% got a tokenRequest: %%%%');
		print_r($request_body);
		
		$response = wp_remote_post( $restMethod['endpoint'], 
			array(
				'method' => $restMethod['method'],
				'timeout' => 45,
				'httpversion' => '1.0',
				'blocking' => true,
				'headers' => $restMethod['headers'],
				'body' => json_encode($request_body),
				'cookies' => array()
			) );

		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			return "Something went wrong: " . $error_message;
		} else {
			if ($response['response']['code'] != 200) {
				// There may be a reason to do this at some point
				return json_decode($response['body']);
			} else {
				return json_decode($response['body']);	
			}
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
					// OK try again
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
