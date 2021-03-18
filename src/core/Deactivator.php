<?php

namespace MzMindbody\Core;

use MzMindbody as NS;
use MzMindbody\Admin as Admin;

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
class Deactivator
{

    /**
     * Short Description.
     *
     * Long Description.
     *
     * @since    2.4.7
     */
    public static function deactivate()
    {
        NS\MZMBO()->helpers->clearLogFiles();
        $admin_obj = new Admin\Admin(NS\PLUGIN_NAME, NS\PLUGIN_VERSION, NS\PLUGIN_TEXT_DOMAIN);
        $admin_obj->clear_plugin_transients();
        wp_clear_scheduled_hook('fetch_mbo_access_token');
        // TODO clear out options when plugin removed
    }
}
