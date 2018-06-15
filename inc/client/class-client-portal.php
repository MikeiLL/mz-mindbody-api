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

    }

    /**
     * Client Log In
     */
    public function client_log_in(){

        if(!empty($_POST)) {

            $validateLogin = $this->mb->ValidateLogin(array(
                'Username' => $_POST['username'],
                'Password' => $_POST['password']
            ));

            if(!empty($validateLogin['ValidateLoginResult']['GUID'])) {

                $_SESSION['MBO_GUID'] = $validateLogin['ValidateLoginResult']['GUID'];
                $_SESSION['MBO_Client'] = $validateLogin['ValidateLoginResult']['Client'];

            } else {

                if ( !empty($validateLogin['ValidateLoginResult']['Message'] ) ) {

                    echo $validateLogin['ValidateLoginResult']['Message'];

                } else {

                    return __('Invalid Login', 'mz-mindbody-api') . '<br/>';

                }

                return $this->login_form();
            }

        } else if(empty($_SESSION['MBO_GUID'])) {

            return $this->login_form();

        } else {

            return $this->welcome_message($welcome);
        }

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

            mz_pr( $this->login_form() );

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
     * Display Login Form
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
            'or' => $global_strings['or']
        );

        $template_loader->set_template_data($this->template_data);
        $template_loader->get_template_part('login_form');

        return ob_get_clean();
    }

    /**
     * Display Login Form
     */
    public function signup_form(){

    }

    /**
     * Display Login Form
     */
    public function welcome_message(){

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
}