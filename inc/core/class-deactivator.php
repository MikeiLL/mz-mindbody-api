<?php

namespace MZ_Mindbody\Inc\Core;

use MZ_Mindbody as NS;
use MZ_Mindbody\Inc\Admin as Admin;

/**
 * Fired during plugin deactivation
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @link       http://mzoo.org
 * @since      1.0.0
 *
 * @author     Mike iLL/mZoo.org
 **/
class Deactivator {

	/**
	 * Short Description.
	 *
	 * Long Description.
	 *
	 * @since    2.4.7
	 */
	public static function deactivate() {
        NS\MZMBO()->helpers->clear_log_files();
        $admin_obj = new Admin\Admin(NS\PLUGIN_NAME, NS\PLUGIN_VERSION, NS\PLUGIN_TEXT_DOMAIN);
        $admin_obj->clear_plugin_transients();
	}

}
