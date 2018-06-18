<?php
namespace MZ_Mindbody\Inc\Client;

use MZ_Mindbody\Inc\Core as Core;
use MZ_Mindbody\Inc\Common as Common;
use MZ_Mindbody\Inc\Libraries as Libraries;
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
     * Check if Client Logged In
     *
     * If MBO_GUID is set in the session, validate it and return true if valid,
     * otherwise return false.
     *
     * @access private
     */
    private function check_client_logged(){

        if ( empty($_SESSION['MBO_GUID']) ) {

            return false;

        }

        return true;

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

        $globals = new Common\Global_Strings();

        $global_strings = $globals->translated_strings();

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

        //if(!empty($signupData['AddOrUpdateClientsResult']['Clients']['Client']['Action']) && $signupData['AddOrUpdateClientsResult']['Clients']['Client']['Action'] == 'Failed' && !empty($signupData['AddOrUpdateClientsResult']['Clients']['Client']['Messages'])) {
        //    foreach($signupData['AddOrUpdateClientsResult']['Clients']['Client']['Messages'] as $message) {
        //        echo "<pre>".print_r($message,1).'</pre><br />';
        //    }
        //}
        // Crate the MBO Object
        $this->get_mbo_results();

        $result['type'] = 'success';

        ob_start();


        //$template_loader = new Core\Template_Loader();
        //$this->template_data = array(
        //    'password' => $global_strings['password'],
        //    'username' => $global_strings['username'],
        //    'antispam' => __('Leave this empty-slash-blank', 'mz-mindbody-api'),
        //    'firstname' => __('First Name', 'mz-mindbody-api'),
        //    'lastname' => __('Last Name', 'mz-mindbody-api'),
        //    'sign_up' => __('Sign up', 'mz-mindbody-api'),
        //    'requiredFieldsInputs' => $requiredFieldsInputs
        //);
        //$template_loader->set_template_data($this->template_data);
        //$template_loader->get_template_part('create_mbo_account');

        // Parse the serialized form into an array.
        $params = array();
        parse_str($_REQUEST['form'], $params);

        if(!empty($params['website_url'])){
            // This is a robot
            die();
        }

        if(!empty($params['data']['Client'])) {

            //mz_pr($_POST['data']['Client']['MobilePhone']);
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

                    $_SESSION['MBO_GUID'] = $validateLogin['ValidateLoginResult']['GUID'];

                    $_SESSION['MBO_Client'] = $validateLogin['ValidateLoginResult']['Client'];

                    echo session_id();
                    var_dump($_SESSION);

                    echo '<h3>' . __('Congratulations. You are now logged in with your new Mindbody account.', 'mz-mindbody-api') . '</h3>';

                    echo '<div class="mz_signup_welcome">' . __('Sign-up for some classes.', 'mz-mindbody-api') . '</div>';

                } else {
                    mz_pr($validateLogin);
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
     * Client Log In
     */
    public function client_log_in(){

        check_ajax_referer($_REQUEST['nonce'], "mz_signup_nonce", false);

        // Crate the MBO Object
        $this->get_mbo_results();

        ob_start();
        //
        $result['type'] = 'success';

        // Parse the serialized form into an array.
        $params = array();
        parse_str($_REQUEST['form'], $params);

        if(!empty($params)) {

            $validateLogin = $this->mb->ValidateLogin(array(
                'Username' => $params['username'],
                'Password' => $params['password']
            ));

            if(!empty($validateLogin['ValidateLoginResult']['GUID'])) {

                $_SESSION['MBO_GUID'] = $validateLogin['ValidateLoginResult']['GUID'];

                $_SESSION['MBO_Client'] = $validateLogin['ValidateLoginResult']['Client'];

            } else {

                if ( !empty($validateLogin['ValidateLoginResult']['Message'] ) ) {

                    echo $validateLogin['ValidateLoginResult']['Message'];

                } else {

                    _e('Invalid Login', 'mz-mindbody-api') . '<br/>';

                }

                echo $this->login_form();
            }

        } else {

            echo $this->welcome_message('You are logged into MindBodyOnline.');

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

        unset($_SESSION['MBO_GUID']);
        unset($_SESSION['MBO_Client']);

        ob_start();
        //
        $result['type'] = 'success';

        _e('Logged Out', 'mz-mindbody-api');

        echo '<br/>';

        echo $this->login_form();

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
     */
    public function register_for_class(){

        check_ajax_referer($_REQUEST['nonce'], "mz_signup_nonce", false);

        ob_start();

        $result['type'] = 'success';

        if ($this->check_client_logged() === true) {

            $this->clientID = $_SESSION['MBO_Client']['ID'];
            echo $this->add_client_to_class($_REQUEST['classID'])['message'];
            // echo $this->mb->debug();
            $template_loader = new Core\Template_Loader();

            $template_loader->set_template_data($this->template_data);
            $template_loader->get_template_part('added_to_class');

        } else {

            echo $this->login_form();

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

        //$rand_number = rand(1, 10); # for testing

        if ( $signupData['AddClientsToClassesResult']['ErrorCode'] != 200 ) {
            $result['type'] = "error";
            $result['message'] = '';

            if (!isset($signupData['AddClientsToClassesResult']['Classes']['Class']['Clients']['Client'])) :

                if (function_exists(mZ_write_to_file)) {
                    //mZ_write_to_file($signupData['AddClientsToClassesResult']['ErrorCode']);
                }
                $result['type'] = "error";

            else:

                foreach ($signupData['AddClientsToClassesResult']['Classes']['Class']['Clients']['Client']['Messages'] as $message){
                    if (strpos($message, 'already booked') != false){
                        $result['message'] .= __('Already registered.', 'mz-mindbody-api');
                    }else{
                        $result['message'] .= $message;
                    }
                }

            endif;

        }else{
            $result['type'] = "success";
            $result['message'] = __('Registered via MindBody', 'mz-mindbody-api');
        }

       return $result;

    }

    /**
     * Create Login Form
     */
    public function login_form(){
        ob_start();

        $globals = new Common\Global_Strings;

        $global_strings = $globals->translated_strings();

        $template_loader = new Core\Template_Loader();

        $this->template_data = array(
            'password' => $global_strings['password'],
            'username' => $global_strings['username'],
            'login' => $global_strings['login'],
            'registration_button' => __('Register with MindBodyOnline', 'mz-mindbody-api'),
            'or' => $global_strings['or'],
            'nonce' => $_REQUEST['nonce'],
            'classID' => $_REQUEST['classID']
        );

        $template_loader->set_template_data($this->template_data);
        $template_loader->get_template_part('login_form');

        return ob_get_clean();
    }

    /**
     * Display Login Form
     */
    public function signup_form(){
        check_ajax_referer($_REQUEST['nonce'], "mz_schedule_display_nonce", false);
    }

    /**
     * Display Login Form
     */
    public function welcome_message($message = 'Welcome to MBO'){
        return $message;
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