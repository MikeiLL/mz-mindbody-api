<?php
/**
 * This file contains main plugin class and, defines and plugin loader.
 *
 * Interface with the Mindbody Online API to display staff, schedules.
 * Use custom templates from your own theme.
 *
 * @link    http://mzoo.org
 * @since   1.0.0
 * @package MzMindbody
 *
 * @wordpress-plugin
 * Plugin Name:     mZoo Mindbody Interface - Schedule, Events, Staff Display
 * Description:     Display staff, events and class schedules from Mindbody Online. Customizable.
 * Version:         2.8.2
 * Stable tag:      2.8.2
 * Tested up to:    5.6.1
 * Requires PHP:    7.0
 * Author:          mZoo.org
 * Author URI:      http://www.mZoo.org/
 * Plugin URI:      http://www.mzoo.org/mz-mindbody-wp
 * License:         GPL-2.0+
 * License URI:     http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:     mz-mindbody-api
 * Domain Path:     /languages
 */

namespace MZoo\MzMindbody;

use MZoo\MzMindbody as NS;
use MZoo\MzMindbody\Core as Core;
use MZoo\MzMindbody\Common as Common;

/**
 * Define Constants
 *
 * TODO consider using const instead
 * see: https://stackoverflow.com/questions/18247726/php-define-constants-inside-namespace-clarification
 */

define( __NAMESPACE__ . '\NS', __NAMESPACE__ . '\\' );

define( NS . 'PLUGIN_NAME', 'mz-mindbody-api' );

define( NS . 'PLUGIN_VERSION', '2.8.2' );

define( NS . 'PLUGIN_NAME_DIR', plugin_dir_path( __FILE__ ) );

define( NS . 'PLUGIN_NAME_URL', plugin_dir_url( __FILE__ ) );

define( NS . 'PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

define( NS . 'PLUGIN_TEXT_DOMAIN', 'mz-mindbody-api' );

define( NS . 'MINIMUM_PHP_VERSION', 7.1 );

/**
 * Autoload Classes
 */
$wp_mindbody_api_autoload = NS\PLUGIN_NAME_DIR . '/vendor/autoload.php';
if ( file_exists( $wp_mindbody_api_autoload ) ) {
	include_once $wp_mindbody_api_autoload;
}

if ( ! class_exists( 'MZoo\MzMindbody\Core\MzMindbodyApi' ) ) {
	exit( 'MZ Mindbody Api requires Composer autoloading, which is not configured' );
}

/**
 * Register Activation and Deactivation Hooks
 * This action is documented in src/Core/class-activator.php
 */

register_activation_hook( __FILE__, array( NS . 'MZoo\MzMindbody\Core\Activator', 'activate' ) );

/**
 * The code that runs during plugin deactivation.
 * This action is documented src/Core/class-deactivator.php
 */

register_deactivation_hook( __FILE__, array( NS . 'MZoo\MzMindbody\Core\Deactivator', 'deactivate' ) );

/**
 * Plugin Singleton Container
 *
 * Maintains a single copy of the plugin app object
 *
 * @since 2.4.7
 */
class MzMindbody {



	/**
	 * The instance of the plugin.
	 *
	 * @since 2.4.7
	 * @var   Init $init Instance of the plugin.
	 */
	private static $instance;

	/**
	 * Main MzMindbody Instance.
	 *
	 * Insures that only one instance of MzMindbody exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * Totally borrowed from Easy_Digital_Downloads, and certainly used with some ignorance
	 * as EDD doesn't actually include a construct in it's class.
	 *
	 * @since     2.4.7
	 * @static
	 * @staticvar array $instance
	 * @see       MZMBO()
	 * @return    object|MzMindbody The one true MzMindbody
	 */
	public static function instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof MzMindbodyApi ) ) {
			self::$instance = new NS\Core\MzMindbodyApi();
			self::$instance->run();

			self::$instance->i18n    = new Common\GlobalStrings();
			self::$instance->helpers = new Common\Helpers();
		}

		return self::$instance;
	}
}

/**
 * Begins execution of the plugin
 *
 * The main function for that returns MzMindbodyApi
 *
 * The main function responsible for returning the one true MzMindbodyApi
 * Instance to functions everywhere.
 *
 * Borrowed from Easy_Digital_Downloads.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example 1: <?php $mZmbo = MzMindbody\MZMBO(); ?>
 * Example 2: <?php $basic_options = MzMindbody\MZMBO()::$basic_options ?>
 *
 * @since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * Also returns copy of the app object so 3rd party developers
 * can interact with the plugin's hooks contained within.
 *
 * @since  1.4
 * @return object|MzMindbodyApi The one true MzMindbodyApi Instance.
 **/
// @codingStandardsIgnoreStart
function MZMBO() {
	// @codingStandardsIgnoreEnd
	return MzMindbody::instance();
}

/**
 * Deactivate
 *
 * Deactivate the plugin and add Admin notice it's been deactivated.
 *
 * @return void.
 */
function deactivate() {
	deactivate_plugins( plugin_basename( __FILE__ ) );
	$admin_object = new NS\Admin\Admin( NS\PLUGIN_NAME, NS\PLUGIN_VERSION, 'mz-mindbody-api' );
	add_action( 'admin_notices', array( $admin_object, 'admin_notice' ) );
}

// Check the minimum required PHP version and run the plugin.
if ( version_compare( PHP_VERSION, NS\MINIMUM_PHP_VERSION, '>=' ) ) {
	// Get MzMindbodyApi Instance.
	MZMBO();
} else {
	add_action( 'admin_init', __NAMESPACE__ . '\\deactivate' );
}
