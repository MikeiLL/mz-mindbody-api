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
class Retrieve_Client extends Interfaces\Retrieve {

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
     * Client Login – using API VERSION 5!
     *
     * Since 2.5.7
     *
     * @param array $credentials with username and password
     * @return string - Welcome message or Error
     */
    public function log_client_in( $credentials = ['username' => '', 'password' => ''] ){
    
        $validateLogin = $this->validate_client($credentials);
		
		if ( !empty($validateLogin['ValidateLoginResult']['GUID']) ) {
			if ( $this->create_client_session( $validateLogin ) ) {
				return __('Welcome', 'mz-mindbody-api') . ', ' . $validateLogin['ValidateLoginResult']['Client']['FirstName'] . '.<br/>';
			}
			return sprintf(__('Whoops. Please try again, %1$s.', 'mz-mindbody-api'),
            					$validateLogin['ValidateLoginResult']['Client']['FirstName']);
		} else {
			// Otherwise error message
			if ( !empty($validateLogin['ValidateLoginResult']['Message'] ) ) {

				return $validateLogin['ValidateLoginResult']['Message'];

			} else {
				// Default fallback message.
				return __('Invalid Login', 'mz-mindbody-api') . '<br/>';

			}
		}
	}
	
	
    /**
     * Validate Client - API VERSION 5!
     *
     * Since 2.5.7
     *
     * @param $validateLoginResult array with result from MBO API
     */
    public function validate_client( $validateLoginResult ){
		
		// Create the MBO Object using API VERSION 5!
        $this->get_mbo_results(5);

		return $this->mb->ValidateLogin(array(
			'Username' => $validateLoginResult['Username'],
			'Password' => $validateLoginResult['Password']
		));

    }
    

    /**
     * Create Client Session
     *
     * Since 2.5.7
     *
     * @param $validateLoginResult array with MBO result
     */
    public function create_client_session( $validateLoginResult ){
		
		if (!empty($validateLoginResult['ValidateLoginResult']['GUID'])) {

			// If validated, create two session variables and store

			NS\MZMBO()->session->set( 'MBO_GUID', $validateLoginResult['ValidateLoginResult']['GUID'] );
			NS\MZMBO()->session->set( 'MBO_Client', $validateLoginResult['ValidateLoginResult']['Client'] );

			return true;

		} 

    }

    /**
     * Client Log Out
     */
    public function client_log_out(){

        NS\MZMBO()->session->clear();

        return true;
    }

    /**
     * Register for Class
     *
     * This is the endpoint when client confirms they want to sign-up for a class.
     */
    public function register_for_class(){

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
     * Return MBO Account config required fields with what I think are default required fields.
     *
     * since: 2.5.7
     *
     * return array numeric array of required fields
     */
    public function get_signup_form_fields(){

        // Crate the MBO Object
        $this->get_mbo_results();

        $requiredFields = $this->mb->GetRequiredClientFields();
        
        $default_required_fields = [
        	"Email",
        	"FirstName",
        	"LastName"
        ];
        
        return array_merge($default_required_fields, $requiredFields['RequiredClientFields']);
    }

    /**
     * Create MBO Account
     */
    public function add_client( $client_fields = array() ){

        // Crate the MBO Object
        $this->get_mbo_results();

		$signup_result = $this->mb->AddClient($client_fields);
		
		return $signup_result;
    
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
     * Display Client Schedule
     *
     */
    public function display_client_schedule(){

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
    private function get_client_schedule() {

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
        if (is_array($client_schedule['GetClientScheduleResult']['Visits']['Visit'][0])){
            // Multiple visits
            $visit_array_scope = $client_schedule['GetClientScheduleResult']['Visits']['Visit'];
        } else {
            $visit_array_scope = $client_schedule['GetClientScheduleResult']['Visits'];
        }


        foreach($visit_array_scope as $visit)
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
     * Since 2.5.7
     * Is there a session containing the MBO_GUID of current user
     *
     * @return bool
     */
    public function check_client_logged(){

        return ( 1 == (bool) NS\MZMBO()->session->get('MBO_GUID') ) ? 1 : 0;
        
    }

    /**
     * Get a timestamp, return data from MBO api, store it in a transient and
     * as object attribute.
     *
     * @since 2.4.7
     *
     * @param $api_version int in case we need to call on API v5 as in for client login
     *
     *
     * @return array of MBO schedule data
     */
    public function get_mbo_results( $api_version = 6 ){
		
		if ( $api_version == 6 ) {
        	$this->mb = $this->instantiate_mbo_API();
		} else {
			$this->mb = $this->instantiate_mbo_API( 5 );
		}
		
        if ( !$this->mb || $this->mb == 'NO_API_SERVICE' ) return false;

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