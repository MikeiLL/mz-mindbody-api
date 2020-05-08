<?php
namespace MZ_Mindbody\Inc\Client;

use MZ_Mindbody\Inc\Core as Core;
use MZ_Mindbody\Inc\Common as Common;
use MZ_Mindbody\Inc\Libraries as Libraries;
use MZ_Mindbody\Inc\Schedule as Schedule;
use MZ_Mindbody\Inc\Common\Interfaces as Interfaces;
use MZ_Mindbody as NS;

/*
 * Class that holds Client Interface Methods for Ajax requests
 *
 *
 */
class Client_Portal extends Retrieve_Client {

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
    public function ajax_client_log_in(){

        check_ajax_referer($_REQUEST['nonce'], "mz_signup_nonce", false);

        // Create the MBO Object
        $this->get_mbo_results();

        // Init message
        $result['message'] = '';

        $result['type'] = 'success';

        // Parse the serialized form into an array.
        $params = array();
        parse_str($_REQUEST['form'], $params);

        if(!empty($params)) {

        	$result = $this->log_client_in($params);

        } else {

            $result['type'] = 'error';

        }

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
    public function ajax_client_log_out(){

        check_ajax_referer($_REQUEST['nonce'], "mz_client_log_out", false);

        ob_start();

        $result['type'] = 'success';

        $this->client_log_out();

        // update class attribute to hold logged out status
        $this->client_logged_in = false;

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
    public function ajax_register_for_class(){

        check_ajax_referer($_REQUEST['nonce'], "mz_register_for_class", false);

        ob_start();

        $result['type'] = 'success';

        if ( NS\MZMBO()->session->get('mbo_guid') ) {

            $template_data = array();

            $this->clientID = NS\MZMBO()->session->get('MBO_Client')['Id'];

            $add_client_to_class_result = $this->add_client_to_class($_REQUEST['classID']);

            $template_data = array(
                'type'      => $add_client_to_class_result['type'],
                'message'   => $add_client_to_class_result['message'],
                'nonce'     => $_REQUEST['nonce'],
                'siteID'    => $_REQUEST['siteID'],
                'location'  => $_REQUEST['location']
            );


            // Debug logging
            // $client = NS\MZMBO()->session->get('MBO_Client');
            // $debug_data = [
            //     'mbo_guid' => NS\MZMBO()->session->get('mbo_guid'),
            //     'client' => $client['FirstName'] . ' ' . $client['LastName'] . ' (' . $client['Id'] . ')',
            //     'nonce'     => $_REQUEST['nonce'],
            //     'message'   => $add_client_to_class_result['message'],
            //     'class_id' => $_REQUEST['classID']
            // ];
            // NS\MZMBO()->helpers->log(array($this->clientID => $debug_data));

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
    private function ajax_add_client_to_class($classID) {

        $result = array();

        $additions = array();

        $additions['ClassIDs'] = array($classID);

        $additions['ClientIDs'] = array($this->clientID);

        $additions['SendEmail'] = true;

        $additions['RequirePayment'] = false;

        // Crate the MBO Object
        $this->get_mbo_results();

        $signupData = $this->mb->AddClientsToClasses($additions);

        if ((isset(NS\Inc\Core\MZ_Mindbody_Api::$advanced_options['log_api_calls'])) && (NS\Inc\Core\MZ_Mindbody_Api::$advanced_options['log_api_calls'] == 'on')):
            // Debug logging on if we have also enabled log_api_calls
            $debug_data = [
                'mbo_guid' => NS\MZMBO()->session->get('mbo_guid'),
                'additions' => $additions,
                'signupData'   => $signupData
            ];
            NS\MZMBO()->helpers->log(array($this->clientID => $debug_data));
        endif;

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
    public function ajax_generate_mbo_signup_form(){

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

                $requiredFieldsInputs .= "<label for='$field'>{$field}</label> <input type='text' name='data[Client][$field]' id='$field' required /><br />";

            }
        }

        $global_strings = NS\MZMBO()->i18n->get();

        $template_loader = new Core\Template_Loader();

        $this->template_data = array(
            'password' => $global_strings['password'],
            'username' => $global_strings['username'],
            'antispam' => __('Leave this empty-slash-blank', 'mz-mindbody-api'),
            'clientemail' => __('Email', 'mz-mindbody-api'),
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
    public function ajax_create_mbo_account(){

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

            if (isset($params['data']['Client']['BirthDate'])) {
                $params['data']['Client']['BirthDate'] = date('c', strtotime($params['data']['Client']['BirthDate']));
            }

            $options = array(
                'Clients' => array(
                    'Client' => $params['data']['Client']
                )
            );
            
            //NS\MZMBO()->helpers->mz_pr($options);
            //$options = $this->mb->FunctionDataXml($options);
            $signupData = $this->mb->AddOrUpdateClients($options);
			
            // echo $this->mb->debug();

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

                foreach ($signupData['AddOrUpdateClientsResult']['Clients']['Client']['Messages']['string'] as $message):
                    echo '<h3>' . $message . '</h3>';
                endforeach;

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
    public function ajax_display_client_schedule(){

        check_ajax_referer($_REQUEST['nonce'], "mz_display_client_schedule", false);

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
    private function ajax_get_client_schedule() {

        $result = array();

        $additions = array();

        $start_date = new \Datetime( '@' . current_time( 'timestamp' ));

        $end_date = new \Datetime( '@' . current_time( 'timestamp' ));

        $di = new \DateInterval('P4W');

        $end_date->add($di);

        $additions['StartDate'] = $start_date->format('Y-m-d');

        $additions['EndDate'] = $end_date->format('Y-m-d');

        $this->clientID = NS\MZMBO()->session->get('MBO_Client')['Id'];

        $additions['ClientID'] = $this->clientID;

        // Crate the MBO Object
        $this->get_mbo_results();

        if ( !$this->mb || $this->mb == 'NO_API_SERVICE' ) return false;

        $client_schedule = $this->mb->GetClientSchedule($additions);

        if ($client_schedule['GetClientScheduleResult']['Status'] != 'Success'):
            $result['message'] = array(NS\MZMBO()->i18n->get('result_error'),
                $schedule_data['GetClientScheduleResult']['Status'],
                $schedule_data['GetClientScheduleResult']);
            $result['message'] = NS\MZMBO()->session->get('MBO_Client')['Id'];
        else:
            $result['message'] = $this->sort_classes_by_date_then_time($client_schedule);
        endif;

        $result['type'] = "success";

        return $result;

    }

   
    /**
     * Check Client Logged In
     *
     * Function run by ajax to continually check if client is logged in
     */
    public function ajax_check_client_logged(){

        check_ajax_referer($_REQUEST['nonce'], "mz_check_client_logged", false);
        		
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

}