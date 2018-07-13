<?php
namespace MZ_Mindbody\Inc\Client;

use MZ_Mindbody\Inc\Core as Core;
use MZ_Mindbody\Inc\Common as Common;
use MZ_Mindbody\Inc\Libraries as Libraries;
use MZ_Mindbody\Inc\Schedule as Schedule;
use MZ_Mindbody\Inc\Common\Interfaces as Interfaces;
use MZ_Mindbody as NS;

/*
 * Class that holds Client Interface Methods
 *
 *
 */
class Client_Portal extends Interfaces\Retrieve {

    /**
     * The Mindbody API Object
     *
     * @access private
     */
    private $mb;

    /**
     * Template Date for sending to template partials
     *
     * @access private
     */
    private $template_data;

    /**
     * Client ID
     *
     * The MBO ID of the Current User/Client
     *
     * @access private
     */
    private $clientID;

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
        $this->date_format = Core\MZ_Mindbody_Api::$date_format;
        $this->time_format = Core\MZ_Mindbody_Api::$time_format;
    }

    /**
     * Client Log In
     */
    public function client_log_in(){

        check_ajax_referer($_REQUEST['nonce'], "mz_signup_nonce", false);

        // Crate the MBO Object
        $this->get_mbo_results();

        ob_start();

        $result['type'] = 'success';

        // Parse the serialized form into an array.
        $params = array();
        parse_str($_REQUEST['form'], $params);

        if(!empty($params)) {

            // We have form data, attempt login validation
            $validateLogin = $this->mb->ValidateLogin(array(
                'Username' => $params['username'],
                'Password' => $params['password']
            ));


            if(!empty($validateLogin['ValidateLoginResult']['GUID'])) {

                // If validated, create two session variables and store

                NS\MZMBO()->session->set( 'MBO_GUID', $validateLogin['ValidateLoginResult']['GUID'] );
                NS\MZMBO()->session->set( 'MBO_Client', $validateLogin['ValidateLoginResult']['Client'] );

                // If user has elected to remember login, create cookie.
                //if (($params['keep_me_logged_in'] == 'on') && (Core\MZ_Mindbody_Api::$advanced_options['keep_loogged_in_cookie'] == 'on')):

                //    $userlabel = 'MZ_MBO_USER';

                 //   $value = json_encode(array('MBO_GUID' => $validateLogin['ValidateLoginResult']['GUID'], 'MBO_Client' => $validateLogin['ValidateLoginResult']['Client']), JSON_FORCE_OBJECT);//

                    //setcookie( $userlabel, $value, time()+60*60*24*30, COOKIEPATH, COOKIE_DOMAIN );

                    // if(!isset($_COOKIE[$userlabel])) {
                    //     echo "The cookie: '" . $userlabel . "' is not set.";
                    // } else {
                    //     echo "The cookie '" . $userlabel . "' is set.";
                    //     NS\MZMBO()->helpers->mz_pr($_COOKIE['MZ_MBO_USER']);
                    //     NS\MZMBO()->helpers->mz_pr(json_decode($_COOKIE['MZ_MBO_USER']));
                    //     $error = json_last_error();
                    //     if ($error !== JSON_ERROR_NONE) {
                    //         echo json_last_error_msg();
                    //     } else {
                    //         echo $json;
                    //     }
                    // }
                //endif;

            } else {

                // Otherwise error message and display form again
                if ( !empty($validateLogin['ValidateLoginResult']['Message'] ) ) {

                    echo $validateLogin['ValidateLoginResult']['Message'];

                } else {

                    _e('Invalid Login', 'mz-mindbody-api') . '<br/>';

                }

                $result['type'] = 'error';
            }

        } else {

            $result['type'] = 'error';

        }


        $result['message'] = ob_get_clean();

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $result = json_encode($result);
            echo $result;
        } else {
            header("Location: " . $_SERVER["HTTP_REFERER"]);
        }

        die();

    }

    /**
     * Client Log Out
     */
    public function client_log_out(){

        ob_start();

        $result['type'] = 'success';

        NS\MZMBO()->session->clear();

        _e('Logged Out', 'mz-mindbody-api');

        echo '<br/>';

        $result['message'] = ob_get_clean();

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $result = json_encode($result);
            echo $result;
        } else {
            header("Location: " . $_SERVER["HTTP_REFERER"]);
        }

        die();
    }

    /**
     * Register for Class
     *
     * This is the endpoint when client confirms they want to sign-up for a class.
     */
    public function register_for_class(){


        check_ajax_referer($_REQUEST['nonce'], "mz_signup_nonce", false);

        ob_start();

        $result['type'] = 'success';

        if ( true ) {

            $template_data = array();

            $this->clientID = NS\MZMBO()->session->get('MBO_Client')['ID'];

            $add_client_to_class_result = $this->add_client_to_class($_REQUEST['classID']);

            $template_data = array(
                'type'      => $add_client_to_class_result['type'],
                'message'   => $add_client_to_class_result['message'],
                'nonce'     => $_REQUEST['nonce'],
                'siteID'    => $_REQUEST['siteID'],
                'location'  => $_REQUEST['location']
            );

            // echo $this->mb->debug();
            $template_loader = new Core\Template_Loader();

            $template_loader->set_template_data($template_data);
            $template_loader->get_template_part('added_to_class');

        } else {

            $result['type'] = 'error';
            // Print out the error message
            echo $add_client_to_class_result['message'];

        }

        $result['message'] = ob_get_clean();

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $result = json_encode($result);
            echo $result;
        } else {
            header("Location: " . $_SERVER["HTTP_REFERER"]);
        }

        die();
    }

    /**
     * Add Client to Class
     *
     * Add Client to Class via MBO API
     *
     * @param $classID int the ID of the class as per MBO
     * @access private
     *
     * @used by register_for_class()
     *
     * @return array with result of api call and message type.
     */
    private function add_client_to_class($classID) {

        $result = array();

        $additions = array();

        $additions['ClassIDs'] = array($classID);

        $additions['ClientIDs'] = array($this->clientID);

        $additions['SendEmail'] = true;

        $additions['RequirePayment'] = false;

        // Crate the MBO Object
        $this->get_mbo_results();

        $signupData = $this->mb->AddClientsToClasses($additions);

        if ( $signupData['AddClientsToClassesResult']['ErrorCode'] != 200 ) {
            // Something did not succeed

            $result['type'] = "error";

            $result['message'] = '';

            if (!isset($signupData['AddClientsToClassesResult']['Classes']['Class']['Clients']['Client'])) :

                $result['type'] = "error";

                if (isset($signupData['AddClientsToClassesResult']['Classes']['Class']['Messages'])):

                    foreach ($signupData['AddClientsToClassesResult']['Classes']['Class']['Messages'] as $message) {

                        $result['message'] .= explode('.', $message)[0] . '.';

                    }

                endif;

            else:

                foreach ($signupData['AddClientsToClassesResult']['Classes']['Class']['Clients']['Client']['Messages'] as $message){

                    if (strpos($message, 'already booked') != false){

                        $result['type'] = "booked";

                        $result['message'] .= __('You are already booked at this time.', 'mz-mindbody-api');

                    } else {

                        /*
                         * For some reason MBO returns an echo in it's error messages. So
                         * here we split two sentences and return the first one. Pretty hacky.
                         */
                        $result['message'] .= explode('.', $message)[0] . '.';

                    }
                }

            endif;

        } else {

            $result['type'] = "success";

            $result['message'] = __('Registered via MindBody', 'mz-mindbody-api');

        }

       return $result;

    }

    /**
     * Generate MBO Account Signup Form
     */
    public function generate_mbo_signup_form(){

        // Crate the MBO Object
        $this->get_mbo_results();

        $requiredFields = $this->mb->GetRequiredClientFields();

        $result['type'] = 'success';

        ob_start();

        if(!empty($requiredFields['GetRequiredClientFieldsResult']['RequiredClientFields']['string'])) {

            $requiredFields = $this->make_numeric_array($requiredFields['GetRequiredClientFieldsResult']['RequiredClientFields']['string']);

        } else {

            $requiredFields = false;

        }

        $requiredFieldsInputs = '';

        if(!empty($requiredFields)) {

            // Force single element $requiredFields into array form
            if (!is_array($requiredFields)){

                $requiredFields = array($requiredFields);
            }

            foreach($requiredFields as $field) {

                $requiredFieldsInputs .= "<label for='$field'>{$field} </label><input type='text' name='data[Client][$field]' id='$field' placeholder='$field' required /><br />";

            }
        }

        $global_strings = NS\MZMBO()->i18n->get();

        $template_loader = new Core\Template_Loader();

        $this->template_data = array(
            'password' => $global_strings['password'],
            'username' => $global_strings['username'],
            'antispam' => __('Leave this empty-slash-blank', 'mz-mindbody-api'),
            'firstname' => __('First Name', 'mz-mindbody-api'),
            'lastname' => __('Last Name', 'mz-mindbody-api'),
            'sign_up' => __('Sign up', 'mz-mindbody-api'),
            'requiredFieldsInputs' => $requiredFieldsInputs,
            'nonce' => $_REQUEST['nonce'],
            'classID' => $_REQUEST['classID']
        );

        $template_loader->set_template_data($this->template_data);
        $template_loader->get_template_part('create_mbo_account');


        $result['message'] = ob_get_clean();

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $result = json_encode($result);
            echo $result;
        } else {
            header("Location: " . $_SERVER["HTTP_REFERER"]);
        }

        die();
    }

    /**
     * Create MBO Account
     */
    public function create_mbo_account(){

        check_ajax_referer($_REQUEST['nonce'], "mz_signup_nonce", false);

        // Crate the MBO Object
        $this->get_mbo_results();

        $result['type'] = 'success';

        ob_start();


        // Parse the serialized form into an array.
        $params = array();
        parse_str($_REQUEST['form'], $params);

        if(!empty($params['website_url'])){
            // This is a robot
            die();
        }

        if(!empty($params['data']['Client'])) {

            //NS\MZMBO()->helpers->mz_pr($_POST['data']['Client']['MobilePhone']);
            if (isset($params['data']['Client']['BirthDate'])) {
                $params['data']['Client']['BirthDate'] = date('c', strtotime($params['data']['Client']['BirthDate']));
            }

            $options = array(
                'Clients' => array(
                    'Client' => $params['data']['Client']
                )
            );
            $signupData = $this->mb->AddOrUpdateClients($options);

            if($signupData['AddOrUpdateClientsResult']['Clients']['Client']['Action'] == 'Added') {

                $validateLogin = $this->mb->ValidateLogin(array(
                    'Username' => $params['data']['Client']['Username'],
                    'Password' => $params['data']['Client']['Password']
                ));

                if ( !empty($validateLogin['ValidateLoginResult']['GUID']) ) {

                    NS\MZMBO()->session->set('MBO_GUID', $validateLogin['ValidateLoginResult']['GUID']);

                    NS\MZMBO()->session->set('MBO_Client', $validateLogin['ValidateLoginResult']['Client']);

                    echo '<h3>' . __('Congratulations. You are now logged in with your new Mindbody account.', 'mz-mindbody-api') . '</h3>';

                    echo '<div class="mz_signup_welcome">' . __('Sign-up for some classes.', 'mz-mindbody-api') . '</div>';

                } else {
                    NS\MZMBO()->helpers->mz_pr($validateLogin);
                }

            } else if ($signupData['AddOrUpdateClientsResult']['Clients']['Client']['Action'] == 'Failed'){

                echo '<h3>' . $signupData['AddOrUpdateClientsResult']['Clients']['Client']['Messages']['string'] . '</h3>';

                echo '<a id="createMBOAccount" href="#" data-nonce="' . $_REQUEST['nonce'] . '" class="btn btn-primary mz_add_to_class">' . __("Try Again", "mz-mindbody-api") . '</a>';
            }

        }

        $result['message'] = ob_get_clean();

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $result = json_encode($result);
            echo $result;
        } else {
            header("Location: " . $_SERVER["HTTP_REFERER"]);
        }

        die();
    }

    /**
     * Display Client Schedule
     *
     */
    public function display_client_schedule(){

        check_ajax_referer($_REQUEST['nonce'], "mz_signup_nonce", false);

        ob_start();

        $result['type'] = 'success';

        $schedule = $this->get_client_schedule();

        if ( ( (bool) NS\MZMBO()->session->get('MBO_GUID') === true ) && ($schedule['type'] == 'success') ) {

            $template_data = array(
                'date_format' => $this->date_format,
                'time_format' => $this->time_format,
                'classes'     => $schedule['message'],
                'nonce'       => $_REQUEST['nonce'],
                'siteID'      => $_REQUEST['siteID'],
                'location'      => $_REQUEST['location']
            );

            //echo $this->mb->debug();
            $template_loader = new Core\Template_Loader();

            $template_loader->set_template_data($template_data);
            $template_loader->get_template_part('client_schedule');
            //NS\MZMBO()->helpers->mz_pr($this->get_client_schedule()['message']);

        } else {

            $result['type'] = 'error';

        }


        $result['message'] = ob_get_clean();

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $result = json_encode($result);
            echo $result;
        } else {
            header("Location: " . $_SERVER["HTTP_REFERER"]);
        }

        die();
    }

    /**
     * Get Client Schedule
     *
     * Fetch Client Schedule from MBO
     *
     * @access private
     */
    private function get_client_schedule() {

        $result = array();

        $additions = array();

        $start_date = new \Datetime( '@' . current_time( 'timestamp' ));

        $end_date = new \Datetime( '@' . current_time( 'timestamp' ));

        $di = new \DateInterval('P4W');

        $end_date->add($di);

        $additions['StartDate'] = $start_date->format('Y-m-d');

        $additions['EndDate'] = $end_date->format('Y-m-d');

        $this->clientID = NS\MZMBO()->session->get('MBO_Client')['ID'];

        $additions['ClientID'] = $this->clientID;

        // Crate the MBO Object
        $this->get_mbo_results();

        if ( !$this->mb || $this->mb == 'NO_SOAP_SERVICE' ) return false;

        $client_schedule = $this->mb->GetClientSchedule($additions);

        if ($client_schedule['GetClientScheduleResult']['Status'] != 'Success'):
            $result['message'] = array(NS\MZMBO()->i18n->get('result_error'),
                $schedule_data['GetClientScheduleResult']['Status'],
                $schedule_data['GetClientScheduleResult']);
            $result['message'] = NS\MZMBO()->session->get('MBO_Client')['ID'];
        else:
            $result['message'] = $this->sort_classes_by_date_then_time($client_schedule);
        endif;

        $result['type'] = "success";

        return $result;

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

        foreach($client_schedule['GetClientScheduleResult']['Visits']['Visit'] as $visit)
        {
            // Make a timestamp of just the day to use as key for that day's classes
            $dt = new \DateTime($visit['StartDateTime']);
            $just_date =  $dt->format('Y-m-d');

            /* Create a new array with a key for each date YYYY-MM-DD
            and corresponding value an array of class details */

            $single_event = new Schedule\Mini_Schedule_Item($visit);

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
     * Check Client Logged In
     *
     * Function run by ajax to continually check if client is logged in
     */
    public function check_client_logged(){

        $result['type'] = 'success';
        $result['message'] =  ( 1 == (bool) NS\MZMBO()->session->get('MBO_GUID') ) ? 1 : 0;

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $result = json_encode($result);
            echo $result;
        } else {
            header("Location: " . $_SERVER["HTTP_REFERER"]);
        }

        die();
    }

    /**
     * Get a timestamp, return data from MBO api, store it in a transient and
     * as object attribute.
     *
     * @since 2.4.7
     *
     * @param @timestamp defaults to current time
     *
     *
     * @return array of MBO schedule data
     */
    public function get_mbo_results(){

        $this->mb = $this->instantiate_mbo_API();

        if ( !$this->mb || $this->mb == 'NO_SOAP_SERVICE' ) return false;

        return true;
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