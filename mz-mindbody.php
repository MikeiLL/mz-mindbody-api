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
 * Version:         2.10.6
 * Stable tag:      2.10.6
 * Tested up to:    6.5.2
 * Requires PHP:    7.1
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

if ( ! defined( 'ABSPATH' ) ) {
    die("Can't load this file directly");
}

/**
 * Define Constants
 */

define( __NAMESPACE__ . '\NS', __NAMESPACE__ . '\\' );

define( NS . 'PLUGIN_NAME', 'mz-mindbody-api' );

define( NS . 'PLUGIN_VERSION', '2.10.6' );

define( NS . 'PLUGIN_NAME_DIR', plugin_dir_path( __FILE__ ) );

define( NS . 'PLUGIN_NAME_URL', plugin_dir_url( __FILE__ ) );

define( NS . 'PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

define( NS . 'MINIMUM_PHP_VERSION', 7.1 );

define( NS . 'INIT_LEVEL', 10 );

/**
 * Check the minimum PHP version.
 */
if ( version_compare( PHP_VERSION, MINIMUM_PHP_VERSION, '<' ) ) {
    add_action( 'admin_notices', NS . 'minimum_php_version' );
    add_action( 'admin_init', __NAMESPACE__ . '\deactivate_plugins', INIT_LEVEL );
} else {
    /**
     * Autoload Classes
     */
    $wp_mindbody_api_autoload = NS\PLUGIN_NAME_DIR . '/vendor/autoload.php';

    if ( file_exists( $wp_mindbody_api_autoload ) ) {
        include_once $wp_mindbody_api_autoload;
    }

    // Mozart-managed dependencies.
    $wp_mindbody_api_mozart_autoload = NS\PLUGIN_NAME_DIR . 'src/Dependencies/autoload.php';
    if ( file_exists( $wp_mindbody_api_mozart_autoload ) ) {
        include_once $wp_mindbody_api_mozart_autoload;
    }

    if ( ! class_exists( 'MZoo\MzMindbody\Core\MzMindbodyApi' ) ) {
        add_action( 'admin_notices', NS . 'missing_composer' );
        add_action( 'admin_init', __NAMESPACE__ . '\deactivate_plugins', INIT_LEVEL );
    } else {

        /**
         * Register Activation and Deactivation Hooks
         * This action is documented in src/Core/class-activator.php
         */

        register_activation_hook( __FILE__, array( 'MZoo\MzMindbody\Core\Activator', 'activate' ) );

        /**
         * The code that runs during plugin deactivation.
         * This action is documented src/Core/class-deactivator.php
         */

        register_deactivation_hook( __FILE__, array( 'MZoo\MzMindbody\Core\Deactivator', 'deactivate' ) );

        /**
         * Run the plugin.
         */

        add_action( 'plugins_loaded', __NAMESPACE__ . '\run_plugin', INIT_LEVEL );

    }
}


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

        // Return if already instantiated
        if ( self::is_instantiated() ) {
            return self::$instance;
        }

        // Setup the singleton
        self::setup_instance();

        self::$instance->run();

        self::$instance->i18n    = new Common\GlobalStrings();
        self::$instance->helpers = new Common\Helpers();
        self::$instance->session = new Session\MzPhpSession();

        return self::$instance;
    }

    /**
     * Setup the singleton instance
     *
     * @since 3.0
     * @param string $file
     */
    private static function setup_instance() {
        self::$instance       = new NS\Core\MzMindbodyApi;
    }

    /**
     * Return whether the main loading class has been instantiated or not.
     *
     * @since 3.0
     *
     * @return boolean True if instantiated. False if not.
     */
    private static function is_instantiated() {
        // Return true if instance is correct class
        if ( ! empty( self::$instance ) && ( self::$instance instanceof \MZoo\MzMindbody\Core\MzMindbodyApi ) ) {
            return true;
        }

        // Return false if not instantiated correctly
        return false;
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
 * Deactivation and message when initialization fails.
 *
 * @param string $error        Error message to output.
 * @since 2.8.8
 * @return void.
 */
function activation_failed( $error ) {
    if ( is_admin() && current_user_can( 'activate_plugins' ) ) {
        ?>
            <div class="notice notice-error is-dismissible"><p><strong>
                <?php echo esc_html( $error ); ?>
            </strong></p></div>
        <?php
    }
}

/**
 * Deactivate plugins.
 *
 * @since 2.8.8
 * @return void.
 */
function deactivate_plugins() {
    \deactivate_plugins( plugin_basename( __FILE__ ) );
    if ( is_admin() && current_user_can( 'activate_plugins' ) ) {
        ?>
            <div class="notice notice-success is-dismissible"><p>
                <?php esc_html_e( 'MZ Mindbody Api plugin has been deactivated.', 'mz-mbo-access' ); ?>
            </p></div>
        <?php
    }
}
/**
 * Notice of missing composer.
 *
 * @since 2.8.8
 * @return void.
 */
function missing_composer() {
    activation_failed( __( 'MZ Mindbody Api requires Composer autoloading, which is not configured.', 'mz-mindbody-api' ) );
}

/**
 * Notice of php version error.
 *
 * @since 2.8.8
 * @return void.
 */
function minimum_php_version() {
    activation_failed( __( 'MZ Mindbody Api requires PHP version', 'mz-mindbody-api' ) . sprintf( ' %1.1f.', MINIMUM_PHP_VERSION ) );
}

/**
 * Notice of plugin deactivation.
 *
 * @since 2.8.8
 * @return void.
 */
function plugin_is_deactivated() {
    if ( is_admin() && current_user_can( 'activate_plugins' ) ) {
        ?>
            <div class="notice notice-success is-dismissible"><p>
                <?php esc_html_e( 'MZ Mindbody Api plugin has been deactivated.', 'mz-mindbody-api' ); ?>
            </p></div>
        <?php
    }
}

/**
 * Run the plugin.
 *
 * @since 2.8.8
 * @return void.
 */
function run_plugin() {

    // Get MzMindbodyApi Instance.
    MZMBO();

}
