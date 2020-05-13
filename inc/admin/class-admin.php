<?php

namespace MZ_Mindbody\Inc\Admin;

use MZ_Mindbody\Inc\Backend as Backend;

use MZ_Mindbody as NS;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @link       http://mzoo.org
 * @since      1.0.0
 *
 * @author    Mike iLL/mZoo.org
 */
class Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    2.4.7
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    2.4.7
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The text domain of this plugin.
	 *
	 * @since    2.4.7
	 * @access   private
	 * @var      string    $plugin_text_domain    The text domain of this plugin.
	 */
	private $plugin_text_domain;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since       1.0.0
	 * @param       string $plugin_name        The name of this plugin.
	 * @param       string $version            The version of this plugin.
	 * @param       string $plugin_text_domain The text domain of this plugin.
	 */
	public function __construct( $plugin_name, $version, $plugin_text_domain ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->plugin_text_domain = $plugin_text_domain;

	}
	
	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    2.4.7
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/mz-mindbody-api-admin.css', array(), $this->version, 'all' );

	}
	
	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    2.4.7
	 */
	public function enqueue_scripts() {
                        
		wp_register_script('mz_mbo_admin_script', NS\PLUGIN_NAME_URL . 'dist/scripts/admin.js', array('jquery'), 1.0, true );
		wp_enqueue_script('mz_mbo_admin_script');
		
		$this->localizeScript();

	}

	
	/**
	 * Localize admin script.
	 *
	 * @since       2.4.7
	 *
	 */
	public function localizeScript() {
	
        $protocol = isset( $_SERVER["HTTPS"]) ? 'https://' : 'http://';
        
        $nonce = wp_create_nonce( 'mz_admin_nonce');
        
        $params = array(
            'ajaxurl' => admin_url( 'admin-ajax.php', $protocol ),
            'nonce' => $nonce,
            'spinner' => site_url('/wp-includes/js/thickbox/loadingAnimation.gif')
            );
            
        wp_localize_script( 'mz_mbo_admin_script', 'mz_mindbody_schedule', $params);
        
    }

    /**
     * Check if we are on a new version of plugin.
     *
     * @since    2.4.7
     */
    public function check_version() {
            // If not set or current version return
            if (get_site_option( 'mz_mbo_version' ) === NS\PLUGIN_VERSION) {
                return false;
            }
            // Otherwise create an option to hold it and set it.
            add_option('mz_mbo_version', NS\PLUGIN_VERSION);
            $this->mz_mbo_upgrade();
        }

    /**
     * If this is a new version of the plugin, perform actions.
     *
     * @since    2.4.7
     */
    public function mz_mbo_upgrade() {
     
    	if (false == get_site_option( 'mz_mbo_version' )) return;
    	
        // If version is previous to 2.4.7
        if (get_site_option( 'mz_mbo_version' ) < '2.4.7') {
            // Copy the old options to the new options
            $old_options = get_option('mz_mbo_basic');
            if ( !false == $old_options ){
				$mz_mbo_basic = array();
				$mz_mbo_basic['mz_source_name'] = $old_options['mz_mindbody_source_name'];
				$mz_mbo_basic['mz_mindbody_password'] = $old_options['mz_mindbody_password'];
				$mz_mbo_basic['mz_mbo_app_name'] = __('YOUR MBO APP NAME', 'mz-mindbody-api');
				$mz_mbo_basic['mz_mbo_api_key'] = __('YOUR MINDBODY API KEY', 'mz-mindbody-api');
				$mz_mbo_basic['mz_mindbody_show_sub_link'] = $old_options['mz_mindbody_show_sub_link'];
				$mz_mbo_events['mz_mindbody_siteID'] = $old_options['mz_mindbody_siteID'];
				$mz_mbo_events['mz_mindbody_eventIDs'] = $old_options['mz_mindbody_eventID'];
				$mz_mbo_events['mz_mindbody_scheduleDuration'] = $old_options['mz_mindbody_scheduleDuration'];
				update_option('mz_mbo_basic', $mz_mbo_basic);
				update_option('mz_mbo_events', $mz_mbo_events);
			}
        }
    	if (get_site_option( 'mz_mbo_version' ) < '2.5.6') {
            // Track api calls
            $mz_mbo_api_calls = array();
            $mz_mbo_api_calls['today'] = date("Y-m-d");
            $mz_mbo_api_calls['calls'] = 2;
            add_option('mz_mbo_api_calls', $mz_mbo_api_calls);
            $this->clear_previous_plugin_transients();
    	}
    	if (get_site_option( 'mz_mbo_version' ) < '2.5.7') {
            // Add options to named for v6 API
            $old_options = get_option('mz_mbo_basic');
            if ( !false == $old_options ){
				$mz_mbo_basic = array();
				$mz_mbo_basic['mz_source_name'] = $old_options['mz_source_name'];
				$mz_mbo_basic['mz_mindbody_password'] = $old_options['mz_mindbody_password'];
				$mz_mbo_basic['mz_mbo_app_name'] = __('YOUR MBO APP NAME', 'mz-mindbody-api');
				$mz_mbo_basic['mz_mbo_api_key'] = __('YOUR MINDBODY API KEY', 'mz-mindbody-api');
				$mz_mbo_basic['mz_mindbody_show_sub_link'] = $old_options['mz_mindbody_show_sub_link'];
				update_option('mz_mbo_basic', $mz_mbo_basic);
			}
    	}
    }
    
    /**
     * Call the clear all plugin transients
     *
     * Called via ajax in admin
     *
     *
     * @since 2.4.7
     */
    public function ajax_clear_plugin_transients () {

        check_ajax_referer($_REQUEST['nonce'], "mz_admin_nonce", false);

        $sql_response = $this->clear_plugin_transients();

        $result['type'] = "success";

        // Initialize message
        $result['message'] = __("No transients to clear.", 'mz-mindbody-api');

        if (false != $sql_response):
            $result['message'] = sprintf(__("Cleared %d transients. Page reloads will re-set them.", 'mz-mindbody-api'), $sql_response);
        endif;
        		
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $result = json_encode($result);
            echo $result;
        }
        else {
            header("Location: ".$_SERVER["HTTP_REFERER"]);
        }

        die();
    }

    /**
     * Clear all plugin transients
     *
     * @since 2.4.7
     *
     * @return result of $wpdb delete call.
     */
    public function clear_plugin_transients() {

        global $wpdb;
        return $wpdb->query( "DELETE FROM `$wpdb->options` WHERE `option_name` LIKE '%transient_mz_mbo%'" );
    }

    /**
     * Clear all plugin transients from versions previous to 2.4.7
     *
     * @since 2.4.7
     *
     * @return result of $wpdb delete call.
     */
    private function clear_previous_plugin_transients() {

        global $wpdb;
        return $wpdb->query( "DELETE FROM `$wpdb->options` WHERE `option_name` LIKE '%transient_mz_mindbody%'" );
    }

    /**
     * Test MBO Credentials for V6
     *
     * Called via ajax in admin
     *
     *
     * @since 2.5.7
     */
    public function test_credentials () {

        check_ajax_referer($_REQUEST['nonce'], "mz_admin_nonce", false);


        $return =  "<p>";
        $return .= sprintf(__('Once credentials have been set and activated, look for %1$s in the 
	                            second (Get Classes Response) box below to confirm settings are correct.',  'mz-mindbody-api'),
            '<code>&lt;ErrorCode&gt;200&lt;/ErrorCode&gt;</code>');
        $return .=  "</p>";
        $schedule_object = new Backend\Retrieve_Debug;
        $mb = $schedule_object->get_mbo_results();

        $result['type'] = "success";
        $result['message'] = $return . $mb;

        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $result = json_encode($result);
            echo $result;
        }
        else {
            header("Location: ".$_SERVER["HTTP_REFERER"]);
        }

        die();
    }
    
    /**
     * Test MBO Credentials for V5
     *
     * Called via ajax in admin
     *
     *
     * @since 2.4.7
     */
    public function test_credentials_v5 () {

        check_ajax_referer($_REQUEST['nonce'], "mz_admin_nonce", false);


        $return =  "<p>";
        $return .= sprintf(__('Once credentials have been set and activated, look for %1$s in the 
	                            second (Get Classes Response) box below to confirm settings are correct.',  'mz-mindbody-api'),
            '<code>&lt;ErrorCode&gt;200&lt;/ErrorCode&gt;</code>');
        $return .=  "</p>";
        $schedule_object = new Backend\Retrieve_Debug;
        $mb = $schedule_object->get_mbo_results();

        $result['type'] = "success";
        $result['message'] = $return . $mb;

        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $result = json_encode($result);
            echo $result;
        }
        else {
            header("Location: ".$_SERVER["HTTP_REFERER"]);
        }

        die();
    }


}
