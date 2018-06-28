<?php

namespace MZ_Mindbody\Inc\Common;

/**
 * Class Global_Strings
 * @package MZ_Mindbody\Inc\Common
 *
 * Store i18n strings that may be used throughout plugin
 */

class Global_Strings {

    /**
     * @var array i18n Strings we use through the plugin.
     * @access private
     */
    private $i18n_strings;

    /**
     * Global_Strings constructor.
     *
     * Assign the i18n strings array.
     * @access public
     */
    public function __construct(){

        $this->i18n_strings = array(
            'username' => __('Username', 'mz-mindbody-api'),
            'password' => __('Password', 'mz-mindbody-api'),
            'login' => __('Login', 'mz-mindbody-api'),
            'logout' => __('Log Out', 'mz-mindbody-api'),
            'sign_up' => __('Sign Up', 'mz-mindbody-api'),
            'login_to_sign_up' => __('Log in to Sign up', 'mz-mindbody-api'),
            'manage_on_mbo' => __('Manage on MindBody Site', 'mz-mindbody-api'),
            'login_url' => __('login', 'mz-mindbody-api'),
            'logout_url' => __('logout', 'mz-mindbody-api'),
            'signup_url' => __('signup', 'mz-mindbody-api'),
            'create_account_url' => __('create-account', 'mz-mindbody-api'),
            'or' => __('or', 'mz-mindbody-api'),
            'with' => __('with', 'mz-mindbody-api'),
            'day' => __('day', 'mz-mindbody-api'),
            'your_account' => __('Your Account', 'mz-mindbody-api'),
            'shortcode' => __('shortcode', 'mz-mindbody-api'),
            'result_error' => __('There was an error retrieving the data. Details below. Could be a network connection. Consider trying again.', 'mz-mindbody-api')
        );
    }



    public function get($key = false) {

        if ($key){
            $this->i18n_strings[$key];
        } else {
            return $this->i18n_strings;
        }

    }

}
?>