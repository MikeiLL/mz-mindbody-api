<?php

namespace MZ_Mindbody\Inc\Core;

use MZ_Mindbody as NS;
use MZ_Mindbody\Inc\Admin as Admin;
use MZ_Mindbody\Inc\Frontend as Frontend;
use MZ_Mindbody\Inc\Backend as Backend;
use MZ_Mindbody\Inc\Common as Common;
use MZ_Mindbody\Inc\Schedule as Schedule;
use MZ_Mindbody\Inc\Staff as Staff;
use MZ_Mindbody\Inc\Events as Events;
use MZ_Mindbody\Inc\Libraries\Rarst\WordPress\DateTime as DateTime;

/**
 * The core plugin class.
 * Defines internationalization, admin-specific hooks, and public-facing site hooks.
 *
 * @link       http://mzoo.org
 * @since      1.0.0
 *
 * @author     Mike iLL/mZoo.org
 */
class MZ_Mindbody_Api
{
    /**
     * @var MZ_Mindbody_API The one true MZ_Mindbody_API
     * @since 2.4.7
     */
    private static $instance;

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @var      Loader $loader Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    2.4.7
     * @access   protected
     * @var      string $plugin_base_name The string used to uniquely identify this plugin.
     */
    protected $plugin_basename;

    /**
     * The current version of the plugin.
     *
     * @since    2.4.7
     * @access   protected
     * @var      string $version The current version of the plugin.
     */
    protected $version;

    /**
     * The text domain of the plugin.
     *
     * @since    2.4.7
     * @access   protected
     * @var      string $plugin_text_domain The plugin i18n text domain.
     */
    protected $plugin_text_domain;

    /**
     * Saved Basic Options for the Plugin.
     *
     * @since    2.4.7
     * @access   protected
     * @var      string $basic_options Basic configurations for the plugin.
     */
    public static $basic_options;

    /**
     * Saved Events Options for the Plugin.
     *
     * @since    2.4.7
     * @access   protected
     * @var      string $events_options Configuration for event display.
     */
    public static $events_options;

    /**
     * Saved Advanced Options for the Plugin.
     *
     * @since    2.4.7
     * @access   protected
     * @var      string $advanced_options Configuration of advanced options.
     */
    public static $advanced_options;

    /**
     * Saved Options array storing number of calls to MBO API.
     *
     * @since    2.5.7
     * @access   protected
     * @var      string $mz_mbo_api_calls number of daily calls to api.
     */
    public static $mz_mbo_api_calls;

    /**
     * Number of days to retrieve Events for at a time.
     *
     * @since    2.4.7
     * @access   protected
     * @var      integer $event_calendar_duration How many days ahead to retrieve Events for.
     */
    public static $event_calendar_duration;

    /**
     * Format for date display, specific to MBO API Plugin.
     *
     * @since    2.4.7
     * @access   public
     * @var      string $date_format WP date format option.
     */
    public static $date_format;

    /**
     * Format for time display, specific to MBO API Plugin.
     *
     * @since    2.4.7
     * @access   public
     * @var      string $time_format
     */
    public static $time_format;

    /**
     * Timezone string returned by wordpress get_timezone function.
     *
     * For example 'US/Eastern'
     *
     * @since    2.4.7
     * @access   public
     * @var      string $timezone PHP Date formatting string.
     */
    public static $timezone;

    /**
     * Wordpress option for start of week.
     *
     * @since    2.4.7
     * @access   public
     * @var      integer $start_of_week.
     */
    public static $start_of_week;

    /**
     * Setting page object to can be extended by other plugins.
     *
     * @since    2.5.8
     * @access   public
     * @var      obj $settings_page.
     */
    public static $settings_page;

    /**
     * Initialize and define the core functionality of the plugin.
     */
    public function __construct()
    {

        $this->plugin_name = NS\PLUGIN_NAME;
        $this->version = NS\PLUGIN_VERSION;
        $this->plugin_basename = NS\PLUGIN_BASENAME;
        $this->plugin_text_domain = NS\PLUGIN_TEXT_DOMAIN;

        self::$basic_options = get_option('mz_mbo_basic', 'Error: No Basic Options');
        self::$events_options = get_option('mz_mbo_events');
        self::$advanced_options = get_option('mz_mbo_advanced');
        self::$mz_mbo_api_calls = get_option('mz_mbo_api_calls');
        self::$mz_mbo_api_calls = empty(self::$mz_mbo_api_calls) ? 2000 : self::$mz_mbo_api_calls;
        self::$timezone = DateTime\WpDateTimeZone::getWpTimezone();
        self::$event_calendar_duration = isset(self::$events_options['mz_mindbody_scheduleDuration']) ? self::$events_options['mz_mindbody_scheduleDuration'] : 60;
        self::$date_format = empty(self::$advanced_options['date_format']) ? get_option('date_format') : self::$advanced_options['date_format'];
        self::$time_format = empty(self::$advanced_options['time_format']) ? get_option('time_format') : self::$advanced_options['time_format'];
        self::$start_of_week = get_option('start_of_week');

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
        $this->register_shortcodes();
        $this->add_settings_page();

    }

    /**
     * Loads the following required dependencies for this plugin.
     *
     * - Loader - Orchestrates the hooks of the plugin.
     * - Internationalization_I18n - Defines internationalization functionality.
     * - Admin - Defines all hooks for the admin area.
     * - Frontend - Defines all hooks for the public side of the site.
     *
     * @access    private
     */
    private function load_dependencies()
    {
        $this->loader = new Loader();

    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Internationalization_I18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @access    private
     */
    private function set_locale()
    {

        $plugin_i18n = new Internationalization_I18n($this->plugin_text_domain);

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');

    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @access    private
     */
    private function define_admin_hooks()
    {

        $plugin_admin = new Admin\Admin($this->get_plugin_name(), $this->get_version(), $this->get_plugin_text_domain());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        $this->loader->add_action('plugins_loaded', $plugin_admin, 'check_version');
        $this->loader->add_action('admin_head', $plugin_admin, 'set_plugin_update_message');
        
        // TODO move this?
        if ((isset(self::$advanced_options['elect_display_substitutes'])) && (self::$advanced_options['elect_display_substitutes'] == 'on')) {
            // Create the "Class Owners" transient, if not already created
            $class_owners_object = new Schedule\Retrieve_Class_Owners;
            $this->loader->add_action('create_class_owners_transient', $class_owners_object, 'deduce_class_owners');
            //add_action('create_class_owners_transient', array($class_owners_object, 'deduce_class_owners'));
            // We delay it just in case because of only one MBO call at a time being allowed.
            $three_seconds_from_now = time() + 3000;
            if (!wp_next_scheduled( 'create_class_owners_transient' )){
             wp_schedule_event($three_seconds_from_now, 'daily', 'create_class_owners_transient');
            }

        }
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @access    private
     */
    private function define_public_hooks()
    {

        $admin_object = new Admin\Admin($this->get_plugin_name(), $this->get_version(), $this->get_plugin_text_domain());
        $schedule_object = new Schedule\Display;
        $events_object = new Events\Display;
        $registrant_object = new Schedule\Retrieve_Registrants;
        $class_owners_object = new Schedule\Retrieve_Class_Owners;
        $staff_object = new Staff\Display;
        $token_object = new Common\Token_Management;


        // Start Ajax Clear Transients
        $this->loader->add_action('wp_ajax_nopriv_mz_mbo_clear_transients', $admin_object, 'ajax_clear_plugin_transients');
        $this->loader->add_action('wp_ajax_mz_mbo_clear_transients', $admin_object, 'ajax_clear_plugin_transients');
        
        // Start Ajax New Token
        $this->loader->add_action('wp_ajax_nopriv_mz_mbo_get_and_save_token', $admin_object, 'ajax_get_and_save_token');
        $this->loader->add_action('wp_ajax_mz_mbo_get_and_save_token', $admin_object, 'ajax_get_and_save_token');

        // Start Ajax Creds Tests
        $this->loader->add_action('wp_ajax_nopriv_mz_mbo_test_credentials', $admin_object, 'test_credentials');
        $this->loader->add_action('wp_ajax_mz_mbo_test_credentials', $admin_object, 'test_credentials');
        $this->loader->add_action('wp_ajax_nopriv_mz_mbo_test_credentials_v5', $admin_object, 'test_credentials_v5');
        $this->loader->add_action('wp_ajax_mz_mbo_test_credentials_v5', $admin_object, 'test_credentials_v5');

        // Start Ajax Display Schedule
        $this->loader->add_action('wp_ajax_nopriv_mz_display_schedule', $schedule_object, 'display_schedule');
        $this->loader->add_action('wp_ajax_mz_display_schedule', $schedule_object, 'display_schedule');

        // Start Ajax Display Schedule
        $this->loader->add_action('wp_ajax_nopriv_mz_display_events', $events_object, 'display_events');
        $this->loader->add_action('wp_ajax_mz_display_events', $events_object, 'display_events');

        // Start Ajax Get Registrants
        $this->loader->add_action('wp_ajax_nopriv_mz_mbo_get_registrants', $registrant_object, 'ajax_get_registrants');
        $this->loader->add_action('wp_ajax_mz_mbo_get_registrants', $registrant_object, 'ajax_get_registrants');

        // Start Ajax Retrieve Class Owners
        $this->loader->add_action('wp_ajax_nopriv_mz_deduce_class_owners', $class_owners_object, 'deduce_class_owners');
        $this->loader->add_action('wp_ajax_mz_deduce_class_owners', $class_owners_object, 'deduce_class_owners');

        // Start Ajax Get Staff
        $this->loader->add_action('wp_ajax_nopriv_mz_mbo_get_staff', $staff_object, 'get_staff_modal');
        $this->loader->add_action('wp_ajax_mz_mbo_get_staff', $staff_object, 'get_staff_modal');
        
        // Call api hourly to retrieve AccessToken
        $this->loader->add_action( 'fetch_mbo_access_token', $token_object, 'get_and_save_token', 10, 2 );

    }


    /**
     * Run the loader to execute all of the hooks with WordPress.
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @return    Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version()
    {
        return $this->version;
    }

    /**
     * Retrieve the text domain of the plugin.
     *
     * @since     1.0.0
     * @return    string    The text domain of the plugin.
     */
    public function get_plugin_text_domain()
    {
        return $this->plugin_text_domain;
    }

    /**
     * Add our settings page
     *
     * @since     1.0.0
     */
    public function add_settings_page()
    {
        self::$settings_page = new Backend\Settings_Page();
        self::$settings_page->addSections();
    }

    /**
     * Registers all the plugins shortcodes.
     *
     * - Events - The Events Class which displays events and loads necessary assets.
     *
     * @access    private
     */
    private function register_shortcodes()
    {
        $schedule_display = new Schedule\Display();
        $schedule_display->register('mz-mindbody-show-schedule');
        $staff_display = new Staff\Display();
        $staff_display->register('mz-mindbody-staff-list');
        $events_display = new Events\Display();
        $events_display->register('mz-mindbody-show-events');
    }

}
