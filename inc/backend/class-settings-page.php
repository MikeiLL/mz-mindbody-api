<?php
namespace MZ_Mindbody\Inc\Backend;

use MZ_Mindbody as NS;
use MZ_Mindbody\Inc\Core as Core;
use MZ_Mindbody\Inc\Common as Common;
use MZ_Mindbody\Inc\Libraries as Libraries;
use MZ_Mindbody\Inc\Schedule as Schedule;

/**
 * This file contains the class which holds all the actions and methods to create the admin dashboard sections
 *
 * This file contains all the actions and functions to create the admin dashboard sections.
 * It should probably be refactored to use oop approach at least for the sake of consistency.
 *
 * @since 2.1.0
 *
 * @package MZ_Mindbody
 *
 */
/**
 * Actions/Filters
 *
 * Related to all settings API.
 *
 * @since  1.0.0
 */

class Settings_Page {

    static public $wposa_obj;

    public function __construct() {
        self::$wposa_obj = new Libraries\WP_OSA();
    }

    public function addSections() {

        // Section: Basic Settings.
        self::$wposa_obj->add_section(
            array(
                'id'    => 'mz_mbo_basic',
                'title' => __( 'Basic Settings', 'mz-mindbody-api' ),
            )
        );

        // Section: Event Settings.
        self::$wposa_obj->add_section(
            array(
                'id'    => 'mz_mbo_events',
                'title' => __( 'Events', 'mz-mindbody-api' ),
            )
        );

        // Section: Shortcodes.
        self::$wposa_obj->add_section(
            array(
                'id'    => 'mz_mbo_shortcodes',
                'title' => __( 'Shortcodes', 'mz-mindbody-api' ),
            )
        );

        // Section: Advanced.
        self::$wposa_obj->add_section(
            array(
                'id'    => 'mz_mbo_advanced',
                'title' => __( 'Advanced', 'mz-mindbody-api' ),
            )
        );

        // Field: Credentials Intro HTML.
        self::$wposa_obj->add_field(
            'mz_mbo_basic',
            array(
                'id'      => 'credentials_intro',
                'type'    => 'html',
                'name'    => __( 'Enter your mindbody credentials below.', 'mz-mindbody-api' ),
                'desc'    => $this->credentials_intro()
            )
        );

        // Field: App Name. 
        self::$wposa_obj->add_field(
            'mz_mbo_basic',
            array(
                'id'      => 'mz_mbo_app_name',
                'type'    => 'text',
                'name'    => __( 'App Name', 'mz-mindbody-api' ),
                'desc'    => 'MBO App Name Name (API V6)',
                'default' => __('YOUR MBO APP NAME', 'mz-mindbody-api')
            )
        );

        // Field: API Key. 
        self::$wposa_obj->add_field(
            'mz_mbo_basic',
            array(
                'id'      => 'mz_mbo_api_key',
                'type'    => 'password',
                'name'    => __( 'API Key', 'mz-mindbody-api' ),
                'desc'    => 'MBO API Key (API V6)',
                'default' => __('YOUR MINDBODY API KEY', 'mz-mindbody-api')
            )
        );

        // Field: Source Name. DEPRECIATED
        self::$wposa_obj->add_field(
            'mz_mbo_basic',
            array(
                'id'      => 'mz_source_name',
                'type'    => 'text',
                'name'    => __( 'Staff Name', 'mz-mindbody-api' ),
                'desc'    => 'Staff Username or MBO Developer Source Name',
                'default' => __('YOUR SOURCE NAME', 'mz-mindbody-api')
            )
        );

        // Field: Display Substitute Status
        self::$wposa_obj->add_field(
            'mz_mbo_basic',
            array(
                'id'      => 'sourcename_not_staff',
                'type'    => 'checkbox',
                'name'    => __( 'Staff Name is MBO Sourcename', 'mz-mindbody-api' ),
                'desc'    => __( 'App adds and underscore when Sourcename used as Staff Name.', 'mz-mindbody-api' ),
                'default' => 'off'
            )
        );

        // Field: Staff Password. 
        self::$wposa_obj->add_field(
            'mz_mbo_basic',
            array(
                'id'      => 'mz_mindbody_password',
                'type'    => 'password',
                'name'    => __( 'Staff Password ', 'mz-mindbody-api' ),
                'desc'    => 'Staff or MBO Developer Password',
                'default' => __('YOUR MINDBODY PASSWORD', 'mz-mindbody-api')
            )
        );

        // Field: Primary MBO Site ID. 
        self::$wposa_obj->add_field(
            'mz_mbo_basic',
            array(
                'id'      => 'mz_mindbody_siteID',
                'type'    => 'number',
                'name'    => __( 'Primary (default) MBO Site ID', 'mz-mindbody-api' ),
                'desc'    => 'MBO Site ID (-99 is the sandbox account for testing)',
                'default' => __('-99', 'mz-mindbody-api')
            )
        );

        // Field: Separator.
        self::$wposa_obj->add_field(
            'mz_mbo_basic',
            array(
                'id'   => 'separator',
                'type' => 'separator',
            )
        );

        // Field: Title.
        self::$wposa_obj->add_field(
            'mz_mbo_basic',
            array(
                'id'   => 'credentials_test',
                'type' => 'title',
                'name' => '<h1>V6 Credentials Test</h1>',
                'default' => ''
            )
        );


        // Field: Textarea.
        self::$wposa_obj->add_field(
            'mz_mbo_basic',
            array(
                'id'   => 'credentials_test',
                'type' => 'html',
                'name' => __( 'Debug Output', 'mz-mindbody-api' ),
                'desc' => $this->mz_mindbody_debug_text()
            )
        );

        // Field: Event IDs.
        self::$wposa_obj->add_field(
            'mz_mbo_events',
            array(
                'id'      => 'mz_mindbody_eventID',
                'type'    => 'text',
                'name'    => __( 'Event IDs', 'mz-mindbody-api' ),
                'desc'    => 'Event Type IDs to return',
                'default' => __('12345, 34, 64', 'mz-mindbody-api')
            )
        );

        // Field: Events Duration Display.
        self::$wposa_obj->add_field(
            'mz_mbo_events',
            array(
                'id'      => 'mz_mindbody_scheduleDuration',
                'type'    => 'text',
                'name'    => __( 'Event Display Duration', 'mz-mindbody-api' ),
                'desc'    => 'How many days from today to display events for',
                'default' => __('60', 'mz-mindbody-api')
            )
        );

        // Field: Schedule Codes.
        self::$wposa_obj->add_field(
            'mz_mbo_shortcodes',
            array(
                'id'      => 'schedule_codes',
                'type'    => 'html',
                'name'    => __( 'Schedule Display', 'mz-mindbody-api' ),
                'desc'    => $this->schedule_codes()
            )
        );

        // Field: Staff Codes.
        self::$wposa_obj->add_field(
            'mz_mbo_shortcodes',
            array(
                'id'      => 'staff_codes',
                'type'    => 'html',
                'name'    => __( 'Staff Display', 'mz-mindbody-api' ),
                'desc'    => $this->staff_codes()
            )
        );

        // Field: Event Codes.
        self::$wposa_obj->add_field(
            'mz_mbo_shortcodes',
            array(
                'id'      => 'event_codes',
                'type'    => 'html',
                'name'    => __( 'Events Display', 'mz-mindbody-api' ),
                'desc'    => $this->event_codes()
            )
        );


        // Field: Date Format.
        self::$wposa_obj->add_field(
            'mz_mbo_advanced',
            array(
                'id'      => 'date_format',
                'type'    => 'text',
                'name'    => __( 'Date Format', 'mz-mindbody-api' ),
                'desc'    => 'PHP Date Format for calendar display',
                'default' => __('l, F j', 'mz-mindbody-api')
            )
        );

        // Field: Time Format.
        self::$wposa_obj->add_field(
            'mz_mbo_advanced',
            array(
                'id'      => 'time_format',
                'type'    => 'text',
                'name'    => __( 'Time Format', 'mz-mindbody-api' ),
                'desc'    => 'PHP Time Format for calendar display',
                'default' => __('g:i a', 'mz-mindbody-api')
            )
        );

        // Field: Clear Transients
        self::$wposa_obj->add_field(
            'mz_mbo_advanced',
            array(
                'id'      => 'schedule_types',
                'type'    => 'multicheck',
                'name'    => __( 'Schedule Types', 'mz-mindbody-api' ),
                'desc'    => __('Which MBO schedule types to display in "schedule" (defaults to Class.)', 'mz-mindbody-api'),
                'options' => array(
                                    'Enrollment' => 'Enrollment',
                                    'Class' => 'Class'
                                )
            )
        );

        // Field: Clear Transients
        self::$wposa_obj->add_field(
            'mz_mbo_advanced',
            array(
                'id'      => 'clear_transients',
                'type'    => 'html',
                'name'    => __( 'Clear Transients', 'mz-mindbody-api' ),
                'desc'    => $this->clear_transients()
            )
        );

        // Field: Clear Transients
        self::$wposa_obj->add_field(
            'mz_mbo_advanced',
            array(
                'id'      => 'update_mbo_site_token',
                'type'    => 'html',
                'name'    => __( 'Update MBO Site Token', 'mz-mindbody-api' ),
                'desc'    => $this->update_mbo_site_token()
            )
        );

        // Field: Clear Transients
        self::$wposa_obj->add_field(
            'mz_mbo_advanced',
            array(
                'id'      => 'api_call_limit',
                'type'    => 'number',
                'name'    => __( 'Max number of daily API calls', 'mz-mindbody-api' ),
                'desc'    => $this->describe_api_call_limit(),
                'default' => 2000
            )
        );

        // Field: Clear Transients
        self::$wposa_obj->add_field(
            'mz_mbo_advanced',
            array(
                'id'      => 'schedule_transient_duration',
                'type'    => 'number',
                'name'    => __( 'Seconds to store schedule transient for', 'mz-mindbody-api' ),
                'desc'    => __('Default is 43200 (12 hours - 60 x 60 x 12)', 'mz-mindbody-api'),
                'default' => 43200
            )
        );

        // Field: Display Substitute Status
        self::$wposa_obj->add_field(
            'mz_mbo_advanced',
            array(
                'id'      => 'elect_display_substitutes',
                'type'    => 'checkbox',
                'name'    => __( 'Display Class Sub Information', 'mz-mindbody-api' ),
                'desc'    => __( 'When checked, schedule display will contain information about class instructor substitution.', 'mz-mindbody-api' ),
                'default' => 'off'
            )
        );

        // Field: Template Override System
        self::$wposa_obj->add_field(
            'mz_mbo_advanced',
            array(
                'id'      => 'template_override_system',
                'type'    => 'html',
                'name'    => __( 'Template Override System', 'mz-mindbody-api' ),
                'desc'    => __( 'You can override any of the files in the plugin/frontend/views directory by placing them in your theme top-level directory within a directory named templates/mindbody.', 'mz-mindbody-api' )
            )
        );

        // Field: Display Allow "Remember Me" Cookie
        // self::$wposa_obj->add_field(
        //     'mz_mbo_advanced',
        //     array(
        //         'id'      => 'keep_loogged_in_cookie',
        //         'type'    => 'checkbox',
        //         'name'    => __( 'Allow "Keep Me Logged In" Cookie', 'mz-mindbody-api' ),
        //         'desc'  //   => __( 'When checked, visitors will have a "keep me logged in" checkbox when they log in.', 'mz-mindbody-api' )
        //     )
        // );

        // Field: Display Substitute Status
        // self::$wposa_obj->add_field(
        //     'mz_mbo_advanced',
        //     array(
        //         'id'      => 'register_within_site',
        //         'type'    => 'checkbox',
        //         'name'    => __( 'Keep Users In Site to Register for MBO, Classes, Events', 'mz-mindbody-api' ),
        //         'desc'    => __( 'When checked, users are not directed to MBO site for registering for classes and events.', 'mz-mindbody-api' ),
        //         'default' => 'off'
        //     )
        // );

        // Field: Display Substitute Status
        self::$wposa_obj->add_field(
            'mz_mbo_advanced',
            array(
                'id'      => 'log_api_calls',
                'type'    => 'checkbox',
                'name'    => __( 'Log MBO API Calls', 'mz-mindbody-api' ),
                'desc'    => __( 'When checked, API calls are logged for up to seven days in wp-content/mbo_api.log.', 'mz-mindbody-api' ),
                'default' => 'off'
            )
        );

        // Field: Regenerate Class Owners
        self::$wposa_obj->add_field(
            'mz_mbo_advanced',
            array(
                'id'      => 'reset_class_owners',
                'type'    => 'html',
                'name'    => __( 'Reset Class "Owners"', 'mz-mindbody-api' ),
                'desc'    => $this->reset_class_owners()
            )
        );

    }

    private function credentials_intro(){
        $return = '';
        $return .= '</p>'.sprintf(__('If you do not have them yet, visit the %1$s MindBodyOnline developers website %2$s and register for developer credentials.', 'mz-mindbody-api'),
                '<a href="https://api.mindbodyonline.com/Home/LogIn">',
                '</a>').'</p>';
        $return .= '(<a href="http://www.mzoo.org/creating-your-mindbody-credentials/">'. __('Detailed instructions here', 'mz-mindbody-api').'</a>.)';
        return $return;
    }

    private function schedule_codes(){
        $return = '';
        $return .= "<p>[mz-mindbody-show-schedule]</p>";
        $return .= "<p>".__('Attributes for schedule shortcodes. For boolean attributes use numeral 1 for true. Lists should be separated with commas', 'mz-miindbody-api').".</p>";
        $return .= "<ul>";
        $return .= "<li><strong>type</strong>: " . __("'week' or 'day'. Defaults to 'week'", 'mz-mindbody-api')."</li>";
        $return .= "<li><strong>locations</strong>: " . __("(numeric) List of IDs of MBO locations to display", 'mz-mindbody-api')."</li>";
        $return .= "<li><strong>account</strong>: " . __("(numeric) Which MBO account to display schedule for", 'mz-mindbody-api')."</li>";
        $return .= "<li><strong>filter</strong>: " . __("(boolean) Option to show a filter by class type, teacher.", 'mz-mindbody-api')."</li>";
        $return .= "<li><strong>grid</strong>: " . __("(boolean) Display schedule in grid format as opposed to list", 'mz-mindbody-api')."</li>";
        $return .= "<li><strong>advanced</strong>: " . __("(boolean) Allow users to sign in directory from wordpress site", 'mz-mindbody-api')."</li>";
        $return .= "<li><strong>hide</strong>: " . __("Remove any of following from display: teacher, signup, duration, session-type", 'mz-mindbody-api')."</li>";
        $return .= "<li><strong>session_types</strong>: " . __("List of MBO-registered session types to display. (Previously attribute was called 'class_types')", 'mz-mindbody-api')."</li>";
        $return .= "<li><strong>session_type_ids</strong>: " . __("List of MBO-registered SessionTypeIds to retrieve.", 'mz-mindbody-api')."</li>";
        $return .= "<li><strong>show_registrants</strong>: " . __("(boolean) If true, modal pop-up window will display registrants of class", 'mz-mindbody-api')."</li>";
        $return .= "<li><strong>hide_cancelled</strong>: " . __("(boolean) True will hide cancelled classes", 'mz-mindbody-api')."</li>";
        $return .= "<li><strong>registrants_count</strong>: " . __("(boolean) Display number of registrants in class", 'mz-mindbody-api')."</li>";
        $return .= "<li><strong>mode_select</strong>: " . __("(int) Allow user to select between grid and list view. 1 defaults to horizontal, 2 to grid.", 'mz-mindbody-api')."</li>";
        $return .= "</ul>";

        $return .= sprintf('<p>%1$s: [%2$s %3$s=%4$s %5$s="1, 2" %6$s=-99 %7$s="Meditation, Hot Yoga"]</p>',
            'Examples',
            'mz-mindbody-show-schedule', 'type', 'day', 'locations', 'account', 'class_types');
        $return .= __('Grid and Filter can be added like this:', 'mz-mindbody-api').'<br/>';
        $return .= sprintf(' [%1$s %2$s=1 %3$s=1]',
            'mz-mindbody-show-schedule', 'grid', 'filter');
        return $return;
    }

    private function staff_codes(){
        $return = '';
        $return .= '<p>'.sprintf('[%1$s]', 'mz-mindbody-staff-list').'</p>';
        $return .= "<ul>";
        $return .= "<li><strong>account</strong>: " . __("(int) Which MBO account to display staff for.", 'mz-mindbody-api')."</li>";
        $return .= "<li><strong>gallery</strong>: " . __("(boolean) Set to `1` to display as responsive grid.", 'mz-mindbody-api')."</li>";
        $return .= "<li><strong>hide</strong>: " . __("(string) Comma-separated string of staff names to not display. Case insensitive.", 'mz-mindbody-api')."</li>";
        $return .= "<li><strong>include_imageless</strong>: " . __("(boolean) Set to `1` to include staff members without photos. Default is not to display them.", 'mz-mindbody-api')."</li>";
        $return .= "</ul>";
        $return .= __('To display staff page as a responsive gallery of images with pop-up biographies, use ', 'mz-mindbody-api');
        $return .= sprintf('[%1$s %2$s]<br/>', 'mz-mindbody-staff-list', 'gallery=1');
        return $return;
    }

    private function event_codes(){
        $return = '';
        $return .= '<p>'.sprintf('[%1$s]', 'mz-mindbody-show-events').'</p>';
        $return .= "<ul>";
        $return .= "<li><strong>account</strong>: " . __("(int) Which MBO account to display events for.", 'mz-mindbody-api')."</li>";
        $return .= "<li><strong>locations</strong>: " . __("List of (int) MBO locations to display events for.", 'mz-mindbody-api')."</li>";
        $return .= "<li><strong>list</strong>: " . __("(boolean) Set to `1` to display events as a list.", 'mz-mindbody-api')."</li>";
        $return .= "<li><strong>event_count</strong>: " . __("(int) Limit the number of events to display.", 'mz-mindbody-api')."</li>";
        $return .= "<li><strong>location_filter</strong>: " . __("(boolean) Set to `1` to include location filter (if multiple locations included).", 'mz-mindbody-api')."</li>";
        $return .= "<li><strong>week-only</strong>: " . __("(boolean) Set to `1` to display events for current week only.", 'mz-mindbody-api')."</li>";
        $return .= "</ul>";
        return $return;
    }
    
    private function describe_api_call_limit(){
    	return  sprintf(__('<p>MBO charges for daily API calls over 1000. Number of calls today: %1$s.</p> <p>Site will email admin at 1200 calls less than this number and stop calling MBO at this number.</p> <p>Default: %2$s.</p>',  'mz-mindbody-api'),
            get_option('mz_mbo_api_calls')['calls'], 2000);
    }

    private function clear_transients(){
        $return = '<a href="#" class="button" id="mzClearTransients">' . __('Clear Transients', 'mz-mindbody-api') . '</a>';
        return $return;
    }

    private function update_mbo_site_token(){
        $return = '<a href="#" class="button" id="mzUpdateSiteToken">' . __('Update Site Token', 'mz-mindbody-api') . '</a>';
        return $return;
    }

    private function reset_class_owners(){

        $return = '<a href="#" class="class_owners button">' . __('Reset Class Owners', 'mz-mindbody-api') . '</a>';
        $return .= sprintf(__('<p>This is the matrix of which instructors %1$s "own" classes (MBO API doesn\'t tell us.) It is automatically regenerated daily with a cron job. 
                                You can see what it looks like by pressing this button and viewing output in the browser console.</p>',  'mz-mindbody-api'),
            '<em>probably</em>');

        return $return;
    }

    private function mz_mindbody_debug_text() {
        return '<a href="#" class="button" id="mzTestCredentials">' . __('Test Credentials', 'mz-mindbody-api') . '</a><div id="displayTest"></div>';
    }

}
