<?php
/**
 * MZ Mindbody Api
 *
 * This file contains the class which instantiates the
 * core plugin instance, defining internationalization,
 * admin-specific and public-facing site hooks.
 *
 * @package MzMindbody
 */

namespace MZoo\MzMindbody\Core;

use MZoo\MzMindbody as NS;
use MZoo\MzMindbody\Admin;
use MZoo\MzMindbody\Frontend;
use MZoo\MzMindbody\Backend;
use MZoo\MzMindbody\Common;
use MZoo\MzMindbody\Client;
use MZoo\MzMindbody\Schedule;
use MZoo\MzMindbody\Staff;
use MZoo\MzMindbody\Events;
use MZoo\MzMindbody\Libraries;
use MZoo\MzMindbody\Cli;
use MZoo\MzMindbody\Session;


/**
 * The core plugin class.
 * Defines internationalization, admin-specific hooks, and public-facing site hooks.
 *
 * @link  http://mzoo.org
 * @since 1.0.0
 *
 * @author Mike iLL/mZoo.org
 */
class MzMindbodyApi {


    /**
     * Instance
     *
     * @var   MzMindbodyApi The one true MzMindbodyApi
     * @since 2.4.7
     */
    private static $instance;

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @var Loader $loader Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since  2.4.7
     * @access protected
     * @var    string $plugin_base_name The string used to uniquely identify this plugin.
     */
    protected $plugin_basename;

    /**
     * The current version of the plugin.
     *
     * @since  2.4.7
     * @access protected
     * @var    string $version The current version of the plugin.
     */
    protected $version;

    /**
     * The text domain of the plugin.
     *
     * @since  2.4.7
     * @access protected
     * @var    string $plugin_text_domain The plugin i18n text domain.
     */
    protected $plugin_text_domain;

    /**
     * Saved Basic Options for the Plugin.
     *
     * @since  2.4.7
     * @access protected
     * @var    string $basic_options Basic configurations for the plugin.
     */
    public static $basic_options;

    /**
     * Saved Events Options for the Plugin.
     *
     * @since  2.4.7
     * @access protected
     * @var    string $events_options Configuration for event display.
     */
    public static $events_options;

    /**
     * Saved Advanced Options for the Plugin.
     *
     * @since  2.4.7
     * @access protected
     * @var    string $advanced_options Configuration of advanced options.
     */
    public static $advanced_options;

    /**
     * Saved Options array storing number of calls to MBO API.
     *
     * @since  2.5.7
     * @access protected
     * @var    string $mz_mbo_api_calls number of daily calls to api.
     */
    public static $mz_mbo_api_calls;

    /**
     * Number of days to retrieve Events for at a time.
     *
     * @since  2.4.7
     * @access protected
     * @var    integer $event_calendar_duration How many days ahead to retrieve Events for.
     */
    public static $event_calendar_duration;

    /**
     * Format for date display, specific to MBO API Plugin.
     *
     * @since  2.4.7
     * @access public
     * @var    string $date_format WP date format option.
     */
    public static $date_format;

    /**
     * Format for time display, specific to MBO API Plugin.
     *
     * @since  2.4.7
     * @access public
     * @var    string $time_format
     */
    public static $time_format;

    /**
     * Timezone string returned by WordPress get_timezone function.
     *
     * For example 'US/Eastern'
     *
     * @since  2.4.7
     * @access public
     * @var    string $timezone PHP Date formatting string.
     */
    public static $timezone;

    /**
     * WordPress option for start of week.
     *
     * @since  2.4.7
     * @access public
     * @var    integer $start_of_week.
     */
    public static $start_of_week;

    /**
     * Setting page object to can be extended by other plugins.
     *
     * @since  2.5.8
     * @access public
     * @var    obj $settings_page.
     */
    public static $settings_page;


    /**
     * Session object
     *
     * @since 2.9.9
     * @var    $session MzPhpSession
     * @access public
     */
    public $session;

    /**
     * Use Oauth
     *
     * @since 2.9.9
     * @var    $client_portal ClientPortal
     * @access public
     */
    public static $client_portal;

    /**
     * Initialize and define the core functionality of the plugin.
     */
    public function __construct() {
        /* echo "Instantiating MzMindbodyApi<pre>";
        print_r(debug_print_backtrace());
        echo "</pre>"; */

        $this->plugin_name        = NS\PLUGIN_NAME;
        $this->version            = NS\PLUGIN_VERSION;
        $this->plugin_basename    = NS\PLUGIN_BASENAME;
        $this->plugin_text_domain = 'mz-mindbody-api';

        self::$basic_options           = get_option( 'mz_mbo_basic', 'Error: No Basic Options' );
        self::$events_options          = get_option( 'mz_mbo_events', [] );
        self::$advanced_options        = get_option( 'mz_mbo_advanced', ['api_call_limit' => 2000, 'elect_display_substitutes' => 'off'] );
        self::$mz_mbo_api_calls        = get_option( 'mz_mbo_api_calls', ['calls' => 2000]);
        self::$timezone                = wp_timezone_string();
        self::$event_calendar_duration = isset( self::$events_options['mz_mindbody_scheduleDuration'] ) ? self::$events_options['mz_mindbody_scheduleDuration'] : 60;
        self::$date_format             = empty( self::$advanced_options['date_format'] ) ? get_option( 'date_format' ) : self::$advanced_options['date_format'];
        self::$time_format             = empty( self::$advanced_options['time_format'] ) ? get_option( 'time_format' ) : self::$advanced_options['time_format'];
        self::$start_of_week           = get_option( 'start_of_week' );
        //$this->session                 = (new Session\MzPhpSession)->init();

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
        $this->register_shortcodes();
        $this->add_settings_page();
        $this->instantiate_wpcli();
    }

    /**
     * Loads the following required dependencies for this plugin.
     *
     * - Loader - Orchestrates the hooks of the plugin.
     * - InternationalizationI18n - Defines internationalization functionality.
     * - Admin - Defines all hooks for the admin area.
     * - Frontend - Defines all hooks for the public side of the site.
     *
     * @access private
     */
    private function load_dependencies() {
        $this->loader = new Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the InternationalizationI18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @access private
     */
    private function set_locale() {

        $plugin_i18n = new InternationalizationI18n( $this->plugin_text_domain );

        $this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @access private
     */
    private function define_admin_hooks() {

        $plugin_admin = new Admin\Admin( $this->get_plugin_name(), $this->get_version(), $this->get_plugin_text_domain() );

        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
        $this->loader->add_action( 'plugins_loaded', $plugin_admin, 'check_version' );
        $this->loader->add_action( 'admin_head', $plugin_admin, 'set_plugin_update_message' );

        // TODO move this?
        if ( ( isset( self::$advanced_options['elect_display_substitutes'] ) ) &&
            ( 'on' === self::$advanced_options['elect_display_substitutes'] ) ) {
            // Create the "Class Owners" transient, if not already created.
            $class_owners_object = new Schedule\RetrieveClassOwners();
            $this->loader->add_action( 'create_class_owners_transient', $class_owners_object, 'deduce_class_owners' );
            // add_action('create_class_owners_transient', array($class_owners_object, 'deduce_class_owners'));.
            // We delay it just in case because of only one MBO call at a time being allowed.
            $three_seconds_from_now = time() + 3000;
            if ( ! wp_next_scheduled( 'create_class_owners_transient' ) ) {
                wp_schedule_event( $three_seconds_from_now, 'daily', 'create_class_owners_transient' );
            }
        }
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @access private
     */
    private function define_public_hooks() {
        $admin_object        = new Admin\Admin( $this->get_plugin_name(), $this->get_version(), $this->get_plugin_text_domain() );
        $schedule_object     = new Schedule\Display();
        $events_object       = new Events\Display();
        $registrant_object   = new Schedule\RetrieveRegistrants();
        $client_portal         = new Client\ClientPortal();
        $class_owners_object = new Schedule\RetrieveClassOwners();
        $staff_object        = new Staff\Display();
        $token_object        = new Common\TokenManagement();
        //$api_object          = new Libraries\MboApi();

        // Create hook for admin excess api calls alert.
        //$this->loader->add_action( 'mz_mbo_api_alert_cron', $api_object, 'admin_call_excess_alert' );

        // Start Ajax Clear Transients.
        $this->loader->add_action( 'wp_ajax_nopriv_mz_mbo_clear_transients', $admin_object, 'ajax_clear_plugin_transients' );
        $this->loader->add_action( 'wp_ajax_mz_mbo_clear_transients', $admin_object, 'ajax_clear_plugin_transients' );

        // Start Ajax Cancel Excess API Alerts.
        $this->loader->add_action( 'wp_ajax_nopriv_mz_mbo_cancel_excess_api_alerts', $admin_object, 'ajax_cancel_excess_api_alerts' );
        $this->loader->add_action( 'wp_ajax_mz_mbo_excess_api_alerts', $admin_object, 'ajax_cancel_excess_api_alerts' );

        // Start Ajax New Token.
        $this->loader->add_action( 'wp_ajax_nopriv_mz_mbo_get_and_save_staff_token', $admin_object, 'ajax_get_and_save_staff_token' );
        $this->loader->add_action( 'wp_ajax_mz_mbo_get_and_save_staff_token', $admin_object, 'ajax_get_and_save_staff_token' );

        // Start Ajax Creds Tests.
        $this->loader->add_action( 'wp_ajax_nopriv_mz_mbo_test_credentials', $admin_object, 'test_credentials' );
        $this->loader->add_action( 'wp_ajax_mz_mbo_test_credentials', $admin_object, 'test_credentials' );

        // Start Ajax Display Schedule.
        $this->loader->add_action( 'wp_ajax_nopriv_mz_display_schedule', $schedule_object, 'display_schedule' );
        $this->loader->add_action( 'wp_ajax_mz_display_schedule', $schedule_object, 'display_schedule' );

        // Start Ajax Display Schedule.
        $this->loader->add_action( 'wp_ajax_nopriv_mz_displayEvents', $events_object, 'displayEvents' );
        $this->loader->add_action( 'wp_ajax_mz_displayEvents', $events_object, 'displayEvents' );

        // Start Ajax Get Registrants.
        $this->loader->add_action( 'wp_ajax_nopriv_mz_mbo_get_registrants', $registrant_object, 'ajax_get_registrants' );
        $this->loader->add_action( 'wp_ajax_mz_mbo_get_registrants', $registrant_object, 'ajax_get_registrants' );

        // Start Ajax Retrieve Class Owners.
        $this->loader->add_action( 'wp_ajax_nopriv_mz_deduce_class_owners', $class_owners_object, 'deduce_class_owners' );
        $this->loader->add_action( 'wp_ajax_mz_deduce_class_owners', $class_owners_object, 'deduce_class_owners' );

        // Start Ajax Get Staff.
        $this->loader->add_action( 'wp_ajax_nopriv_mz_mbo_get_staff', $staff_object, 'get_staff_modal' );
        $this->loader->add_action( 'wp_ajax_mz_mbo_get_staff', $staff_object, 'get_staff_modal' );

        // Start Ajax Client Check Logged
        $this->loader->add_action('wp_ajax_nopriv_mz_add_client_to_class', $client_portal, 'ajax_add_client_to_class');
        $this->loader->add_action('wp_ajax_mz_add_client_to_class', $client_portal, 'ajax_add_client_to_class');

        // Start Ajax Client Create Account
        $this->loader->add_action('wp_ajax_nopriv_mz_create_mbo_account', $client_portal, 'ajax_create_mbo_account');
        $this->loader->add_action('wp_ajax_mz_create_mbo_account', $client_portal, 'ajax_create_mbo_account');

        // Start Ajax Client Create Account
        $this->loader->add_action('wp_ajax_nopriv_mz_generate_signup_form', $client_portal, 'ajax_generate_mbo_signup_form');
        $this->loader->add_action('wp_ajax_mz_generate_signup_form', $client_portal, 'ajax_generate_mbo_signup_form');

        // Start Ajax Client Log In
        $this->loader->add_action('wp_ajax_nopriv_mz_client_login', $client_portal, 'ajax_client_login');
        $this->loader->add_action('wp_ajax_mz_client_login', $client_portal, 'ajax_client_login');

        // Start Ajax Client Log Out
        $this->loader->add_action('wp_ajax_nopriv_mz_client_logout', $client_portal, 'ajax_client_logout');
        $this->loader->add_action('wp_ajax_mz_client_logout', $client_portal, 'ajax_client_logout');

        // Start Ajax Display Client Schedule
        $this->loader->add_action('wp_ajax_nopriv_mz_display_client_schedule', $client_portal, 'ajax_display_client_schedule');
        $this->loader->add_action('wp_ajax_mz_display_client_schedule', $client_portal, 'ajax_display_client_schedule');

        // Start Ajax Check Client Logged Status
        $this->loader->add_action('wp_ajax_nopriv_mz_check_client_logged', $client_portal, 'ajax_check_client_logged');
        $this->loader->add_action('wp_ajax_mz_check_client_logged', $client_portal, 'ajax_check_client_logged');

        // Call api hourly to retrieve AccessToken.
        $this->loader->add_action( 'fetch_mbo_access_token', $token_object, 'get_and_save_staff_token', 10, 2 );
    }


    /**
     * Run the loader to execute all of the hooks with WordPress.
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @return Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since  1.0.0
     * @return string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }

    /**
     * Return our session instance
     * @since 2.9.9
     * TODO Redundant as also in Session class
     */
    public function get_session() {
        return $this->session;
    }

    /**
     * Retrieve the text domain of the plugin.
     *
     * @since  1.0.0
     * @return string    The text domain of the plugin.
     */
    public function get_plugin_text_domain() {
        return $this->plugin_text_domain;
    }

    /**
     * Add our settings page
     *
     * @since 1.0.0
     */
    public function add_settings_page() {
        self::$settings_page = new Backend\SettingsPage();
        self::$settings_page->addSections();
    }

    /**
     * Registers all the plugins shortcodes.
     *
     * - Events - The Events Class which displays events and loads necessary assets.
     *
     * @access private
     */
    private function register_shortcodes() {
        $schedule_display = new Schedule\Display();
        $schedule_display->register( 'mz-mindbody-show-schedule' );
        $staff_display = new Staff\Display();
        $staff_display->register( 'mz-mindbody-staff-list' );
        $events_display = new Events\Display();
        $events_display->register( 'mz-mindbody-show-events' );

/*         add_shortcode('authenticate', function() {




            $body = [
                'client_id' => 'f89a95cc-d6d5-470a-825b-93f707b88139',
                'response_type' => 'code',
                'scope' => 'email profile openid offline_access Mindbody.Api.Public.v6 PG.ConsumerActivity.Api.Read',
                'redirect_uri' => home_url() . '/mzmbo/authenticate',
                'nonce' => wp_create_nonce( 'mz_mbo_authenticate_with_api' )
            ];

            ?>
    <a href="https://signin.mindbodyonline.com/connect/authorize?<?php echo http_build_query($body) ?>"?>Sign In</a>
            <?php



            Array
            (
                [code] => 4E3C7757C61596DE4E4C0CA4DCCBDB5800CEAC42C38A92C5EAEC6636EEC5001B-1
                [id_token] => eyJhbGciOiJSUzI1NiIsImtpZCI6IjlERkRDNzQwMUU5NTk2RTAxNTQxRkMyQTM1QUIzRkQ4NjhGRUIwMDYiLCJ4NXQiOiJuZjNIUUI2Vmx1QVZRZndxTmFzXzJHai1zQVkiLCJ0eXAiOiJKV1QifQ.eyJpc3MiOiJodHRwczovL3NpZ25pbi5taW5kYm9keW9ubGluZS5jb20iLCJuYmYiOjE2NjU1OTY2NjEsImlhdCI6MTY2NTU5NjY2MSwiZXhwIjoxNjY1NTk2OTYxLCJhdWQiOiJmODlhOTVjYy1kNmQ1LTQ3MGEtODI1Yi05M2Y3MDdiODgxMzkiLCJhbXIiOlsiZXh0ZXJuYWwiXSwibm9uY2UiOiI1ZTI2ZmVmNjAzIiwiY19oYXNoIjoieERiOTF0bWxpaUVsZnJ2Qnd4aVdSZyIsInNpZCI6IkIzNjZEN0FGQTJCODgwODlDOTM3NjhFMEQ5QjQ5ODA2Iiwic3ViIjoiNWY3ZjJlYTE5YjVlMzU2YWU0MzBlM2Q4IiwiYXV0aF90aW1lIjoxNjY1NTk1OTIyLCJpZHAiOiJGYWNlYm9vayJ9.sON1eNNaYX3Pnc9oNMLSVQxX8crNPlvXEE4cwV3vv7PDYp3zzaxcc-RC01P8nv1YdysjfUuLOX_DSZ3MRE4mHtOLcQxRM8PjGhSV9qwrVmleLoKRYm4GGExw0odJzH4cEWSkT8fcKiR_bL2pFBn4aPX4EA9AibMUEwhA3nUSI_-4ddxMzVcMg0CD5aPX5eh3A8UEvBwX5tkdzLgRRSTFtXv2_Lzj20RJNLVbuDRHrxSTGn_WTl4L8eH-MAUvN54HKtFaGk1F2NqhqCXqAhgPHrqxn-5T1y9fdldKp7uuhy6zFYJhwvysNYiEan8i9LPXI05s5-NMCp2nSDPvT5JgdX7jHrArGnwJk44XalFqZIqIlSp7VDDIvxwMenhMjxWbQ8QBQ1KJgqphN_pAAwRgX8657WnpxOGvPICsCjCisVT1ARJxUzO0R-5bFl3bbbCp3MsulQpmd9Mx5vhVUxYy_-kewW5C3J5FZPpeRv6_CsSuLTGS6AhwfxoPAg57a6-Bq0kJtbwoY9BG7Svq7JDWV8SIO9tSXW5_CEtQT2f3TTDu2FJOzvqHD1m0Vh_Y05-S4ximHWK8JqjW_kc-YcAlkcjEQXpFNO99AfQRMMzB2ArvqbjGkC1bHofV3xnwLQ_YkEOkEeOPM0iF-PjmtfEW_qBLg0TLCsF_8GtJ36rRCKM
                [scope] => offline_access PG.ConsumerActivity.Api.Read PG.ConsumerActivity.Api.Write email openid
                [session_state] => h4OfU5Yx2dWNBBwM6SZKR05XpRUtQC6mE0Ow2c4fTNk.45FEE050899FC84ABF1223F7DB58D272
            )

        }); // End of shortcode authenticate*/
    }

    /**
     * Instantiate WP CLI
     * @since 2.9.3
     */
    private function instantiate_wpcli(){
        if ( defined( 'WP_CLI' ) && WP_CLI ) {
            \WP_CLI::add_command( 'mzmbo', 'MZoo\MzMindbody\Cli\WpCommands' );
        }
    }
}
