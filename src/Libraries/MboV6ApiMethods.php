<?php

namespace MZoo\MzMindbody\Libraries;

use MZoo\MzMindbody as MZ;

class MboV6ApiMethods
{

    // set apiServices array with Mindbody endpoints
    // source: https://github.com/mindbody/API-Examples/tree/master/SDKs/PHP/SwaggerClient-php/docs/Api

    public $methods;
    private $headersBasic      = array();
    private $headersAuthorized = array();

    protected $endpointClasses     = 'https://api.mindbodyonline.com/public/v6/class';
    protected $endpointAppointment = 'https://api.mindbodyonline.com/public/v6/appointment';
    protected $endpointClient      = 'https://api.mindbodyonline.com/public/v6/client';
    protected $endpointEnrollment  = 'https://api.mindbodyonline.com/public/v6/enrollment';
    protected $endpointPayroll     = 'https://api.mindbodyonline.com/public/v6/payroll';
    protected $endpointSale        = 'https://api.mindbodyonline.com/public/v6/sale';
    protected $endpointSite        = 'https://api.mindbodyonline.com/public/v6/site';
    protected $endpointStaff       = 'https://api.mindbodyonline.com/public/v6/staff';
    protected $endpointUserToken   = 'https://api.mindbodyonline.com/public/v6/usertoken';

    /**
     * Initialize the apiServices and apiMethods arrays
     *
     * For calls which require higher levels of authorization, use headerAuthorized.
     */
    public function __construct( $headers = array() )
    {

        $this->headersBasic = $headers;

        $this->headersAuthorized = array_merge(
            $headers,
            array(
            'Authorization' => get_option(
                'mz_mbo_token',
                array(
                'stored_time' => '',
                'AccessToken' => '',
                    )
            ),
            )
        );

        $this->methods = array(
        'AppointmentService' => array(
        'AddApppointment'       => array(
        'method'   => 'POST',
        'name'     => 'AddApppointment',
        'endpoint' => $this->endpointAppointment . '/addappointment',
        'headers'  => $this->headersBasic,
        ),
        'GetActiveSessionTimes' => array(
                    'method'   => 'GET',
                    'name'     => 'GetActiveSessionTimes',
                    'endpoint' => $this->endpointAppointment . '/activesessiontimes',
                    'headers'  => $this->headersBasic,
        ),
        'GetAppointmentOptions' => array(
                    'method'   => 'GET',
                    'name'     => 'GetAppointmentOptions',
                    'endpoint' => $this->endpointAppointment . '/appointmentoptions',
                    'headers'  => $this->headersBasic,
        ),
        'GetBookableItems'      => array(
                    'method'   => 'GET',
                    'name'     => 'GetBookableItems',
                    'endpoint' => $this->endpointAppointment . '/bookableitems',
                    'headers'  => $this->headersBasic,
        ),
        'GetScheduleItems'      => array(
                    'method'   => 'GET',
                    'name'     => 'GetScheduleItems',
                    'endpoint' => $this->endpointAppointment . '/scheduleitems',
                    'headers'  => $this->headersBasic,
        ),
        'GetStaffAppointments'  => array(
                    'method'   => 'GET',
                    'name'     => 'GetStaffAppointments',
                    'endpoint' => $this->endpointAppointment . '/staffappointments',
                    'headers'  => $this->headersBasic,
        ),
        'UpdateApppointment'    => array(
                    'method'   => 'POST',
                    'name'     => 'UpdateApppointment',
                    'endpoint' => $this->endpointAppointment . '/updateappointment',
                    'headers'  => $this->headersBasic,
        ),
        ),
        'ClassService'       => array(
        'AddClientToClass'       => array(
                    'method'   => 'POST',
                    'name'     => 'AddClientToClass',
                    'endpoint' => $this->endpointClasses . '/addclienttoclass',
                    'headers'  => $this->headersBasic,
        ),
        'GetClassDescriptions'   => array(
                    'method'   => 'GET',
                    'name'     => 'GetClassDescriptions',
                    'endpoint' => $this->endpointClasses . '/classdescriptions',
                    'headers'  => $this->headersBasic,
        ),
        'GetClassVisits'         => array(
                    'method'   => 'GET',
                    'name'     => 'GetClassVisits',
                    'endpoint' => $this->endpointClasses . '/classvisits',
                    'headers'  => $this->headersBasic,
        ),
        'GetClasses'             => array(
                    'method'   => 'GET',
                    'name'     => 'GetClasses',
                    'endpoint' => $this->endpointClasses . '/classes',
                    'headers'  => $this->headersBasic,
        ),
        'GetWaitlistEntries'     => array(
                    'method'   => 'GET',
                    'name'     => 'GetWaitlistEntries',
                    'endpoint' => $this->endpointClasses . '/waitlistentries',
                    'headers'  => $this->headersBasic,
        ),
        'RemoveClientFromClass'  => array(
                    'method'   => 'POST',
                    'name'     => 'RemoveClientFromClass',
                    'endpoint' => $this->endpointClasses . '/removeclientfromclass',
                    'headers'  => $this->headersBasic,
        ),
        'RemoveFromWaitlist'     => array(
                    'method'   => 'POST',
                    'name'     => 'RemoveFromWaitlist',
                    'endpoint' => $this->endpointClasses . '/removefromwaitlist',
                    'headers'  => $this->headersBasic,
        ),
        'SubstituteClassTeacher' => array(
                    'method'   => 'POST',
                    'name'     => 'SubstituteClassTeacher',
                    'endpoint' => $this->endpointClasses . '/substituteclassteacher',
                    'headers'  => $this->headersBasic,
        ),
        ),
        'ClientApi'          => array(
        'AddArrival'                         => array(
                    'method'   => 'POST',
                    'name'     => 'AddArrival',
                    'endpoint' => $this->endpointClient . '/addarrival',
                    'headers'  => $this->headersBasic,
        ),
        'AddClient'                          => array(
                    'method'   => 'POST',
                    'name'     => 'AddClient',
                    'endpoint' => $this->endpointClient . '/addclient',
                    'headers'  => $this->headersBasic,
        ),
        'AddContactLog'                      => array(
                    'method'   => 'POST',
                    'name'     => 'AddContactLog',
                    'endpoint' => $this->endpointClient . '/addcontactlog',
                    'headers'  => $this->headersBasic,
        ),
        'GetActiveClientMemberships'         => array(
                    'method'   => 'GET',
                    'name'     => 'GetActiveClientMemberships',
                    'endpoint' => $this->endpointClient . '/activeclientmemberships',
                    'headers'  => $this->headersBasic,
        ),
        'GetClientAccountBalances'           => array(
                    'method'   => 'GET',
                    'name'     => 'GetClientAccountBalances',
                    'endpoint' => $this->endpointClient . '/clientaccountbalances',
                    'headers'  => $this->headersBasic,
        ),
        'GetClientContracts'                 => array(
                    'method'   => 'GET',
                    'name'     => 'GetClientContracts',
                    'endpoint' => $this->endpointClient . '/clientcontracts',
                    'headers'  => $this->headersBasic,
        ),
        'GetClientFormulaNotes'              => array(
                    'method'   => 'GET',
                    'name'     => 'GetClientFormulaNotes',
                    'endpoint' => $this->endpointClient . '/clientformulanotes',
                    'headers'  => $this->headersBasic,
        ),
        'GetClientIndexes'                   => array(
                    'method'   => 'GET',
                    'name'     => 'GetClientIndexes',
                    'endpoint' => $this->endpointClient . '/clientindexes',
                    'headers'  => $this->headersBasic,
        ),
        'GetClientPurchases'                 => array(
                    'method'   => 'GET',
                    'name'     => 'GetClientPurchases',
                    'endpoint' => $this->endpointClient . '/clientpurchases',
                    'headers'  => $this->headersBasic,
        ),
        'GetClientReferralTypes'             => array(
                    'method'   => 'GET',
                    'name'     => 'GetClientReferralTypes',
                    'endpoint' => $this->endpointClient . '/clientreferraltypes',
                    'headers'  => $this->headersBasic,
        ),
        'GetClientServices'                  => array(
                    'method'   => 'GET',
                    'name'     => 'GetClientServices',
                    'endpoint' => $this->endpointClient . '/clientservices',
                    'headers'  => $this->headersBasic,
        ),
        'GetClientVisits'                    => array(
                    'method'   => 'GET',
                    'name'     => 'GetClientVisits',
                    'endpoint' => $this->endpointClient . '/clientvisits',
                    'headers'  => $this->headersBasic,
        ),
        'GetClients'                         => array(
                    'method'   => 'GET',
                    'name'     => 'GetClients',
                    'endpoint' => $this->endpointClient . '/clients',
                    'headers'  => $this->headersBasic,
        ),
        'GetContactLogs'                     => array(
                    'method'   => 'GET',
                    'name'     => 'GetContactLogs',
                    'endpoint' => $this->endpointClient . '/contactlogs',
                    'headers'  => $this->headersBasic,
        ),
        'GetCrossRegionalClientAssociations' => array(
                    'method'   => 'GET',
                    'name'     => 'GetCrossRegionalClientAssociations',
                    'endpoint' => $this->endpointClient . '/crossregionalclientassociations',
                    'headers'  => $this->headersBasic,
        ),
        'GetCustomClientFields'              => array(
                    'method'   => 'GET',
                    'name'     => 'GetCustomClientFields',
                    'endpoint' => $this->endpointClient . '/customclientfields',
                    'headers'  => $this->headersBasic,
        ),
        'GetRequiredClientFields'            => array(
                    'method'   => 'GET',
                    'name'     => 'GetRequiredClientFields',
                    'endpoint' => $this->endpointClient . '/requiredclientfields',
                    'headers'  => $this->headersBasic,
        ),
        'SendPasswordResetEmail'             => array(
                    'method'   => 'POST',
                    'name'     => 'SendPasswordResetEmail',
                    'endpoint' => $this->endpointClient . '/sendpasswordresetemail',
                    'headers'  => $this->headersBasic,
        ),
        'UpdateClient'                       => array(
                    'method'   => 'POST',
                    'name'     => 'UpdateClient',
                    'endpoint' => $this->endpointClient . '/updateclient',
                    'headers'  => $this->headersBasic,
        ),
        'UpdateClientService'                => array(
                    'method'   => 'POST',
                    'name'     => 'UpdateClientService',
                    'endpoint' => $this->endpointClient . '/updateclientservice',
                    'headers'  => $this->headersBasic,
        ),
        'UpdateClientVisit'                  => array(
                    'method'   => 'POST',
                    'name'     => 'UpdateClientVisit',
                    'endpoint' => $this->endpointClient . '/updateclientvisit',
                    'headers'  => $this->headersBasic,
        ),
        'UpdateContactLog'                   => array(
                    'method'   => 'POST',
                    'name'     => 'UpdateContactLog',
                    'endpoint' => $this->endpointClient . '/updatecontactlog',
                    'headers'  => $this->headersBasic,
        ),
        'UploadClientDocument'               => array(
                    'method'   => 'POST',
                    'name'     => 'UploadClientDocument',
                    'endpoint' => $this->endpointClient . '/uploadclientdocument',
                    'headers'  => $this->headersBasic,
        ),
        'UploadClientPhoto'                  => array(
                    'method'   => 'POST',
                    'name'     => 'UploadClientPhoto',
                    'endpoint' => $this->endpointClient . '/uploadclientphoto',
                    'headers'  => $this->headersBasic,
        ),
        ),
        'EnrollmentApi'      => array(
        'AddClientToEnrollment' => array(
                    'method'   => 'POST',
                    'name'     => 'AddClientToEnrollment',
                    'endpoint' => $this->endpointEnrollment . '/addclienttoenrollment',
                    'headers'  => $this->headersBasic,
        ),
        'GetEnrollments'        => array(
        'method'   => 'GET',
        'name'     => 'GetEnrollments',
        'endpoint' => $this->endpointEnrollment . '/enrollments',
        'headers'  => $this->headersBasic,
        ),
        ),
        'PayrollApi'         => array(
        'GetClassPayroll' => array(
                    'method'   => 'GET',
                    'name'     => 'GetClassPayroll',
                    'endpoint' => $this->endpointPayroll . '/classes',
                    'headers'  => $this->headersBasic,
        ),
        'GetTimeClock'    => array(
        'method'   => 'GET',
        'name'     => 'GetTimeClock',
        'endpoint' => $this->endpointPayroll . '/timeclock',
        'headers'  => $this->headersBasic,
        ),
        ),
        'SaleApi'            => array(
        'CheckoutShoppingCart'    => array(
                    'method'   => 'POST',
                    'name'     => 'CheckoutShoppingCart',
                    'endpoint' => $this->endpointSale . '/checkoutshoppingcart',
                    'headers'  => $this->headersAuthorized,
        ),
        'GetAcceptedCardTypes'    => array(
        'method'   => 'GET',
        'name'     => 'GetAcceptedCardTypes',
        'endpoint' => $this->endpointSale . '/acceptedcardtypes',
        'headers'  => $this->headersBasic,
        ),
        'GetContracts'            => array(
        'method'   => 'GET',
        'name'     => 'GetContracts',
        'endpoint' => $this->endpointSale . '/contracts',
        'headers'  => $this->headersAuthorized,
        ),
        'GetCustomPaymentMethods' => array(
        'method'   => 'GET',
        'name'     => 'GetCustomPaymentMethods',
        'endpoint' => $this->endpointSale . '/custompaymentmethods',
        'headers'  => $this->headersBasic,
        ),
        'GetGiftCards'            => array(
        'method'   => 'GET',
        'name'     => 'GetGiftCards',
        'endpoint' => $this->endpointSale . '/giftcards',
        'headers'  => $this->headersBasic,
        ),
        'GetProducts'             => array(
        'method'   => 'GET',
        'name'     => 'GetProducts',
        'endpoint' => $this->endpointSale . '/products',
        'headers'  => $this->headersBasic,
        ),
        'GetSales'                => array(
        'method'   => 'GET',
        'name'     => 'GetSales',
        'endpoint' => $this->endpointSale . '/sales',
        'headers'  => $this->headersBasic,
        ),
        'GetServices'             => array(
        'method'   => 'GET',
        'name'     => 'GetServices',
        'endpoint' => $this->endpointSale . '/services',
        'headers'  => $this->headersBasic,
        ),
        'PurchaseContract'        => array(
        'method'   => 'POST',
        'name'     => 'PurchaseContract',
        'endpoint' => $this->endpointSale . '/purchasecontract',
        'headers'  => $this->headersBasic,
        ),
        'PurchaseGiftCard'        => array(
        'method'   => 'POST',
        'name'     => 'PurchaseGiftCard',
        'endpoint' => $this->endpointSale . '/purchasegiftcard',
        'headers'  => $this->headersBasic,
        ),
        ),
        'SiteApi'            => array(
        'GetActivationCode' => array(
                    'method'   => 'GET',
                    'name'     => 'GetActivationCode',
                    'endpoint' => $this->endpointSite . '/activationcode',
                    'headers'  => $this->headersBasic,
        ),
        'GetLocations'      => array(
        'method'   => 'GET',
        'name'     => 'GetLocations',
        'endpoint' => $this->endpointSite . '/locations',
        'headers'  => $this->headersBasic,
        ),
        'GetGenders'        => array(
        'method'   => 'GET',
        'name'     => 'GetGenders',
        'endpoint' => $this->endpointSite . '/genders',
        'headers'  => $this->headersBasic,
        ),
        'GetMemberships'    => array(
        'method'   => 'GET',
        'name'     => 'GetMemberships',
        'endpoint' => $this->endpointSite . '/memberships',
        'headers'  => $this->headersBasic,
        ),
        'GetPrograms'       => array(
        'method'   => 'GET',
        'name'     => 'GetPrograms',
        'endpoint' => $this->endpointSite . '/programs',
        'headers'  => $this->headersBasic,
        ),
        'GetResources'      => array(
        'method'   => 'GET',
        'name'     => 'GetResources',
        'endpoint' => $this->endpointSite . '/resources',
        'headers'  => $this->headersBasic,
        ),
        'GetSessionTypes'   => array(
        'method'   => 'GET',
        'name'     => 'GetSessionTypes',
        'endpoint' => $this->endpointSite . '/sessiontypes',
        'headers'  => $this->headersBasic,
        ),
        'GetSites'          => array(
        'method'   => 'GET',
        'name'     => 'GetSites',
        'endpoint' => $this->endpointSite . '/sites',
        'headers'  => $this->headersBasic,
        ),
        ),
        'StaffApi'           => array(
        'GetStaff'            => array(
                    'method'   => 'GET',
                    'name'     => 'GetStaff',
                    'endpoint' => $this->endpointStaff . '/staff',
                    'headers'  => $this->headersBasic,
        ),
        'GetStaffPermissions' => array(
        'method'   => 'GET',
        'name'     => 'GetStaffPermissions',
        'endpoint' => $this->endpointStaff . '/staffpermissions',
        'headers'  => $this->headersBasic,
        ),
        ),
        'UserToken'          => array(
        'TokenIssue'  => array(
                    'method'   => 'POST',
                    'name'     => 'TokenIssue',
                    'endpoint' => $this->endpointUserToken . '/issue',
                    'headers'  => $this->headersBasic,
        ),
        'TokenRevoke' => array(
        'method'   => 'DELETE',
        'name'     => 'TokenRevoke',
        'endpoint' => $this->endpointUserToken . '/revoke',
        'headers'  => $this->headersBasic,
        ),
        ),
        );
    }
}
