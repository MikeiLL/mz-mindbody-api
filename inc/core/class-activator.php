<?php

namespace MZ_Mindbody\Inc\Core;

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
	 * Short Description.
	 *
	 * Long Description.
	 *
	 * @since    2.4.7
	 */
	public static function activate() {

			$min_php = '5.6.0';

		// Check PHP Version and deactivate & die if it doesn't meet minimum requirements.
		if ( version_compare( PHP_VERSION, $min_php, '<' ) ) {
			deactivate_plugins( plugin_basename( __FILE__ ) );
			wp_die( 'This plugin requires a minmum PHP Version of ' . $min_php );
		}
		
		// Automatically create option to track api calls
		$mz_mbo_api_calls = array();
		$mz_mbo_api_calls['today'] = date("Y-m-d");
		$mz_mbo_api_calls['calls'] = 1;
		add_option('mz_mbo_api_calls', $mz_mbo_api_calls);
	}

}
