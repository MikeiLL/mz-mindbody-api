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
     */
    private $mb;

    /**
     * Template Date for sending to template partials
     */
    public $template_data;

    /**
     * Check if Client Logged In
     */
    private function check_client_logged(){

        if ( empty($_SESSION['MBO_GUID']) ) {

            return false;

        }

        return true;
    }

    /**
     * Create MBO Account
     */
    public function create_mbo_account(){
        $requiredFields = $this->mb->GetRequiredClientFields();

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

        if(!empty($signupData['AddOrUpdateClientsResult']['Clients']['Client']['Action']) && $signupData['AddOrUpdateClientsResult']['Clients']['Client']['Action'] == 'Failed' && !empty($signupData['AddOrUpdateClientsResult']['Clients']['Client']['Messages'])) {

            foreach($signupData['AddOrUpdateClientsResult']['Clients']['Client']['Messages'] as $message) {

                echo "<pre>".print_r($message,1).'</pre><br />';

            }
        }


        $globals = new Common\Global_Strings();

        $global_strings = $globals->translate_them();

        $password = $global_strings['password'];

        $username = $global_strings['username'];

        $antispam = __('Leave this empty-slash-blank', 'mz-mindbody-api');

        $firstname = __('First Name', 'mz-mindbody-api');

        $lastname = __('Last Name', 'mz-mindbody-api');

        $sign_up = __('Sign up', 'mz-mindbody-api');

        return <<<EOD
	<form  class="mz_mbo_signup" method="POST">
		<p class="website_url" style="display:none">$antispam<input type="text" name="website_url" /></p>
		<label for="Username"> $username</label><input type="text" name="data[Client][Username]" id="Username" placeholder="$username" required /><br />
		<br/><label for="Password"> $password</label><input type="password" name="data[Client][Password]" id="Password" placeholder="$password" required /><br />
		<br/><label for="FirstName"> $firstname</label><input type="text" name="data[Client][FirstName]" id="FirstName" placeholder="$firstname" required /><br />
		<br/><label for="LastName"> $lastname</label><input type="text" name="data[Client][LastName]" id="LastName" placeholder="$lastname" required /><br />
		$requiredFieldsInputs
		<button type="submit">$sign_up</button>
	</form>
EOD;
    }

    /**
     * Client Log In
     */
    public function client_log_in(){

        // Crate the MBO Object
        $this->get_mbo_results();

        ob_start();
        //
        $result['type'] = 'success';

        // Parse the serialized form into an array.
        $params = array();
        parse_str($_REQUEST['form'], $params);

        if(!empty($params)) {
//
            $validateLogin = $this->mb->ValidateLogin(array(
                'Username' => $params['username'],
                'Password' => $params['password']
            ));
//
            if(!empty($validateLogin['ValidateLoginResult']['GUID'])) {
//
                $_SESSION['MBO_GUID'] = $validateLogin['ValidateLoginResult']['GUID'];
                $_SESSION['MBO_Client'] = $validateLogin['ValidateLoginResult']['Client'];
//
            } else {
//
                if ( !empty($validateLogin['ValidateLoginResult']['Message'] ) ) {
//
                    echo $validateLogin['ValidateLoginResult']['Message'];
//
                } else {
//
                    _e('Invalid Login', 'mz-mindbody-api') . '<br/>';
//
                }
//
                echo $this->login_form();
            }
//
        } else {
//
            echo $this->welcome_message('welcome');
            mz_pr($_SESSION);
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

    }
    
    /**
     * Register for Class
     */
    public function register_for_class(){

        check_ajax_referer($_REQUEST['nonce'], "mz_signup_nonce", false);

        // Crate the MBO Object
        $this->get_mbo_results();

        ob_start();

        $result['type'] = 'success';
        // $template_loader = new Core\Template_Loader();

        // $template_loader->set_template_data($this->template_data);
        // $template_loader->get_template_part('staff_list');

        if ($this->check_client_logged === true) {

            echo 'you are logged in.';

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
            'nonce' => $_REQUEST['nonce']
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