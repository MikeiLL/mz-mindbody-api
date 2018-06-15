<?php
namespace MZ_Mindbody\Inc\Client;

use MZ_Mindbody\Inc\Core as Core;
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
     * Check if Client Logged In
     */
    private function check_client_logged(){

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

        ob_start();

        $result['type'] = 'success';
        // $template_loader = new Core\Template_Loader();

        // $template_loader->set_template_data($this->template_data);
        // $template_loader->get_template_part('staff_list');

        echo "You love Rivka.";

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

        $mb = $this->instantiate_mbo_API();

        if ( !$mb || $mb == 'NO_SOAP_SERVICE' ) return false;



        return true;
    }
}