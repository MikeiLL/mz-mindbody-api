<?php
namespace MZoo\MzMindbody\Client;

use MZoo\MzMindbody\Core as Core;
use MZoo\MzMindbody\Common as Common;
use MZoo\MzMindbody\Libraries as Libraries;
use MZoo\MzMindbody\Schedule as Schedule;
use MZoo\MzMindbody\Common\Interfaces as Interfaces;
use MZoo\MzMindbody as NS;

/*
 * Class that holds Client Interface Methods for Ajax requests
 *
 *
 */
class ClientPortal extends RetrieveClient {

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
     * Invalid Nonce Feedback
     *
     * @since 1.0.5
     * @var string $this->invalid_nonce_feedback Text to display when nonce is invalid.
     */
    private $invalid_nonce_feedback = 'Invalid nonce. Try reloading the page.';


    /**
     * Class constructor
     *
     * Since 2.4.7
     */
    public function __construct(){
        $this->date_format = Core\MzMindbodyApi::$date_format;
        $this->time_format = Core\MzMindbodyApi::$time_format;
    }

    /**
     * Client Log In
     */
    public function ajax_client_login(){

        check_ajax_referer($_REQUEST['nonce'], "mz_mbo_api", false);

        // Create the MBO Object
        $this->get_mbo_results();

        // Init message
        $result['message'] = '';

        $result['type'] = 'success';

        // Parse the serialized form into an array.
        $params = array();
        parse_str($_REQUEST['form'], $params);

        if (empty($params) || !is_array($params)) {

            $result['type'] = 'error';

        } else {

            $credentials = ['Username' => $params['email'], 'Password' => $params['password']];

            $login = $this->log_client_in($credentials);

            if ( $login['type'] == 'error' ) $result['type'] = 'error';

            $result['message'] = $login['message'];

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
    public function ajax_client_logout(){

        check_ajax_referer($_REQUEST['nonce'], "mz_mbo_api", false);

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
    public function ajax_add_client_to_class($classID) {

        if ( isset( $_GET['nonce'] ) ) {
            $nonce = wp_unslash( $_GET['nonce'] );
        } else {
            \wp_send_json_error( 'Missing nonce' );
            \wp_die();
        }

        if ( ! \wp_verify_nonce( $nonce, 'mz_mbo_api' ) ) {
            \wp_send_json_error( $this->invalid_nonce_feedback );
            \wp_die();
        }

        $this->clientID = $_SESSION['MindbodyAuth']['MBO_USER_StudioProfile_ID'];

        $result = array();

        $signupData = $this->add_client_to_class($this->clientID, $_GET['classID']);

        if ((isset(NS\Core\MzMindbodyApi::$advanced_options['log_api_calls']))
        && (NS\Core\MzMindbodyApi::$advanced_options['log_api_calls'] == 'on')):
            // Debug logging on if we have also enabled log_api_calls
            $debug_data = [
                'mbo_client' => $_SESSION['MindbodyAuth']['MBO_Client'],
                'additions' => $additions,
                'signupData'   => $signupData
            ];
        endif;

        if ( isset($signupData['Error']) ) {
            \wp_send_json_error( $signupData['Error']['Message'] . ' Code: ' . $signupData['Error']['Code'] );
            \wp_die();
        }

        if ( $signupData['Visit']['ClassId'] != $_GET['classID'] ) {
            \wp_send_json_error( "Something wasn't quite right." . print_r($signupData, true) );
        } else {
            \wp_send_json_success( __('Registered via MindBody', 'mz-mindbody-api') );
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
                $requiredFieldsInputs .= "<input type='hidden' name='mz_mbo_action' value='true' />"; // Add our identifier for this plugin.

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
        check_ajax_referer($_REQUEST['nonce'], "mz_mbo_api", false);

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

            $signupData = $this->mb->AddClient($params['data']['Client']);
            $_SESSION['MindbodyAuth']['MBO_USER_StudioProfile_ID'] = $signupData['Client']['Id'];

            // echo $this->mb->debug();

            if(array_key_exists('Client', $signupData)) {

                echo '<h3>' . __('Congratulations. You are now logged in with your new Mindbody account.', 'mz-mindbody-api') . '</h3>';

                /* $validateLogin = $this->mb->ValidateLogin(array(
                    'Username' => $params['data']['Client']['Username'],
                    'Password' => $params['data']['Client']['Password']
                ));

                if ( !empty($validateLogin['ValidateLoginResult']['GUID']) ) {

                    NS\MZMBO()->session->set('MBO_Client', $validateLogin['ValidateLoginResult']['Client']);

                    echo '<h3>' . __('Congratulations. You are now logged in with your new Mindbody account.', 'mz-mindbody-api') . '</h3>';

                    echo '<div class="mz_signup_welcome">' . __('Sign-up for some classes.', 'mz-mindbody-api') . '</div>';

                } else {
                    NS\MZMBO()->helpers->print($validateLogin);
                } */

            } else { /* if ($signupData['AddOrUpdateClientsResult']['Clients']['Client']['Action'] == 'Failed'){ */

                echo '<h3>' . __('There was an error creating your account.', 'mz-mindbody-api') . '</h3>';
                echo "<pre>";
                print_r($signupData);
                echo "</pre>";

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

        if ( isset( $_GET['nonce'] ) ) {
            $nonce = wp_unslash( $_GET['nonce'] );
        } else {
            \wp_send_json_error( 'Missing nonce' );
            \wp_die();
        }

        if ( ! \wp_verify_nonce( $nonce, 'mz_mbo_api' ) ) {
            \wp_send_json_error( $this->invalid_nonce_feedback );
            \wp_die();
        }

        ob_start();

        if ( ( isset($_SESSION['MindbodyAuth']['MBO_USER_StudioProfile_ID']) ) ) {

            $clientId = $_SESSION['MindbodyAuth']['MBO_USER_StudioProfile_ID'];
            $schedule = $this->get_client_schedule($clientId);
            if ( !isset($schedule['Visits']) ) {
                \wp_send_json_error( 'No schedule found' );
                \wp_die();
            }
            \wp_send_json_success( $schedule['Visits'] );
            \wp_die();

            /* $template_data = array(
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
            //NS\MZMBO()->helpers->print($this->get_client_schedule()['message']); */

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

        \wp_send_json_success( ['message'] );
        \wp_die();
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

        $this->clientID = $_SESSION['MindbodyAuth']['MBO_Client']['Id'];

        $additions['ClientID'] = $this->clientID;

        // Crate the MBO Object
        $this->get_mbo_results();

        if ( !$this->mb || $this->mb == 'NO_API_SERVICE' ) return false;

        $client_schedule = $this->mb->GetClientSchedule(
            array(
                'clientId' => $id,
                'EndDate'  => \wp_date( 'Y-m-d', \strtotime( '+1 month' ) ),
            )
        );

        if ($client_schedule['GetClientScheduleResult']['Status'] != 'Success'):
            $result['message'] = array(NS\MZMBO()->i18n->get('result_error'),
                $schedule_data['GetClientScheduleResult']['Status'],
                $schedule_data['GetClientScheduleResult']);
            $result['message'] = $_SESSION['MindbodyAuth']['MBO_Client']['Id'];
        else:
            $result['message'] = $this->sort_classes_by_date_then_time($client_schedule);
        endif;

        $result['type'] = "success";

        return $result;
        \wp_send_json_success( $result );
        \wp_die();

    }


    /**
     * Check Client Logged In
     *
     * Function run by ajax to continually check if client is logged in
     */
    public function ajax_check_client_logged(){

        check_ajax_referer($_REQUEST['nonce'], "mz_mbo_api", false);

        $result = array();

        $result['type'] = 'success';
        $result['message'] =  $this->check_client_logged();

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $result = json_encode($result);
            echo $result;
        } else {
            header("Location: " . $_SERVER["HTTP_REFERER"]);
        }

        die();
    }

}
