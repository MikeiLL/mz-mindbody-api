<?php
/**
 * This file contains the class with methods to handle the [mz-mindbody-show-schedule] shortcode.
 *
 * It has been used to display php objects, text, arrays for view in the browser.
 *
 * @package MzMindbody
 */

namespace MZoo\MzMindbody\Schedule;

use MZoo\MzMindbody as NS;
use MZoo\MzMindbody\Core as Core;
use MZoo\MzMindbody\Common as Common;
use MZoo\MzMindbody\Common\Interfaces as Interfaces;

/**
 * Schedule Display Shortcode class
 *
 * Display MBO schedule in a page via shortcode.
 */
class Display extends Interfaces\ShortcodeScriptLoader {

    /**
     * If shortcode script has been enqueued.
     *
     * @since  2.4.7
     * @access private
     *
     * @used in handle_shortcode, addScript
     * @var  boolean $added_already True if shorcdoe scripts have been enqueued.
     */
    private static $added_already = false;

    /**
     * Table styling option holders
     *
     * @since  2.4.7
     * @access public
     *
     * @var string
     */
    public $table_class;

    /**
     * Horizontal Class
     *
     * @since  2.4.7
     * @access public
     *
     * @var string $horizontal_class CSS Class for horizontal style display.
     */
    public $horizontal_class;

    /**
     * Grid Class
     *
     * @since  2.4.7
     * @access public
     *
     * @var string $grid_class CSS Class for grid/calendar style display.
     */
    public $grid_class;

    /**
     * Text for Mode Select Button when not yet toggled
     *
     * @since  2.4.7
     * @access public
     *
     * @var string $initial_button_text Button text pre any javascript updates.
     */
    public $initial_button_text;

    /**
     * Location ID from MBO.
     *
     * Single-location accounts this will probably be a one. Used in generating URL for class sign-up.
     *
     * @since  2.4.7
     * @access public
     * @var    int $studio_location_id Which MBO location this schedule item occurs at.
     */
    public $studio_location_id;

    /**
     * Text for Mode Select Button when toggled
     *
     * @since  2.4.7
     * @access public
     *
     * @var string $swap_button_text Text in swap button.
     */
    public $swap_button_text;

    /**
     * Data Target
     *
     * @since  2.4.7
     * @access public
     *
     * @var string $data_target Which modal target to use for modal pop-up.
     */
    public $data_target;

    /**
     * Class Modal Link
     *
     * @since  2.4.7
     * @access public
     *
     * @var string $class_modal_link which modal include display file to select.
     */
    public $class_modal_link;


    /**
     * Instance of RetrieveSchedule.
     *
     * @since     2.4.7
     * @access    public
     * @populated in handle_shortcode
     * @used      in handle_shortcode, localize_script, display_schedule.
     * @var       object $schedule_object Instance of RetrieveSchedule.
     */
    public $schedule_object;

    /**
     * Site ID
     *
     * Used in Display Schedule sent to studioID in show schedule button in teacher modal.
     * Might be same as account.
     *
     * @since  2.4.7
     * @access public
     * @var    int $site_id
     */
    public $site_id;

    /**
     * Shortcode attributes.
     *
     * @since  2.4.7
     * @access public
     *
     * @used in handle_shortcode, localize_script, display_schedule
     * @var  array $atts Shortcode attributes function called with.
     */
    public $atts;

    /**
     * Data to send to template
     *
     * @since  2.4.7
     * @access public
     *
     * @used in handle_shortcode, display_schedule
     * @var  @array    $data    array to send template.
     */
    public $template_data;

    /**
     * Which type of schedule to display
     *
     * Will be 'horizontal' (default), 'grid' or 'both'.
     *
     * @since  2.4.7
     * @access public
     *
     * @used in handle_shortcode, display_schedule
     * @var  string    $display_type    Which type of schedule to display.
     */
    public $display_type;

    /**
     * Which schedule_event items not to display
     *
     * Teacher, Location, Sign-up, Duration
     *
     * @since  2.4.7
     * @access public
     *
     * @used in handle_shortcode, display_schedule
     * @var  array    $hide    Which elements to not display in schedule.
     */
    public $hide;

    /**
     * Handle Shortcode
     *
     * Bulk of the work happens here, and in the similar function below which does
     * a lot of duplication for the Ajax calls.
     *
     * @param  string $atts    shortcode inputs.
     * @param  string $content any content between start and end shortcode tags.
     * @return string shortcode content.
     */
    public function handle_shortcode( $atts, $content = null ) {
        $this->atts = shortcode_atts(
            array(
                'type'                  => 'week',
                'location'              => '', // stop using this eventually, in preference "int, int" format.
                'locations'             => 1,
                'account'               => 0,
                'filter'                => 0,
                'hide_cancelled'        => 0,
                'grid'                  => 0,
                'advanced'              => 0,
                'this_week'             => 0,
                'hide'                  => '',
                'class_types'           => '', // migrating to session_types 2019 December(ish).
                'session_types'         => '',
                'session_type_ids'      => '',
                'show_registrants'      => 0,
                'calendar_format'       => 'horizontal',
                'schedule_type'         => null, // allow over-ridding of global setting.
                'show_registrants'      => 0,
                'registrants_count'     => 0,
                'classesByDateThenTime' => array(),
                'mode_select'           => 0,
                'unlink'                => 0,
                'offset'                => 0,
            ),
            $atts
        );

        // Set siteID to option if not set explicitly in shortcode.
        $this->site_id = ( isset( $atts['account'] ) ) ? $atts['account'] : Core\MzMindbodyApi::$basic_options['mz_mindbody_siteID'];

        $this->class_modal_link = NS\PLUGIN_NAME_URL . 'src/Frontend/views/modals/modal_descriptions.php';

        // Support the old 'class_types' shortcode attr.
        $this->atts['session_types'] = ! empty( $this->atts['class_types'] ) ? $this->atts['class_types'] : $this->atts['session_types'];

        // If set, turn Session/Class Types into an Array and call it session_types.
        if ( '' !== $this->atts['session_types'] ) {
            if ( ! is_array( $this->atts['session_types'] ) ) { // if not already an array.
                $this->atts['session_types'] = explode( ',', $this->atts['session_types'] );
            }
            // TODO: is this sometimes done reduntantly?
            foreach ( $this->atts['session_types'] as $key => $type ) :
                $this->atts['session_types'][ $key ] = trim( $type );
            endforeach;
        }

        // If set, turn Session/Class Types into an Array and call it session_types.
        if ( '' !== $this->atts['session_type_ids'] ) {
            if ( ! is_array( $this->atts['session_type_ids'] ) ) { // if not already an array.
                $this->atts['session_type_ids'] = explode( ',', $this->atts['session_type_ids'] );
            }
            // TODO: is this sometimes done reduntantly?
            foreach ( $this->atts['session_type_ids'] as $key => $type ) :
                $this->atts['session_type_ids'][ $key ] = trim( $type );
            endforeach;
        }

        // Break locations up into array, if it hasn't already been.
        if ( ! is_array( $this->atts['locations'] ) ) {
            $this->atts['locations'] = explode( ',', str_replace( ' ', '', $this->atts['locations'] ) );
        }
        // Set sLoc which we use in generating link, to first location in locations array.
        $this->studio_location_id = $this->atts['locations'][0];

        // Break hide up into array, if it hasn't already been.
        if ( ! is_array( $this->atts['hide'] ) ) {
            $this->atts['hide'] = explode( ',', str_replace( ' ', '', strtolower( $this->atts['hide'] ) ) );
        }

        // Begin generating output.
        ob_start();

        $template_loader       = new Core\TemplateLoader();
        $this->schedule_object = new RetrieveSchedule( $this->atts );

        // Call the API and if fails, return error message.
        if ( false === $this->schedule_object->get_mbo_results() ) {
            return '<div>' . __( 'Error returning schedule from MBO for shortcode display.', 'mz-mindbody-api' ) . '</div>';
        }

        // echo "<script>console.log(" . json_encode($this->schedule_object) . ")</script>";

        /**
         * Configure the display type based on shortcode atts.
         */
        $this->display_type = ( ! empty( $atts['grid'] ) ) ? 'grid' : 'horizontal';

        // If mode_select is on, render both grid and horizontal.
        if ( ! empty( $atts['mode_select'] ) ) {
            $this->display_type = 'both';
        }

        // Define styling variables based on shortcode attribute values.
        $this->table_class = ( 1 === (int) $this->atts['filter'] ) ? 'mz-schedule-filter' : 'mz-schedule-table';

        if ( 1 === (int) $this->atts['mode_select'] ) :
            $this->grid_class          = ' mz_hidden';
            $this->horizontal_class    = '';
            $this->initial_button_text = __( 'Grid View', 'mz-mindbody-api' );
            $this->swap_button_text    = __( 'Horizontal View', 'mz-mindbody-api' );
        elseif ( 2 === (int) $this->atts['mode_select'] ) :
            $this->horizontal_class    = ' mz_hidden';
            $this->grid_class          = '';
            $this->initial_button_text = __( 'Horizontal View', 'mz-mindbody-api' );
            $this->swap_button_text    = __( 'Grid View', 'mz-mindbody-api' );
        else :
            $this->horizontal_class    = '';
            $this->grid_class          = '';
            $this->initial_button_text = 0;
            $this->swap_button_text    = 0;
        endif;

        /*
        *
        * Determine which type(s) of schedule(s) need to be configured
        *
        * The schedules are not class objects because they change depending on
        * (date) offset value when they are called.
        */

        // Initialize the variables, so won't be un-set.
        $horizontal_schedule = '';
        $grid_schedule       = '';

        // If we actually have classes
        if (!empty($this->schedule_object->classes)){
            if ( 'grid' === $this->display_type || 'both' === $this->display_type ) :
                $grid_schedule = $this->schedule_object->sortClassesByTimeThenDate();
            endif;
            if ( 'horizontal' === $this->display_type || 'both' === $this->display_type ) :
                $horizontal_schedule = $this->schedule_object->sortClassesByDateThenTime();
            endif;
        }

        $week_names = array(
            __( 'Sunday', 'mz-mindbody-api' ),
            __( 'Monday', 'mz-mindbody-api' ),
            __( 'Tuesday', 'mz-mindbody-api' ),
            __( 'Wednesday', 'mz-mindbody-api' ),
            __( 'Thursday', 'mz-mindbody-api' ),
            __( 'Friday', 'mz-mindbody-api' ),
            __( 'Saturday', 'mz-mindbody-api' ),
        );

        /*
        * If wordpress-configured week starts on Monday instead of Sunday,
        * we shift our week names array.
        */
        if ( 0 !== (int) Core\MzMindbodyApi::$start_of_week ) {
            array_push( $week_names, array_shift( $week_names ) );
        }

        $this->template_data = array(
            'atts'                 => $this->atts,
            'data_target'          => $this->data_target,
            'grid_class'           => $this->grid_class,
            'horizontal_class'     => $this->horizontal_class,
            'initial_button_text'  => $this->initial_button_text,
            'swap_button_text'     => $this->swap_button_text,
            'time_format'          => $this->schedule_object->time_format,
            'date_format'          => $this->schedule_object->date_format,
            'site_id'              => $this->site_id,
            'week_names'           => $week_names,
            'start_date'           => $this->schedule_object->start_date,
            'end_date'             => $this->schedule_object->current_week_end,
            'display_type'         => $this->display_type,
            'hide'                 => $this->atts['hide'],
            'table_class'          => $this->table_class,
            'locations_dictionary' => $this->schedule_object->locations_dictionary,
            'login'                => NS\MZMBO()->i18n->get( 'login' ),
            'login_to_sign_up'     => NS\MZMBO()->i18n->get( 'login_to_sign_up' ),
            'registration_button'  => NS\MZMBO()->i18n->get( 'registration_button' ),
            'username'             => NS\MZMBO()->i18n->get( 'username' ),
            'password'             => NS\MZMBO()->i18n->get( 'password' ),
            'manage_on_mbo'        => NS\MZMBO()->i18n->get( 'manage_on_mbo' ),
            'horizontal_schedule'  => $horizontal_schedule,
            'grid_schedule'        => $grid_schedule,
        );

        $template_loader->set_template_data( $this->template_data );
        $template_loader->get_template_part( 'schedule_container' );

        // Add Scripts and Style with script adder.
        self::addScript();

        return ob_get_clean();
    }

    /**
     * Add Script.
     *
     * Add scripts if not added already.
     *
     * @return void
     */
    public function addScript() {
        if ( ! self::$added_already ) {
            self::$added_already = true;

            wp_register_style( 'mz_mindbody_style', NS\PLUGIN_NAME_URL . 'dist/styles/main.css' );
            wp_enqueue_style( 'mz_mindbody_style' );

            wp_register_script( 'mz_mbo_bootstrap_script', NS\PLUGIN_NAME_URL . 'dist/scripts/main.js', array( 'jquery' ), NS\PLUGIN_VERSION, true );
            wp_enqueue_script( 'mz_mbo_bootstrap_script' );

            wp_register_script( 'mz_display_schedule_script', NS\PLUGIN_NAME_URL . 'dist/scripts/schedule-display.js', array( 'jquery', 'mz_mbo_bootstrap_script' ), NS\PLUGIN_VERSION, true );
            wp_enqueue_script( 'mz_display_schedule_script' );

            if ( 1 === (int) $this->atts['filter'] ) :
                wp_register_script( 'filterTable', NS\PLUGIN_NAME_URL . 'dist/scripts/mz_filtertable.js', array( 'jquery', 'mz_display_schedule_script' ), NS\PLUGIN_VERSION, true );
                wp_enqueue_script( 'filterTable' );
            endif;

            $this->localize_script();
        }
    }

    /**
     * Localize Script.
     *
     * Send required variables as javascript object.
     *
     * @return void
     */
    public function localize_script() {
        // Clean out unneeded strings from Locations Dictionary.
        $locations_dictionary = $this->schedule_object->locations_dictionary;
        foreach ( $locations_dictionary as $k => $v ) {
            unset( $locations_dictionary[ $k ]['link'] );
        }

        $protocol = isset( $_SERVER['HTTPS'] ) ? 'https://' : 'http://';

        $translated_strings = NS\MZMBO()->i18n->get();

        $params = array(
            'ajaxurl'                => admin_url( 'admin-ajax.php', $protocol ),
            // Used in display_schedule below.
            'display_schedule_nonce' => wp_create_nonce( 'mz_display_schedule' ),
            'atts'                   => $this->atts,
            'with'                   => __( 'with', 'mz-mindbody-api' ),
            'account'                => Core\MzMindbodyApi::$basic_options['mz_mindbody_siteID'],
            'initial'                => $this->initial_button_text,
            'mode_select'            => $this->atts['mode_select'],
            'swap'                   => $this->swap_button_text,
            'registrants_header'     => __( 'Registrants', 'mz-mindbody-api' ),
            'get_registrants_error'  => __( 'Error retrieving class details.', 'mz-mindbody-api' ),
            'error'                  => __( 'Sorry but there was an error retrieving the schedule.', 'mz-mindbody-api' ),
            'sub_by_text'            => __( 'substitute for', 'mz-mindbody-api' ),
            'no_bio'                 => __( 'No biography listed for this staff member.', 'mz-mindbody-api' ),
            'filter_default'          => __( 'by teacher, class type', 'mz-mindbody-api' ),
            'quick_1'                => __( 'morning', 'mz-mindbody-api' ),
            'quick_2'                => __( 'afternoon', 'mz-mindbody-api' ),
            'quick_3'                => __( 'evening', 'mz-mindbody-api' ),
            'label'                  => __( 'Filter', 'mz-mindbody-api' ),
            'selector'               => __( 'All Locations', 'mz-mindbody-api' ),
            'login'                  => $translated_strings['login'],
            'signup'                 => $translated_strings['sign_up'],
            'confirm_signup'          => $translated_strings['confirm_signup'],
            'logout'                 => $translated_strings['logout'],
            'signup_heading'         => $translated_strings['signup_heading'],
            'Locations_dict'         => wp_json_encode( $locations_dictionary ),
            'site_id'                => $this->site_id,
            'location'               => $this->studio_location_id,
        );
        wp_localize_script( 'mz_display_schedule_script', 'mz_mindbody_schedule', $params );
    }

    /**
     * Ajax function to return mbo schedule
     *
     * @since 2.4.7
     *
     * This duplicates a lot of the handle_shortcode function, but
     * is called via AJAX and used when navigating the schedule.
     *
     *
     *
     * Echo json wp_json_encode() version of HTML from template
     */
    public function display_schedule() {
        if ( isset( $_POST['nonce'] ) ) {
            $nonce = wp_unslash( $_POST['nonce'] );
        } else {
            \wp_send_json_error( 'Missing nonce' );
            \wp_die();
        }

        // Generated in localize_script above.
        if ( ! \wp_verify_nonce( $nonce, 'mz_display_schedule' ) ) {
            \wp_send_json_error( $this->invalid_nonce_feedback );
            \wp_die();
        }

        $atts = $_REQUEST['atts'];

        $result['type'] = 'success';

        $template_loader = new Core\TemplateLoader();

        $this->schedule_object = new RetrieveSchedule( $atts );

        // Call the API and if fails, return error message.
        if ( false === $this->schedule_object->get_mbo_results() ) {
            echo '<div>' . __( 'Error returning schedule from MBO for display.', 'mz-mindbody-api' ) . '</div>';
        }

        // Register attributes.
        $this->handle_shortcode( $atts );

        // Update the data array.
        $this->template_data['time_format'] = $this->schedule_object->time_format;
        $this->template_data['date_format'] = $this->schedule_object->date_format;

        $template_loader->set_template_data( $this->template_data );

        // Initialize the variables, so won't be un-set.
        $horizontal_schedule = '';
        $grid_schedule       = '';
        if ( 'grid' === $this->display_type || 'both' === $this->display_type ) :
            ob_start();
            $grid_schedule = $this->schedule_object->sortClassesByTimeThenDate();
            // Update the data array.
            $this->template_data['grid_schedule'] = $grid_schedule;
            $template_loader->get_template_part( 'grid_schedule' );
            $result['grid'] = ob_get_clean();
        endif;

        if ( 'horizontal' === $this->display_type || 'both' === $this->display_type ) :
            ob_start();
            $horizontal_schedule = $this->schedule_object->sortClassesByDateThenTime();
            // Update the data array.
            $this->template_data['horizontal_schedule'] = $horizontal_schedule;
            $template_loader->get_template_part( 'horizontal_schedule' );
            $result['horizontal'] = ob_get_clean();
        endif;

        $result['message'] = __( 'Error. Please try again.', 'mz-mindbody-api' );

        if ( ! empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) &&
            'xmlhttprequest' === strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) ) {
            \wp_send_json_success( $result );
            \wp_die();
        } else {
            header( 'Location: ' . $_SERVER['HTTP_REFERER'] );
        }

        die();
    }
}
