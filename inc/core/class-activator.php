<?php

namespace MZ_Mindbody\Inc\Core;
use MZ_Mindbody as NS;
use MZ_Mindbody\Inc\Admin as Admin;

/**
 * Fired during plugin activation
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @link       http://mzoo.org
 * @since      1.0.0
 *
 * @author     Mike iLL/mZoo.org
 **/
class Activator {

	/**
	 * Run when plugin is activated.
	 *
	 * 
	 *
	 * @since    2.4.7
	 */
	public static function activate() {
		// Check PHP Version and deactivate & die if it doesn't meet minimum requirements.
		if ( version_compare( PHP_VERSION, NS\MINIMUM_PHP_VERSION, '<' ) ) {
			$admin_object = new Admin\Admin(NS\PLUGIN_NAME, NS\PLUGIN_VERSION, NS\PLUGIN_TEXT_DOMAIN);
			add_action('admin_notices', array($admin_object, 'admin_notice'));
			deactivate_plugins( NS\PLUGIN_BASENAME );
			return;
		}
		
		// Automatically create option to track api calls
		$mz_mbo_api_calls = array();
		$mz_mbo_api_calls['today'] = date("Y-m-d");
		$mz_mbo_api_calls['calls'] = 2;
		update_option('mz_mbo_api_calls', $mz_mbo_api_calls);
		
		$advanced_options = get_option('mz_mbo_advanced');

		// Set default advanced options
		// If completely empty set all
		if (empty($advanced_options)) {
			$advanced_options = array(
				'date_format' => 'l, F j',
				'time_format' => 'g:i a',
				'api_call_limit' => 2000,
				'elect_display_substitutes' => 'on',
				'log_api_calls' => 'off',
				'schedule_transient_duration' => 43200
			);
		} 
		
		if (!empty($advanced_options) && empty($advanced_options['api_call_limit'])){
			// Maybe it's been installed pre v2.5.7
			$advanced_options['api_call_limit'] = 2000;
			// Maybe it's been installed pre v2.6.7
			$advanced_options['schedule_transient_duration'] = 43200;
		}
		update_option('mz_mbo_advanced', $advanced_options );
		
		if (! wp_next_scheduled ( 'fetch_mbo_access_token')) {
			wp_schedule_event( time(), 'hourly', 'fetch_mbo_access_token');
		}
	}
	

	

}
