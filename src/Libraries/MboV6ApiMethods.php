<?php
/**
 * Mindbody V6 API Methods
 *
 * This file contains the class which tracks the
 * MBO Api v6 methods, called upon by MboV6Api class.
 *
 * @package MzMindbody
 */

namespace MZoo\MzMindbody\Libraries;

use MZoo\MzMindbody as MZ;

/**
 * Mindbody V6 API Methods Class
 *
 * This class exposes the various MBO v6 endpoints
 * and thier methods.
 */
class MboV6ApiMethods {

	/**
	 * Api Services
	 *
	 * Array of Mindbody endpoints.
	 *
	 * source: https://github.com/mindbody/API-Examples/tree/master/SDKs/PHP/SwaggerClient-php/docs/Api.
	 *
	 * @access public
	 * @var array $methods the methods available within each endpoint.
	 */
	public $methods;

	/**
	 * Headers Basic
	 *
	 * @access private
	 * @var array $headers_basic basic, default headers for API call.
	 */
	private $headers_basic = array();

	/**
	 * Api Services array with Mindbody endpoints.
	 *
	 * @access private
	 * @var array $headers_authorized some methods require auth headers.
	 */
	private $headers_authorized = array();

	/**
	 * Endpoint Classes
	 *
	 * @access protected
	 * @var string $endpoint_classes MBO API Endpoint.
	 */
	protected $endpoint_classes = 'https://api.mindbodyonline.com/public/v6/class';

	/**
	 * Endpoint Appointment
	 *
	 * @access protected
	 * @var string $endpoint_appointment MBO API Endpoint.
	 */
	protected $endpoint_appointment = 'https://api.mindbodyonline.com/public/v6/appointment';

	/**
	 * Endpoint Client
	 *
	 * @access protected
	 * @var string $endpoint_client MBO API Endpoint.
	 */
	protected $endpoint_client = 'https://api.mindbodyonline.com/public/v6/client';

	/**
	 * Endpoint Enrollment
	 *
	 * @access protected
	 * @var string $endpoint_enrollment MBO API Endpoint.
	 */
	protected $endpoint_enrollment = 'https://api.mindbodyonline.com/public/v6/enrollment';

	/**
	 * Endpoint Payroll
	 *
	 * @access protected
	 * @var string $endpoint_payroll MBO API Endpoint.
	 */
	protected $endpoint_payroll = 'https://api.mindbodyonline.com/public/v6/payroll';

	/**
	 * Endpoint Sale
	 *
	 * @access protected
	 * @var string $endpoint_sale MBO API Endpoint.
	 */
	protected $endpoint_sale = 'https://api.mindbodyonline.com/public/v6/sale';

	/**
	 * Endpoint Site
	 *
	 * @access protected
	 * @var string $endpoint_site MBO API Endpoint.
	 */
	protected $endpoint_site = 'https://api.mindbodyonline.com/public/v6/site';

	/**
	 * Endpoint Staff
	 *
	 * @access protected
	 * @var string $endpoint_staff MBO API Endpoint.
	 */
	protected $endpoint_staff = 'https://api.mindbodyonline.com/public/v6/staff';

	/**
	 * Endpoint User Token
	 *
	 * @access protected
	 * @var string $endpoint_user_token MBO API Endpoint.
	 */
	protected $endpoint_user_token = 'https://api.mindbodyonline.com/public/v6/usertoken';

	/**
	 * Initialize the apiServices and api_methods arrays
	 *
	 * For calls which require higher levels of authorization, use headerAuthorized.
	 *
	 * @param array $headers required for api call.
	 */
	public function __construct( $headers = array() ) {

		$this->headers_basic = $headers;

		$this->headers_authorized = array_merge(
			$headers,
			array(
				'Authorization' => get_option(
					'mz_mbo_token',
					array(
						'stored_time' => '',
						'AccessToken' => '',
					)
				)['AccessToken'],
			)
		);

		$this->methods = array(
			'AppointmentService' => array(
				'AddApppointment'       => array(
					'method'   => 'POST',
					'name'     => 'AddApppointment',
					'endpoint' => $this->endpoint_appointment . '/addappointment',
					'headers'  => $this->headers_basic,
				),
				'GetActiveSessionTimes' => array(
					'method'   => 'GET',
					'name'     => 'GetActiveSessionTimes',
					'endpoint' => $this->endpoint_appointment . '/activesessiontimes',
					'headers'  => $this->headers_basic,
				),
				'GetAppointmentOptions' => array(
					'method'   => 'GET',
					'name'     => 'GetAppointmentOptions',
					'endpoint' => $this->endpoint_appointment . '/appointmentoptions',
					'headers'  => $this->headers_basic,
				),
				'GetBookableItems'      => array(
					'method'   => 'GET',
					'name'     => 'GetBookableItems',
					'endpoint' => $this->endpoint_appointment . '/bookableitems',
					'headers'  => $this->headers_basic,
				),
				'GetScheduleItems'      => array(
					'method'   => 'GET',
					'name'     => 'GetScheduleItems',
					'endpoint' => $this->endpoint_appointment . '/scheduleitems',
					'headers'  => $this->headers_basic,
				),
				'GetStaffAppointments'  => array(
					'method'   => 'GET',
					'name'     => 'GetStaffAppointments',
					'endpoint' => $this->endpoint_appointment . '/staffappointments',
					'headers'  => $this->headers_basic,
				),
				'UpdateApppointment'    => array(
					'method'   => 'POST',
					'name'     => 'UpdateApppointment',
					'endpoint' => $this->endpoint_appointment . '/updateappointment',
					'headers'  => $this->headers_basic,
				),
			),
			'ClassService'       => array(
				'AddClientToClass'       => array(
					'method'   => 'POST',
					'name'     => 'AddClientToClass',
					'endpoint' => $this->endpoint_classes . '/addclienttoclass',
					'headers'  => $this->headers_basic,
				),
				'GetClassDescriptions'   => array(
					'method'   => 'GET',
					'name'     => 'GetClassDescriptions',
					'endpoint' => $this->endpoint_classes . '/classdescriptions',
					'headers'  => $this->headers_basic,
				),
				'GetClassVisits'         => array(
					'method'   => 'GET',
					'name'     => 'GetClassVisits',
					'endpoint' => $this->endpoint_classes . '/classvisits',
					'headers'  => $this->headers_basic,
				),
				'GetClasses'             => array(
					'method'   => 'GET',
					'name'     => 'GetClasses',
					'endpoint' => $this->endpoint_classes . '/classes',
					'headers'  => $this->headers_basic,
				),
				'GetWaitlistEntries'     => array(
					'method'   => 'GET',
					'name'     => 'GetWaitlistEntries',
					'endpoint' => $this->endpoint_classes . '/waitlistentries',
					'headers'  => $this->headers_basic,
				),
				'RemoveClientFromClass'  => array(
					'method'   => 'POST',
					'name'     => 'RemoveClientFromClass',
					'endpoint' => $this->endpoint_classes . '/removeclientfromclass',
					'headers'  => $this->headers_basic,
				),
				'RemoveFromWaitlist'     => array(
					'method'   => 'POST',
					'name'     => 'RemoveFromWaitlist',
					'endpoint' => $this->endpoint_classes . '/removefromwaitlist',
					'headers'  => $this->headers_basic,
				),
				'SubstituteClassTeacher' => array(
					'method'   => 'POST',
					'name'     => 'SubstituteClassTeacher',
					'endpoint' => $this->endpoint_classes . '/substituteclassteacher',
					'headers'  => $this->headers_basic,
				),
			),
			'ClientApi'          => array(
				'AddArrival'                         => array(
					'method'   => 'POST',
					'name'     => 'AddArrival',
					'endpoint' => $this->endpoint_client . '/addarrival',
					'headers'  => $this->headers_basic,
				),
				'AddClient'                          => array(
					'method'   => 'POST',
					'name'     => 'AddClient',
					'endpoint' => $this->endpoint_client . '/addclient',
					'headers'  => $this->headers_basic,
				),
				'AddContactLog'                      => array(
					'method'   => 'POST',
					'name'     => 'AddContactLog',
					'endpoint' => $this->endpoint_client . '/addcontactlog',
					'headers'  => $this->headers_basic,
				),
				'GetActiveClientMemberships'         => array(
					'method'   => 'GET',
					'name'     => 'GetActiveClientMemberships',
					'endpoint' => $this->endpoint_client . '/activeclientmemberships',
					'headers'  => $this->headers_basic,
				),
				'GetClientAccountBalances'           => array(
					'method'   => 'GET',
					'name'     => 'GetClientAccountBalances',
					'endpoint' => $this->endpoint_client . '/clientaccountbalances',
					'headers'  => $this->headers_basic,
				),
				'GetClientContracts'                 => array(
					'method'   => 'GET',
					'name'     => 'GetClientContracts',
					'endpoint' => $this->endpoint_client . '/clientcontracts',
					'headers'  => $this->headers_basic,
				),
				'GetClientFormulaNotes'              => array(
					'method'   => 'GET',
					'name'     => 'GetClientFormulaNotes',
					'endpoint' => $this->endpoint_client . '/clientformulanotes',
					'headers'  => $this->headers_basic,
				),
				'GetClientIndexes'                   => array(
					'method'   => 'GET',
					'name'     => 'GetClientIndexes',
					'endpoint' => $this->endpoint_client . '/clientindexes',
					'headers'  => $this->headers_basic,
				),
				'GetClientPurchases'                 => array(
					'method'   => 'GET',
					'name'     => 'GetClientPurchases',
					'endpoint' => $this->endpoint_client . '/clientpurchases',
					'headers'  => $this->headers_basic,
				),
				'GetClientReferralTypes'             => array(
					'method'   => 'GET',
					'name'     => 'GetClientReferralTypes',
					'endpoint' => $this->endpoint_client . '/clientreferraltypes',
					'headers'  => $this->headers_basic,
				),
				'GetClientServices'                  => array(
					'method'   => 'GET',
					'name'     => 'GetClientServices',
					'endpoint' => $this->endpoint_client . '/clientservices',
					'headers'  => $this->headers_basic,
				),
				'GetClientVisits'                    => array(
					'method'   => 'GET',
					'name'     => 'GetClientVisits',
					'endpoint' => $this->endpoint_client . '/clientvisits',
					'headers'  => $this->headers_basic,
				),
				'GetClients'                         => array(
					'method'   => 'GET',
					'name'     => 'GetClients',
					'endpoint' => $this->endpoint_client . '/clients',
					'headers'  => $this->headers_basic,
				),
				'GetContactLogs'                     => array(
					'method'   => 'GET',
					'name'     => 'GetContactLogs',
					'endpoint' => $this->endpoint_client . '/contactlogs',
					'headers'  => $this->headers_basic,
				),
				'GetCrossRegionalClientAssociations' => array(
					'method'   => 'GET',
					'name'     => 'GetCrossRegionalClientAssociations',
					'endpoint' => $this->endpoint_client . '/crossregionalclientassociations',
					'headers'  => $this->headers_basic,
				),
				'GetCustomClientFields'              => array(
					'method'   => 'GET',
					'name'     => 'GetCustomClientFields',
					'endpoint' => $this->endpoint_client . '/customclientfields',
					'headers'  => $this->headers_basic,
				),
				'GetRequiredClientFields'            => array(
					'method'   => 'GET',
					'name'     => 'GetRequiredClientFields',
					'endpoint' => $this->endpoint_client . '/requiredclientfields',
					'headers'  => $this->headers_basic,
				),
				'SendPasswordResetEmail'             => array(
					'method'   => 'POST',
					'name'     => 'SendPasswordResetEmail',
					'endpoint' => $this->endpoint_client . '/sendpasswordresetemail',
					'headers'  => $this->headers_basic,
				),
				'UpdateClient'                       => array(
					'method'   => 'POST',
					'name'     => 'UpdateClient',
					'endpoint' => $this->endpoint_client . '/updateclient',
					'headers'  => $this->headers_basic,
				),
				'UpdateClientService'                => array(
					'method'   => 'POST',
					'name'     => 'UpdateClientService',
					'endpoint' => $this->endpoint_client . '/updateclientservice',
					'headers'  => $this->headers_basic,
				),
				'UpdateClientVisit'                  => array(
					'method'   => 'POST',
					'name'     => 'UpdateClientVisit',
					'endpoint' => $this->endpoint_client . '/updateclientvisit',
					'headers'  => $this->headers_basic,
				),
				'UpdateContactLog'                   => array(
					'method'   => 'POST',
					'name'     => 'UpdateContactLog',
					'endpoint' => $this->endpoint_client . '/updatecontactlog',
					'headers'  => $this->headers_basic,
				),
				'UploadClientDocument'               => array(
					'method'   => 'POST',
					'name'     => 'UploadClientDocument',
					'endpoint' => $this->endpoint_client . '/uploadclientdocument',
					'headers'  => $this->headers_basic,
				),
				'UploadClientPhoto'                  => array(
					'method'   => 'POST',
					'name'     => 'UploadClientPhoto',
					'endpoint' => $this->endpoint_client . '/uploadclientphoto',
					'headers'  => $this->headers_basic,
				),
			),
			'EnrollmentApi'      => array(
				'AddClientToEnrollment' => array(
					'method'   => 'POST',
					'name'     => 'AddClientToEnrollment',
					'endpoint' => $this->endpoint_enrollment . '/addclienttoenrollment',
					'headers'  => $this->headers_basic,
				),
				'GetEnrollments'        => array(
					'method'   => 'GET',
					'name'     => 'GetEnrollments',
					'endpoint' => $this->endpoint_enrollment . '/enrollments',
					'headers'  => $this->headers_basic,
				),
			),
			'PayrollApi'         => array(
				'GetClassPayroll' => array(
					'method'   => 'GET',
					'name'     => 'GetClassPayroll',
					'endpoint' => $this->endpoint_payroll . '/classes',
					'headers'  => $this->headers_basic,
				),
				'GetTimeClock'    => array(
					'method'   => 'GET',
					'name'     => 'GetTimeClock',
					'endpoint' => $this->endpoint_payroll . '/timeclock',
					'headers'  => $this->headers_basic,
				),
			),
			'SaleApi'            => array(
				'CheckoutShoppingCart'    => array(
					'method'   => 'POST',
					'name'     => 'CheckoutShoppingCart',
					'endpoint' => $this->endpoint_sale . '/checkoutshoppingcart',
					'headers'  => $this->headers_authorized,
				),
				'GetAcceptedCardTypes'    => array(
					'method'   => 'GET',
					'name'     => 'GetAcceptedCardTypes',
					'endpoint' => $this->endpoint_sale . '/acceptedcardtypes',
					'headers'  => $this->headers_basic,
				),
				'GetContracts'            => array(
					'method'   => 'GET',
					'name'     => 'GetContracts',
					'endpoint' => $this->endpoint_sale . '/contracts',
					'headers'  => $this->headers_authorized,
				),
				'GetCustomPaymentMethods' => array(
					'method'   => 'GET',
					'name'     => 'GetCustomPaymentMethods',
					'endpoint' => $this->endpoint_sale . '/custompaymentmethods',
					'headers'  => $this->headers_basic,
				),
				'GetGiftCards'            => array(
					'method'   => 'GET',
					'name'     => 'GetGiftCards',
					'endpoint' => $this->endpoint_sale . '/giftcards',
					'headers'  => $this->headers_basic,
				),
				'GetProducts'             => array(
					'method'   => 'GET',
					'name'     => 'GetProducts',
					'endpoint' => $this->endpoint_sale . '/products',
					'headers'  => $this->headers_basic,
				),
				'GetSales'                => array(
					'method'   => 'GET',
					'name'     => 'GetSales',
					'endpoint' => $this->endpoint_sale . '/sales',
					'headers'  => $this->headers_basic,
				),
				'GetServices'             => array(
					'method'   => 'GET',
					'name'     => 'GetServices',
					'endpoint' => $this->endpoint_sale . '/services',
					'headers'  => $this->headers_basic,
				),
				'PurchaseContract'        => array(
					'method'   => 'POST',
					'name'     => 'PurchaseContract',
					'endpoint' => $this->endpoint_sale . '/purchasecontract',
					'headers'  => $this->headers_basic,
				),
				'PurchaseGiftCard'        => array(
					'method'   => 'POST',
					'name'     => 'PurchaseGiftCard',
					'endpoint' => $this->endpoint_sale . '/purchasegiftcard',
					'headers'  => $this->headers_basic,
				),
			),
			'SiteApi'            => array(
				'GetActivationCode' => array(
					'method'   => 'GET',
					'name'     => 'GetActivationCode',
					'endpoint' => $this->endpoint_site . '/activationcode',
					'headers'  => $this->headers_basic,
				),
				'GetLocations'      => array(
					'method'   => 'GET',
					'name'     => 'GetLocations',
					'endpoint' => $this->endpoint_site . '/locations',
					'headers'  => $this->headers_basic,
				),
				'GetGenders'        => array(
					'method'   => 'GET',
					'name'     => 'GetGenders',
					'endpoint' => $this->endpoint_site . '/genders',
					'headers'  => $this->headers_basic,
				),
				'GetMemberships'    => array(
					'method'   => 'GET',
					'name'     => 'GetMemberships',
					'endpoint' => $this->endpoint_site . '/memberships',
					'headers'  => $this->headers_basic,
				),
				'GetPrograms'       => array(
					'method'   => 'GET',
					'name'     => 'GetPrograms',
					'endpoint' => $this->endpoint_site . '/programs',
					'headers'  => $this->headers_basic,
				),
				'GetResources'      => array(
					'method'   => 'GET',
					'name'     => 'GetResources',
					'endpoint' => $this->endpoint_site . '/resources',
					'headers'  => $this->headers_basic,
				),
				'GetSessionTypes'   => array(
					'method'   => 'GET',
					'name'     => 'GetSessionTypes',
					'endpoint' => $this->endpoint_site . '/sessiontypes',
					'headers'  => $this->headers_basic,
				),
				'GetSites'          => array(
					'method'   => 'GET',
					'name'     => 'GetSites',
					'endpoint' => $this->endpoint_site . '/sites',
					'headers'  => $this->headers_basic,
				),
			),
			'StaffApi'           => array(
				'GetStaff'            => array(
					'method'   => 'GET',
					'name'     => 'GetStaff',
					'endpoint' => $this->endpoint_staff . '/staff',
					'headers'  => $this->headers_basic,
				),
				'GetStaffPermissions' => array(
					'method'   => 'GET',
					'name'     => 'GetStaffPermissions',
					'endpoint' => $this->endpoint_staff . '/staffpermissions',
					'headers'  => $this->headers_basic,
				),
			),
			'UserToken'          => array(
				'TokenIssue'  => array(
					'method'   => 'POST',
					'name'     => 'TokenIssue',
					'endpoint' => $this->endpoint_user_token . '/issue',
					'headers'  => $this->headers_basic,
				),
				'TokenRevoke' => array(
					'method'   => 'DELETE',
					'name'     => 'TokenRevoke',
					'endpoint' => $this->endpoint_user_token . '/revoke',
					'headers'  => $this->headers_basic,
				),
			),
		);
	}
}
