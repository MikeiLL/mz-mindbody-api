<?php

namespace MZ_Mindbody\Inc\Admin;

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
            'nonce' => $nonce
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
        // If version is previous to 2.4.7
        if (get_site_option( 'mz_mbo_version' ) < '2.4.7') {
            // Copy the old options to the new options
            $old_options = get_option('mz_mindbody_options');
            $mz_mbo_basic = array();
            $mz_mbo_basic['mz_source_name'] = $old_options['mz_mindbody_source_name'];
            $mz_mbo_basic['mz_mindbody_password'] = $old_options['mz_mindbody_password'];
            $mz_mbo_basic['mz_mindbody_show_sub_link'] = $old_options['mz_mindbody_show_sub_link'];
            $mz_mbo_events['mz_mindbody_siteID'] = $old_options['mz_mindbody_siteID'];
            $mz_mbo_events['mz_mindbody_eventIDs'] = $old_options['mz_mindbody_eventID'];
            $mz_mbo_events['mz_mindbody_eventsDuration'] = $old_options['mz_mindbody_eventsDuration'];
            add_option('mz_mbo_basic', $mz_mbo_basic);
            add_option('mz_mbo_events', $mz_mbo_events);
        }
    }
    
    /*
     * Clear all plugin transients 
     *
     * Called via ajax in admin
     *
     *
     * @since 2.4.7
     */
    public function clear_plugin_transients () {

        check_ajax_referer($_REQUEST['nonce'], "mz_admin_nonce", false);

        global $wpdb;
        $wpdb->query( "DELETE FROM `$wpdb->options` WHERE `option_name` LIKE '%transient_mz_mindbody%'" );
        $result['type'] = "success";
        $result['message'] = __("Transients cleared. Page reloads will re-set them.", 'mz-mindbody-api');
        		
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
