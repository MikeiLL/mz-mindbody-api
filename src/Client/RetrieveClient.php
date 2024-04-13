<?php
namespace MZoo\MzMindbody\Client;

use MZoo\MzMindbody\Core;
use MZoo\MzMindbody\Common;
use MZoo\MzMindbody\Libraries;
use MZoo\MzMindbody\Schedule;
use MZoo\MzMindbody\Common\Interfaces;
use MZoo\MzMindbody as NS;

/*
 * Class that holds Client Interface Methods
 *
 *
 */
class RetrieveClient extends Interfaces\Retrieve {

    /**
     * The Mindbody API Object
     *
     * @access private
     */
    private $mb;

    /**
     * Client ID
     *
     * The MBO ID of the Current User/Client
     *
     * @access private
     */
    private $clientID;

    /**
     * MBO Client
     *
     * GetClient result from MBO
     *
     * @access private
     */
    private $mbo_client;

    /**
     * Client Services
     *
     * Services returned from MBO
     *
     * @access private
     */
    private $services;

    /**
     * Format for date display, specific to MBO API Plugin.
     *
     * @since    2.4.7
     * @access   public
     * @var      string $date_format WP date format option.
     */
    public $date_format;

    /**
     * Format for time display, specific to MBO API Plugin.
     *
     * @since    2.4.7
     * @access   public
     * @var      string $time_format
     */
    public $time_format;

    /**
     * Class constructor
     *
     * Since 2.4.7
     */
    public function __construct(){
        $this->date_format = Core\MzMindbodyApi::$date_format;
        $this->time_format = Core\MzMindbodyApi::$time_format;
        // Ensure we have a valid token.
        $token_mgmt = new Common\TokenManagement();
        $token_mgmt->serve_token();
        $this->mb = $this->instantiate_mbo_api();
    }

    /**
     * Get Client by SearchText field.
     *
     * Email, Phone, etc from MBO.
     *
     * @since 1.0.7
     * @param array $match_field to check.
     * @param string $needle text to search on and match.
     * @return array|null $found from MBO.
     */
    public function get_client_by_searchText_field( $match_field, $needle ) {
        // Create the MBO Object
        $this->get_mbo_results();
        // Get the client(s) from the API.
        $response = $this->mb->GetClients( array( 'searchText' => $needle ) );
        if (!empty($response['Error'])) return null;
        foreach( $response['Clients'] as $client ) {
            if ( $needle == $client[$match_field] ) {
                $found = $client;
                break;
            }
        }
        return $found;
    }

    /**
     * Get Client by ID
     *
     * @since 1.0.0
     * @param string $id The client email.
     * @return array from Mindbody API.
     */
    public function get_client_by_id( $id ) {
        // Create the MBO Object
        $this->get_mbo_results();
        // Get the client from the API.
        return $this->mb->GetClients( array( 'clientIds' => array( $id ) ) );
    }

    /**
     * Add New Client
     *
     * @since 1.0.0
     * @param array $data Client data.
     * @return array from Mindbody API.
     */
    public function add_new_client( $data ) {
        // Create the MBO Object
        $this->get_mbo_results();
        $client_info = array(
            'FirstName'     => $data['first_name'],
            'LastName'      => $data['last_name'],
            'Email'         => $data['email'],
            'MobilePhone'   => $data['phone'],
        );
        return $this->mb->AddClient( $client_info );
    }
    /**
     * Get Client by ID
     *
     * @since 1.0.0
     * @param string $id The client email.
     * @return array from Mindbody API.
     */
    public function get_client_schedule( $id ) {

        // Create the MBO Object
        $this->get_mbo_results();


        $result = $this->mb->GetClientVisits(
            array(
                'clientId' => $id,
                'EndDate'  => \wp_date( 'Y-m-d', \strtotime( '+1 month' ) ),
            )
        );
        if (isset($result['Visits'])) {
            // @TODO MAYBE add to session
            $sorted_client_schedule = $this->sort_classes_by_date_then_time($result);
            $result['Visits'] = $sorted_client_schedule;
        }
        return $result;

    }

    /**
     * Add Client to class or classes
     *
     * @since 1.0.0
     * @param string $client_id The client ID.
     * @param string $class_id The class ID.
     * @return array from Mindbody API.
     */
    public function add_client_to_class( $client_id, $class_id ) {

        // Create the MBO Object
        $this->get_mbo_results();

        $additions = array();

        $additions['ClassId'] = $class_id;

        $additions['ClientId'] = $client_id;

        $additions['SendEmail'] = "true";

        $additions['RequirePayment'] = "false";

        $additions['Waitlist'] = "false";

        $result = $this->mb->AddClientToClass( $additions );

        return $result;
    }

    /**
     * Remove Client from class or classes
     *
     * @since 1.0.0
     * @param string $client_id The client ID.
     * @param bool $late_cancel To "late cancel" as per mbo.
     * @return array from Mindbody API.
     */
    public function remove_client_from_class( $client_id, $class_id, $late_cancel = false ) {

        $removals = array();

        $removals['ClassId'] = $class_id;

        $removals['ClientId'] = $client_id;

        $starttime = \strtotime($_SESSION['Mbo_User_Visits'][$class_id]['StartDateTime']);

        /*
         * We started to build some functionality to allow user to choose
         * whether to late cancel or not, but for now just warning them,
         * and making decision based on .env specified LATE_CANCEL_WINDOW.
         */

        if (Engine\Credentials::$late_cancel_window) {
            if (current_time( 'timestamp' ) > ($starttime - Engine\Credentials::$late_cancel_window * 60)) {
                $removals['LateCancel'] = 'true';
            }
        }

        $removals['SendEmail'] = true;

        return $this->mb->RemoveClientFromClass( $removals );
    }


    /**
     * Get Client
     *
     * Since 2.5.7
     *
     * @param $validateLoginResult array with result from MBO API
     */
    public function get_clients( $id, $email="" ){
        // Create the MBO Object
        $this->get_mbo_results();
        $request_data = [];

        // if we are in -99 site, we need to search by email for this client.
        if ( (string) -99 === (string) Core\MzMindbodyApi::$basic_options['mz_mindbody_siteID'] ) {
            $request_data['searchText'] = $email;
        } else {
            $request_data['ClientID'] = $id;
        }
        $result = $this->mb->GetClients($request_data);

        return $result;

    }


    /**
     * Client Log Out
     */
    public function client_log_out(){

        $_SESSION['MindbodyAuth'] = [];
        return true;
    }

    /**
     * Return MBO Account config required fields with what I think are default required fields.
     *
     * since: 2.5.7
     *
     * @param bool $fill_defaults whether to fill in defaults
     *
     * NOTE: Necessary when making request to the Platform Api to prevent 401 based on:
     *
     * "The documentation on that endpoint is not totally accurate.
     * The "businessId" should be used in the body of the request instead of the Header.
     * Leaving that out of the request body will return an error about the business ID.
     * Another thing that we found during testing is that passing information from the
     * Identity account, email, first name, last name, also seems to return an error.
     * That information should populate automatically, so the request would only need
     * the required fields that the business asks for, such as "address_line_1", "state",
     * "city", and "postal_code". The property "names" will be underscored like that
     * and be all lower case.
     *
     * @return array numeric array of required fields
     */
    public function get_signup_form_fields( $fill_defaults = false ){
        // Create the MBO Object
        $this->get_mbo_results();

        if ( true || false === get_transient( 'required_mbo_fields' ) ) {

            $requiredFields = $this->mb->GetRequiredClientFields();

            if (true == $fill_defaults) {

                $default_required_fields = [
                    "Email",
                    "FirstName",
                    "LastName"
                ];

                $requiredFields = array_merge($default_required_fields, $requiredFields['RequiredClientFields']);
            }
            // Store the transient for 12 hours or admin set duration.
            set_transient( 'required_mbo_fields', $requiredFields['RequiredClientFields'], 43200 );
        }

        return get_transient( 'required_mbo_fields' );
    }

    /**
     * Create MBO Account
     */
    public function add_client( $client_fields = array() ){
        // Create the MBO Object
        $this->get_mbo_results();

        $signup_result = $this->mb->AddClient($client_fields);

        return $signup_result;

    }

    /**
     * Get client details.
     *
     * since: 2.5.7
     *
     * return array of client info from MBO or require login
     */
    public function get_client_details() {
        // Create the MBO Object
        $this->get_mbo_results();

        $client_info = $_SESSION['MindbodyAuth']['MBO_Client'];

        if (empty($client_info)) return __('Please Login', 'mz-mindbody-api');

        return $client_info['mbo_result'];

    }

    /**
     * Get client active memberships.
     *
     * Memberships will be an array, each of which contain among other stuff:
     *
     * [Name] => Monthly Membership - Gym Access
     *      [PaymentDate] => 2020-05-06T00:00:00
     *      [Program] => Array
     *          (
     *              [Id] => 21
     *              [Name] => Gym Membership
     *              [ScheduleType] => Arrival
     *              [CancelOffset] => 0
     *          )
     * [Remaining] => 1000, etc..
     *
     * since: 2.5.7
     *
     * return array numeric array of active memberships
     */
    public function get_client_active_memberships() {

        // Create the MBO Object
        $this->get_mbo_results();

        $result = $this->mb->GetActiveClientMemberships(['clientId' => $_SESSION['MindbodyAuth']['MBO_USER_StudioProfile_ID']]); // UniqueID ??

        return $result['ClientMemberships'];
    }

    /**
     * Get client account balance.
     *
     * since: 2.5.7
     *
     * This wraps a method for getting balances for multiple accounts, but
     * we just get it for one.
     *
     * return string client account balance
     */
    public function get_client_account_balance() {

        // Create the MBO Object
        $this->get_mbo_results();

        // Can accept a list of client id strings
        $result = $this->mb->GetClientAccountBalances(['clientIds' => $_SESSION['MindbodyAuth']['MBO_USER_StudioProfile_ID']]); // UniqueID ??

        // Just return the first (and only) result
        return $result['Clients'][0]['AccountBalance'];
    }

    /**
     * Get client contracts.
     *
     * Since 2.5.7
     *
     * Returns an array of items that look like this:
     *
     * [AgreementDate] => 2020-05-06T00:00:00
     * [AutopayStatus] => Active
     * [ContractName] => Monthly Membership - 12 Months
     * [EndDate] => 2021-05-06T00:00:00
     * [Id] => 15040
     * [OriginationLocationId] => 1
     * [StartDate] => 2020-05-06T00:00:00
     * [SiteId] => -99
     * [UpcomingAutopayEvents] => Array
     *     (
     *         [0] => Array
     *             (
     *                 [ClientContractId] => 15040
     *                 [ChargeAmount] => 75
     *                 [PaymentMethod] => DebitAccount
     *                 [ScheduleDate] => 2020-06-06T00:00:00
     *             )
     * etc...
     * [LocationId] => 1
     * [Payments] => Array
     * (
     *     [0] => Array
     *         (
     *             [Id] => 158015
     *             [Amount] => 75
     *             [Method] => 16
     *             [Type] => Account
     *             [Notes] =>
     *         )
     *
     * )
     *
     * return array numeric array of client contracts
     */
    public function get_client_contracts() {

        // Create the MBO Object
        $this->get_mbo_results();

        $result = $this->mb->GetClientContracts(['clientId' => $_SESSION['MindbodyAuth']['MBO_USER_StudioProfile_ID']]); // UniqueID ??

        return $result['Contracts'];
    }

    /**
     * Get client purchases.
     *
     * Since 2.5.7
     *
     * Returns an array of items that look like this:
     * [Sale] => Array
     *     (
     *         [Id] => 100160377
     *         [SaleDate] => 2020-05-06T00:00:00Z
     *         [SaleTime] => 23:46:45
     *         [SaleDateTime] => 2020-05-06T23:46:45Z
     *         [ClientId] => 100015683
     *         [PurchasedItems] => Array
     *             (
     *                 [0] => Array
     *                     (
     *                         [Id] => 1198
     *                         [IsService] => 1
     *                         [BarcodeId] =>
     *                     )
     *             )
     *         [LocationId] => 1
     *         [Payments] => Array
     *             (
     *                 [0] => Array
     *                     (
     *                         [Id] => 158015
     *                         [Amount] => 75
     *                         [Method] => 16
     *                         [Type] => Account
     *                         [Notes] =>
     *                     )
     *             )
     *     )
     * [Description] => Monthly Membership - Gym Access
     * [AccountPayment] =>
     * [Price] => 75
     * [AmountPaid] => 75
     * [Discount] => 0
     * [Tax] => 0
     * [Returned] =>
     * [Quantity] => 1
     *
     * return array numeric array of client purchases
     */
    public function get_client_purchases() {

        // Create the MBO Object
        $this->get_mbo_results();

        $result = $this->mb->GetClientPurchases(['clientId' => $_SESSION['MindbodyAuth']['MBO_USER_StudioProfile_ID']]); // UniqueID ??

        return $result['Purchases'];
    }

    /**
     * Get client services.
     *
     * since: 2.5.7
     *
     * return array numeric array of required fields
     */
    public function get_client_services() {

        // Create the MBO Object
        $this->get_mbo_results();

        $result = $this->mb->GetClientServices(['clientId' => $_SESSION['MindbodyAuth']['MBO_USER_StudioProfile_ID']]); // UniqueID ??

        return $result;
    }

    /**
     * Create MBO Account
     * since 5.4.7
     *
     * param array containing 'UserEmail' 'UserFirstName' 'UserLastName'
     *
     * return array either error or new client details
     */
    public function password_reset_email_request( $clientID = array() ){

        // Crate the MBO Object
        $this->get_mbo_results();

        $result = $this->mb->SendPasswordResetEmail($clientID);

        return $result;

    }


    /**
     * Check Client Logged In
     *
     * Since 2.5.7
     * Is there a session containing the MBO_GUID of current user
     *
     * @return bool
     */
    public function check_client_logged(){

        return ( 1 == (bool) $_SESSION['MindbodyAuth']['MBO_USER_StudioProfile_ID'] ) ? 1 : 0;

    }

    /**
     * Get API version, create API Interface Object
     *
     * @since 2.4.7
     *
     * @param $api_version int in case we need to call on API v5 as in for client login
     *
     *
     * @return array of MBO schedule data
     */
    public function get_mbo_results(){

        $this->mb = $this->instantiate_mbo_API();

        if ( !$this->mb || $this->mb == 'NO_API_SERVICE' ) return false;

        return true;
    }

     /**
     * Return an array of MBO Class Objects, ordered by date, then time.
     *
     * This is a limited version of the Retrieve Classes method used in horizontal schedule
     *
     *
     * @param @type array $mz_classes
     *
     * @return @type array of Objects from Single_event class, in Date (and time) sequence.
     */
    public function sort_classes_by_date_then_time($client_schedule = array()) {

        $classesByDateThenTime = array();

        /* For some reason, when there is only a single class in the client
         * schedule, the 'Visits' array contains that visit, but when there are multiple
         * visits then the array of visits is under 'Visits'/'Visit'
         */
    /*     if (is_array($client_schedule['Visits'][0])){
            // Multiple visits
            $visit_array_scope = $client_schedule['Visits']['Visit'];
        } else {
            $visit_array_scope = $client_schedule['Visits'];
        } */


        foreach($client_schedule['Visits'] as $visit)
        {
            // Make a timestamp of just the day to use as key for that day's classes
            $dt = new \DateTime($visit['StartDateTime']);
            $just_date =  $dt->format('Y-m-d');

            /* Create a new array with a key for each date YYYY-MM-DD
            and corresponding value an array of class details */

            $single_event = new Schedule\MiniScheduleItem($visit);

            if(!empty($classesByDateThenTime[$just_date])) {
                array_push($classesByDateThenTime[$just_date], $single_event);
            } else {
                $classesByDateThenTime[$just_date] = array($single_event);
            }
        }

        /* They are not ordered by date so order them by date */
        ksort($classesByDateThenTime);

        foreach($classesByDateThenTime as $classDate => &$classes)
        {
            /*
             * $classes is an array of all classes for given date
             * Take each of the class arrays and order it by time
             * $classesByDateThenTime should have a length of seven, one for
             * each day of the week.
             */
            usort($classes, function($a, $b) {
                if($a->startDateTime == $b->startDateTime) {
                    return 0;
                }
                return $a->startDateTime < $b->startDateTime ? -1 : 1;
            });
        }

        return $classesByDateThenTime;
    }


    /**
     * Make Numeric Array
     *
     * Make sure that we have an array
     *
     * @param $data
     * @return array
     */
    private function make_numeric_array($data) {

        return (isset($data[0])) ? $data : array($data);

    }


}
