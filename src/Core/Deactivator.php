<?php
/**
 * Deactivator class
 *
 * Methods to run on plugin deactivation.
 *
 * @package MzMindbody
 */

namespace MZoo\MzMindbody\Core;

use MZoo\MzMindbody as NS;
use MZoo\MzMindbody\Admin as Admin;

/**
 * Fired during plugin deactivation
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @link  http://mzoo.org
 * @since 1.0.0
 *
 * @author Mike iLL/mZoo.org
 **/
class Deactivator {

    /**
     * Short Description.
     *
     * Long Description.
     *
     * @since 2.4.7
     */
    public static function deactivate() {
        NS\MZMBO()->helpers->clear_log_files();
        $admin_obj = new Admin\Admin( NS\PLUGIN_NAME, NS\PLUGIN_VERSION, 'mz-mindbody-api' );
        $admin_obj->clear_plugin_transients();
        wp_clear_scheduled_hook( 'fetch_mbo_access_token' );
        wp_clear_scheduled_hook( 'mz_mbo_api_alert_cron' );
        delete_option( 'mz_mbo_token' );
        // TODO clear out options when plugin removed.
    }
}
