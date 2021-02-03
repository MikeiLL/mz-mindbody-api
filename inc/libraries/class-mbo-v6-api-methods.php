<?php
namespace MZ_Mindbody\Inc\Libraries;

use MZ_Mindbody as MZ;

class MBO_V6_API_METHODS { 
	// set apiServices array with Mindbody endpoints
	// source: https://github.com/mindbody/API-Examples/tree/master/SDKs/PHP/SwaggerClient-php/docs/Api
	
	public  $methods;
	private $headersBasic = array();
	private $headersAuthorized = array();
	
	protected $endpointClasses = 'https://api.mindbodyonline.com/public/v6/class';
	protected $endpointAppointment = 'https://api.mindbodyonline.com/public/v6/appointment';
	protected $endpointClient = 'https://api.mindbodyonline.com/public/v6/client';
	protected $endpointEnrollment = 'https://api.mindbodyonline.com/public/v6/enrollment';
	protected $endpointPayroll = 'https://api.mindbodyonline.com/public/v6/payroll';
	protected $endpointSale = 'https://api.mindbodyonline.com/public/v6/sale';
	protected $endpointSite = 'https://api.mindbodyonline.com/public/v6/site';
	protected $endpointStaff = 'https://api.mindbodyonline.com/public/v6/staff';
	protected $endpointUserToken = 'https://api.mindbodyonline.com/public/v6/usertoken';
	
	/**
	* Initialize the apiServices and apiMethods arrays
	*
	* For calls which require higher levels of authorization, use headerAuthorized.
	*/
	public function __construct( $headers = array() ) {
	
		$this->headersBasic = $headers;
		
		$this->headersAuthorized = array_merge($headers, [
		                                        'Authorization' => get_option('mz_mbo_token', ['stored_time' => '', 'AccessToken' => ''])['AccessToken']
		                                        ]);
		
		$this->methods = [
			'AppointmentService' => [
				'AddApppointment' => [
										'method' => 'POST',
										'name' => 'AddApppointment', 
										'endpoint' => $this->endpointAppointment . '/addappointment',
										'headers' => $this->headersBasic
									 ],
				'GetActiveSessionTimes' => [
										'method' => 'GET',
										'name' => 'GetActiveSessionTimes', 
										'endpoint' => $this->endpointAppointment . '/activesessiontimes',
										'headers' => $this->headersBasic
									 ],
				'GetAppointmentOptions' => [
										'method' => 'GET',
										'name' => 'GetAppointmentOptions', 
										'endpoint' => $this->endpointAppointment . '/appointmentoptions',
										'headers' => $this->headersBasic
									 ],
				'GetBookableItems' => [
										'method' => 'GET',
										'name' => 'GetBookableItems', 
										'endpoint' => $this->endpointAppointment . '/bookableitems',
										'headers' => $this->headersBasic
									 ],
				'GetScheduleItems' => [
										'method' => 'GET',
										'name' => 'GetScheduleItems', 
										'endpoint' => $this->endpointAppointment . '/scheduleitems',
										'headers' => $this->headersBasic
									 ],
				'GetStaffAppointments' => [
										'method' => 'GET',
										'name' => 'GetStaffAppointments', 
										'endpoint' => $this->endpointAppointment . '/staffappointments',
										'headers' => $this->headersBasic
									 ],
				'UpdateApppointment' => [
										'method' => 'POST',
										'name' => 'UpdateApppointment', 
										'endpoint' => $this->endpointAppointment . '/updateappointment',
										'headers' => $this->headersBasic
									 ]
			],
			'ClassService' => [
				'AddClientToClass' => [
										'method' => 'POST',
										'name' => 'AddClientToClass', 
										'endpoint' => $this->endpointClasses . '/addclienttoclass',
										'headers' => $this->headersBasic
									 ],
				'GetClassDescriptions' => [
										'method' => 'GET',
										'name' => 'GetClassDescriptions', 
										'endpoint' => $this->endpointClasses . '/classdescriptions',
										'headers' => $this->headersBasic
									 ],
				'GetClassVisits' => [
										'method' => 'GET',
										'name' => 'GetClassVisits', 
										'endpoint' => $this->endpointClasses . '/classvisits',
										'headers' => $this->headersBasic
									 ],
				'GetClasses' => [
										'method' => 'GET',
										'name' => 'GetClasses', 
										'endpoint' => $this->endpointClasses . '/classes',
										'headers' => $this->headersBasic
									 ],
				'GetWaitlistEntries' => [
										'method' => 'GET',
										'name' => 'GetWaitlistEntries', 
										'endpoint' => $this->endpointClasses . '/waitlistentries',
										'headers' => $this->headersBasic
									 ],
				'RemoveClientFromClass' => [
										'method' => 'POST',
										'name' => 'RemoveClientFromClass', 
										'endpoint' => $this->endpointClasses . '/removeclientfromclass',
										'headers' => $this->headersBasic
									 ],
				'RemoveFromWaitlist' => [
										'method' => 'POST',
										'name' => 'RemoveFromWaitlist', 
										'endpoint' => $this->endpointClasses . '/removefromwaitlist',
										'headers' => $this->headersBasic
									 ],
				'SubstituteClassTeacher' => [
										'method' => 'POST',
										'name' => 'SubstituteClassTeacher', 
										'endpoint' => $this->endpointClasses . '/substituteclassteacher',
										'headers' => $this->headersBasic
									 ]
			],
			'ClientApi' => [
				'AddArrival' => [
										'method' => 'POST',
										'name' => 'AddArrival', 
										'endpoint' => $this->endpointClient . '/addarrival',
										'headers' => $this->headersBasic
									 ],
				'AddClient' => [
										'method' => 'POST',
										'name' => 'AddClient', 
										'endpoint' => $this->endpointClient . '/addclient',
										'headers' => $this->headersBasic
									 ],
				'AddContactLog' => [
										'method' => 'POST',
										'name' => 'AddContactLog', 
										'endpoint' => $this->endpointClient . '/addcontactlog',
										'headers' => $this->headersBasic
									 ],
				'GetActiveClientMemberships' => [
										'method' => 'GET',
										'name' => 'GetActiveClientMemberships', 
										'endpoint' => $this->endpointClient . '/activeclientmemberships',
										'headers' => $this->headersBasic
									 ],
				'GetClientAccountBalances' => [
										'method' => 'GET',
										'name' => 'GetClientAccountBalances', 
										'endpoint' => $this->endpointClient . '/clientaccountbalances',
										'headers' => $this->headersBasic
									 ],
				'GetClientContracts' => [
										'method' => 'GET',
										'name' => 'GetClientContracts', 
										'endpoint' => $this->endpointClient . '/clientcontracts',
										'headers' => $this->headersBasic
									 ],
				'GetClientFormulaNotes' => [
										'method' => 'GET',
										'name' => 'GetClientFormulaNotes', 
										'endpoint' => $this->endpointClient . '/clientformulanotes',
										'headers' => $this->headersBasic
									 ],
				'GetClientIndexes' => [
										'method' => 'GET',
										'name' => 'GetClientIndexes', 
										'endpoint' => $this->endpointClient . '/clientindexes',
										'headers' => $this->headersBasic
									 ],
				'GetClientPurchases' => [
										'method' => 'GET',
										'name' => 'GetClientPurchases', 
										'endpoint' => $this->endpointClient . '/clientpurchases',
										'headers' => $this->headersBasic
									 ],
				'GetClientReferralTypes' => [
										'method' => 'GET',
										'name' => 'GetClientReferralTypes', 
										'endpoint' => $this->endpointClient . '/clientreferraltypes',
										'headers' => $this->headersBasic
									 ],
				'GetClientServices' => [
										'method' => 'GET',
										'name' => 'GetClientServices', 
										'endpoint' => $this->endpointClient . '/clientservices',
										'headers' => $this->headersBasic
									 ],
				'GetClientVisits' => [
										'method' => 'GET',
										'name' => 'GetClientVisits', 
										'endpoint' => $this->endpointClient . '/clientvisits',
										'headers' => $this->headersBasic
									 ],
				'GetClients' => [
										'method' => 'GET',
										'name' => 'GetClients', 
										'endpoint' => $this->endpointClient . '/clients',
										'headers' => $this->headersBasic
									 ],
				'GetContactLogs' => [
										'method' => 'GET',
										'name' => 'GetContactLogs', 
										'endpoint' => $this->endpointClient . '/contactlogs',
										'headers' => $this->headersBasic
									 ],
				'GetCrossRegionalClientAssociations' => [
										'method' => 'GET',
										'name' => 'GetCrossRegionalClientAssociations', 
										'endpoint' => $this->endpointClient . '/crossregionalclientassociations',
										'headers' => $this->headersBasic
									 ],
				'GetCustomClientFields' => [
										'method' => 'GET',
										'name' => 'GetCustomClientFields', 
										'endpoint' => $this->endpointClient . '/customclientfields',
										'headers' => $this->headersBasic
									 ],
				'GetRequiredClientFields' => [
										'method' => 'GET',
										'name' => 'GetRequiredClientFields', 
										'endpoint' => $this->endpointClient . '/requiredclientfields',
										'headers' => $this->headersBasic
									 ],
				'SendPasswordResetEmail' => [
										'method' => 'POST',
										'name' => 'SendPasswordResetEmail', 
										'endpoint' => $this->endpointClient . '/sendpasswordresetemail',
										'headers' => $this->headersBasic
									 ],
				'UpdateClient' => [
										'method' => 'POST',
										'name' => 'UpdateClient', 
										'endpoint' => $this->endpointClient . '/updateclient',
										'headers' => $this->headersBasic
									 ],
				'UpdateClientService' => [
										'method' => 'POST',
										'name' => 'UpdateClientService', 
										'endpoint' => $this->endpointClient . '/updateclientservice',
										'headers' => $this->headersBasic
									 ],
				'UpdateClientVisit' => [
										'method' => 'POST',
										'name' => 'UpdateClientVisit', 
										'endpoint' => $this->endpointClient . '/updateclientvisit',
										'headers' => $this->headersBasic
									 ],
				'UpdateContactLog' => [
										'method' => 'POST',
										'name' => 'UpdateContactLog', 
										'endpoint' => $this->endpointClient . '/updatecontactlog',
										'headers' => $this->headersBasic
									 ],
				'UploadClientDocument' => [
										'method' => 'POST',
										'name' => 'UploadClientDocument', 
										'endpoint' => $this->endpointClient . '/uploadclientdocument',
										'headers' => $this->headersBasic
									 ],
				'UploadClientPhoto' => [
										'method' => 'POST',
										'name' => 'UploadClientPhoto', 
										'endpoint' => $this->endpointClient . '/uploadclientphoto',
										'headers' => $this->headersBasic
									 ]
			],
			'EnrollmentApi' => [
				'AddClientToEnrollment' => [
										'method' => 'POST',
										'name' => 'AddClientToEnrollment', 
										'endpoint' => $this->endpointEnrollment . '/addclienttoenrollment',
										'headers' => $this->headersBasic
									 ],
				'GetEnrollments' => [
										'method' => 'GET',
										'name' => 'GetEnrollments', 
										'endpoint' => $this->endpointEnrollment . '/enrollments',
										'headers' => $this->headersBasic
									 ]
			],
			'PayrollApi' => [
				'GetClassPayroll' => [
										'method' => 'GET',
										'name' => 'GetClassPayroll', 
										'endpoint' => $this->endpointPayroll . '/classes',
										'headers' => $this->headersBasic
									 ],
				'GetTimeClock' => [
										'method' => 'GET',
										'name' => 'GetTimeClock', 
										'endpoint' => $this->endpointPayroll . '/timeclock',
										'headers' => $this->headersBasic
									 ]
			],
			'SaleApi' => [
				'CheckoutShoppingCart' => [
										'method' => 'POST',
										'name' => 'CheckoutShoppingCart', 
										'endpoint' => $this->endpointSale . '/checkoutshoppingcart',
										'headers' => $this->headersAuthorized
									 ],
				'GetAcceptedCardTypes' => [
										'method' => 'GET',
										'name' => 'GetAcceptedCardTypes', 
										'endpoint' => $this->endpointSale . '/acceptedcardtypes',
										'headers' => $this->headersBasic
									 ],
				'GetContracts' => [
										'method' => 'GET',
										'name' => 'GetContracts', 
										'endpoint' => $this->endpointSale . '/contracts',
										'headers' => $this->headersAuthorized
									 ],
				'GetCustomPaymentMethods' => [
										'method' => 'GET',
										'name' => 'GetCustomPaymentMethods', 
										'endpoint' => $this->endpointSale . '/custompaymentmethods',
										'headers' => $this->headersBasic
									 ],
				'GetGiftCards' => [
										'method' => 'GET',
										'name' => 'GetGiftCards', 
										'endpoint' => $this->endpointSale . '/giftcards',
										'headers' => $this->headersBasic
									 ],
				'GetProducts' => [
										'method' => 'GET',
										'name' => 'GetProducts', 
										'endpoint' => $this->endpointSale . '/products',
										'headers' => $this->headersBasic
									 ],
				'GetSales' => [
										'method' => 'GET',
										'name' => 'GetSales', 
										'endpoint' => $this->endpointSale . '/sales',
										'headers' => $this->headersBasic
									 ],
				'GetServices' => [
										'method' => 'GET',
										'name' => 'GetServices', 
										'endpoint' => $this->endpointSale . '/services',
										'headers' => $this->headersBasic
									 ],
				'PurchaseContract' => [
										'method' => 'POST',
										'name' => 'PurchaseContract', 
										'endpoint' => $this->endpointSale . '/purchasecontract',
										'headers' => $this->headersBasic
									 ],
				'PurchaseGiftCard' => [
										'method' => 'POST',
										'name' => 'PurchaseGiftCard', 
										'endpoint' => $this->endpointSale . '/purchasegiftcard',
										'headers' => $this->headersBasic
									 ]
			],
			'SiteApi' => [
				'GetActivationCode' => [
										'method' => 'GET',
										'name' => 'GetActivationCode', 
										'endpoint' => $this->endpointSite . '/activationcode',
										'headers' => $this->headersBasic
									 ],
				'GetLocations' => [
										'method' => 'GET',
										'name' => 'GetLocations', 
										'endpoint' => $this->endpointSite . '/locations',
										'headers' => $this->headersBasic
									 ],
				'GetGenders' => [
										'method' => 'GET',
										'name' => 'GetGenders', 
										'endpoint' => $this->endpointSite . '/genders',
										'headers' => $this->headersBasic
									 ],
				'GetMemberships' => [
										'method' => 'GET',
										'name' => 'GetMemberships', 
										'endpoint' => $this->endpointSite . '/memberships',
										'headers' => $this->headersBasic
									 ],
				'GetPrograms' => [
										'method' => 'GET',
										'name' => 'GetPrograms', 
										'endpoint' => $this->endpointSite . '/programs',
										'headers' => $this->headersBasic
									 ],
				'GetResources' => [
										'method' => 'GET',
										'name' => 'GetResources', 
										'endpoint' => $this->endpointSite . '/resources',
										'headers' => $this->headersBasic
									 ],
				'GetSessionTypes' => [
										'method' => 'GET',
										'name' => 'GetSessionTypes', 
										'endpoint' => $this->endpointSite . '/sessiontypes',
										'headers' => $this->headersBasic
									 ],
				'GetSites' => [
										'method' => 'GET',
										'name' => 'GetSites', 
										'endpoint' => $this->endpointSite . '/sites',
										'headers' => $this->headersBasic
									 ]
			],
			'StaffApi' => [
				'GetStaff' => [
										'method' => 'GET',
										'name' => 'GetStaff', 
										'endpoint' => $this->endpointStaff . '/staff',
										'headers' => $this->headersBasic
									 ],
				'GetStaffPermissions' => [
										'method' => 'GET',
										'name' => 'GetStaffPermissions', 
										'endpoint' => $this->endpointStaff . '/staffpermissions',
										'headers' => $this->headersBasic
									 ]
			],
			'UserToken' => [
				'TokenIssue' => [
										'method' => 'POST',
										'name' => 'TokenIssue', 
										'endpoint' => $this->endpointUserToken . '/issue',
										'headers' => $this->headersBasic
									 ],
				'TokenRevoke' => [
										'method' => 'DELETE',
										'name' => 'TokenRevoke', 
										'endpoint' => $this->endpointUserToken . '/revoke',
										'headers' => $this->headersBasic
									 ]
			]
		];
	}
}
?>