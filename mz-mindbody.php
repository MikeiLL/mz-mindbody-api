<?php
/**
 * This file contains main plugin class and, defines and plugin loader.
 *
 * The mZoo Mindbody Interface plugin utilizes the Devin Crossman Mindbody API
 * to interface with mindbody's SOAP API. This particular file is responsible for
 * including the necessary dependencies and starting the plugin.
 *
 *
 * @link              http://mzoo.org
 * @since             1.0.0
 * @package           MZ_Mindbody
 *
 * @wordpress-plugin
 * Plugin Name: 	mZoo Mindbody Interface - Schedule, Events, Staff Display
 * Description: 	Interface Wordpress with MindbodyOnline data with Bootstrap Responsive Layout.
 * Version: 		2.5.0
 * Stable tag:      2.5.0
 * Tested up to:    4.9.8
 * Requires PHP:    5.6
 * Author: 			mZoo.org
 * Author URI: 		http://www.mZoo.org/
 * Plugin URI: 		http://www.mzoo.org/mz-mindbody-wp
 * License:         GPL-2.0+
 * License URI:     http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: 	mz-mindbody-api
 * Domain Path: 	/languages
*/

namespace MZ_Mindbody;

use MZ_Mindbody\Inc\Core as Core;
use MZ_Mindbody\Inc\Common as Common;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Constants
 */

define( __NAMESPACE__ . '\NS', __NAMESPACE__ . '\\' );

define( NS . 'PLUGIN_NAME', 'mz-mindbody-api' );

define( NS . 'PLUGIN_VERSION', '2.4.7' );

define( NS . 'PLUGIN_NAME_DIR', plugin_dir_path( __FILE__ ) );

define( NS . 'PLUGIN_NAME_URL', plugin_dir_url( __FILE__ ) );

define( NS . 'PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

define( NS . 'PLUGIN_TEXT_DOMAIN', 'mz-mindbody-api' );


/**
 * Autoload Classes
 */

require_once( PLUGIN_NAME_DIR . 'inc/libraries/autoloader.php' );

/**
 * Register Activation and Deactivation Hooks
 * This action is documented in inc/core/class-activator.php
 */

register_activation_hook( __FILE__, array( NS . 'Inc\Core\Activator', 'activate' ) );

/**
 * The code that runs during plugin deactivation.
 * This action is documented inc/core/class-deactivator.php
 */

register_deactivation_hook( __FILE__, array( NS . 'Inc\Core\Deactivator', 'deactivate' ) );

/**
 * Plugin Singleton Container
 *
 * Maintains a single copy of the plugin app object
 *
 * @since    2.4.7
 */
class MZ_Mindbody {

	/**
	 * The instance of the plugin.
	 *
	 * @since    2.4.7
	 * @var      Init $init Instance of the plugin.
	 */
	private static $instance;

    /**
     * Main MZ_Mindbody Instance.
     *
     * Insures that only one instance of MZ_Mindbody exists in memory at any one
     * time. Also prevents needing to define globals all over the place.
     *
     * Totally borrowed from Easy_Digital_Downloads, and certainly used with some ignorance
     * as EDD doesn't actually include a construct in it's class.
     *
     * @since 2.4.7
     * @static
     * @staticvar array $instance
     * @see MZMBO()
     * @return object|MZ_Mindbody The one true MZ_Mindbody
     */
	public static function instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof MZ_Mindbody_Api ) ) {
			self::$instance = new Inc\Core\MZ_Mindbody_Api;
			self::$instance->run();

            self::$instance->session        = new Core\MZMBO_Session();
            self::$instance->i18n           = new Common\Global_Strings();
            self::$instance->helpers        = new Common\Helpers();
		}

		return self::$instance;
	}

}

/**
 * Begins execution of the plugin
 *
 * The main function for that returns MZ_Mindbody_Api
 *
 * The main function responsible for returning the one true MZ_Mindbody_Api
 * Instance to functions everywhere.
 *
 * Borrowed from Easy_Digital_Downloads.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $mZmbo = MZ_Mindbody\MZMBO(); ?>
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * Also returns copy of the app object so 3rd party developers
 * can interact with the plugin's hooks contained within.
 *
 * @since 1.4
 * @return object|MZ_Mindbody_Api The one true MZ_Mindbody_Api Instance.
 **/
function MZMBO() {
		return MZ_Mindbody::instance();
}

$min_php = '5.6.0';

// Check the minimum required PHP version and run the plugin.
if ( version_compare( PHP_VERSION, $min_php, '>=' ) ) {
    // Get MZ_Mindbody_Api Instance.
    MZMBO();
}
