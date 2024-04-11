<?php
/**
 * This file contains the class with methods to handle the [mz-mindbody-show-events] shortcode.
 *
 * It has been used to display php objects, text, arrays for view in the browser.
 *
 * @package MzMindbody
 */

namespace MZoo\MzMindbody\Events;

use MZoo\MzMindbody as NS;
use MZoo\MzMindbody\Core as Core;
use MZoo\MzMindbody\Common as Common;
use MZoo\MzMindbody\Common\Interfaces as Interfaces;

/**
 * Events Display Shortcode class
 *
 * Display MBO events in a page via shortcode.
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
     * Events object.
     *
     * @since  2.4.7
     * @access public
     *
     * @used in handle_shortcode, get_events_modal
     * @var  object $events_object The class that retrieves the MBO events.
     */
    public $events_object;

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
     * Event Location
     *
     * @since 1.0.0
     *
     * For backwards compatibility
     *
     * @access public
     * @var    $location array of locations to display events for
     */
    public $location;

    /**
     * Event Locations
     *
     * @since 2.0.0 (estimate)
     *
     * @access public
     * @var    $locations array of locations to display events for
     */
    public $locations;

    /**
     * Event List Only View
     *
     * @since 2.0.0 (estimate)
     *
     * @access public
     * @var    $list_only boolean True indicates to just display a list of event Name, Date and Times
     */
    public $list_only;

    /**
     * Event Count
     *
     * @since 2.0.0 (estimate)
     *
     * @access public
     * @var    $event_count int number of events returned per request to MBO
     */
    public $event_count;

    /**
     * Event Account
     *
     * Default in Options, overridden in shortcode atts
     *
     * @since 2.0.0 (estimate)
     *
     * @access public
     * @var    $account int MBO account to retrieve events from.
     */
    public $account;

    /**
     * Event Single Week DIsplay
     *
     * @since 2.0.0 (estimate)
     *
     * @access public
     * @var    $week_only boolean True indicates to only display a weeks worth of events, starting with current day
     */
    public $week_only;

    /**
     * Event Locations
     *
     * @since 2.0.0 (estimate)
     *
     * @access public
     * @var    $locations array of locations to display events for
     */
    public $number_of_events;

    /**
     * Client ID
     *
     * @since  2.4.7
     * @access public
     *
     * @used in handle_shortcode, displayEvents
     * @var  @array
     */
    public $client_id;

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
     * Handle Shortcode
     *
     * @param  string $atts    shortcode inputs.
     * @param  string $content any content between start and end shortcode tags.
     * @return string shortcode content.
     */
    public function handle_shortcode( $atts, $content = null ) {

        $this->atts = shortcode_atts(
            array(
                'location'        => '1',
                'locations'       => '1',
                'list'            => 0,
                'event_count'     => '0',
                'account'         => 0,
                'week-only'       => 0,
                'offset'          => 0,
                'location_filter' => 0,
            ),
            $atts
        );

        // Set siteID to option unless set explicitly in shortcode.
        $this->site_id = Core\MzMindbodyApi::$basic_options['mz_mindbody_siteID'];
        if ( ! empty( $atts['account'] ) ) {
            $atts['account'];
        }

        // Break locations up into array, if it hasn't already been.
        if ( ! is_array( $this->atts['locations'] ) ) {
            $this->atts['locations'] = explode( ',', str_replace( ' ', '', $this->atts['locations'] ) );
            // Coerce to integers so we can use strict testing.
            $this->atts['locations'] = array_map( 'intval', $this->atts['locations'] );
        }

        ob_start();

        $template_loader = new Core\TemplateLoader();

        $this->events_object = new RetrieveEvents( $this->atts );

        // Call the API and if fails, return error message.
        $response = $this->events_object->get_mbo_results();
        if ( empty( $response ) ) {
            return '<div>' . __( 'Error returning events from Mindbody.', 'mz-mindbody-api' ) . '</div>';
        }

        // Add Style with script adder.
        self::addScript();

        $events = ( count( $response ) >= 1 ) ? $response : __( 'No Events in current cycle', 'mz-mindbody-api' );

        $events = $this->events_object->sortEventsByTime();

        $this->template_data = array(
            'atts'                 => $this->atts,
            'events'               => $events,
            'display_time_frame'   => $this->events_object->display_time_frame,
            'locations_dictionary' => $this->events_object->locations_dictionary,
            'locations_count'      => count( $this->atts['locations'] ),
            'no_events'            => NS\MZMBO()->i18n->get( 'no_events_this_period' ),
            'heading_date'         => __( 'Date', 'mz_mindbody-api' ),
            'heading_time'         => __( 'Time', 'mz_mindbody-api' ),
            'heading_event'        => __( 'Event', 'mz_mindbody-api' ),
            'heading_location'     => __( 'Location', 'mz_mindbody-api' ),
            'site_id'              => $this->site_id,
            'with'                 => NS\MZMBO()->i18n->get( 'with' ),
            'login'                => NS\MZMBO()->i18n->get( 'login' ),
            'login_to_sign_up'     => NS\MZMBO()->i18n->get( 'login_to_sign_up' ),
            // 'signup_nonce'         => wp_create_nonce('mz_signup_nonce'), // out of use.
            'registration_button'  => NS\MZMBO()->i18n->get( 'registration_button' ),
            'username'             => NS\MZMBO()->i18n->get( 'username' ),
            'password'             => NS\MZMBO()->i18n->get( 'password' ),
            'manage_on_mbo'        => NS\MZMBO()->i18n->get( 'manage_on_mbo' ),
            'all_locations_copy'   => NS\MZMBO()->i18n->get( 'all_locations_copy' ),
        );

        $template_loader->set_template_data( $this->template_data );
        $template_loader->get_template_part( 'event_listing_container' );

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

            wp_register_style(
                'mz_mindbody_style',
                NS\PLUGIN_NAME_URL . 'dist/styles/main.css'
            );
            wp_enqueue_style( 'mz_mindbody_style' );

            wp_register_script(
                'mz_mbo_bootstrap_script',
                NS\PLUGIN_NAME_URL . 'dist/scripts/main.js',
                array( 'jquery' ),
                NS\PLUGIN_VERSION,
                true
            );
            wp_enqueue_script( 'mz_mbo_bootstrap_script' );

            wp_register_script(
                'mz_mbo_events',
                NS\PLUGIN_NAME_URL . 'dist/scripts/events-display.js',
                array( 'jquery' ),
                NS\PLUGIN_VERSION,
                true
            );
            wp_enqueue_script( 'mz_mbo_events' );

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

        $protocol = isset( $_SERVER['HTTPS'] ) ? 'https://' : 'http://';

        $translated_strings = NS\MZMBO()->i18n->get();

        $params = array(
            'ajaxurl'        => admin_url( 'admin-ajax.php', $protocol ),
            // Used in displayEvents below.
            'nonce'          => wp_create_nonce( 'mz_events_display_nonce' ),
            'atts'           => $this->atts,
            'account'        => Core\MzMindbodyApi::$basic_options['mz_mindbody_siteID'],
            'error'          => __( 'Sorry but there was an error retrieving the events.', 'mz-mindbody-api' ),
            'no_bio'         => __( 'No biography listed for this staff member.', 'mz-mindbody-api' ),
            'login'          => $translated_strings['login'],
            'signup'         => $translated_strings['sign_up'],
            'confirm_signup' => $translated_strings['confirm_signup'],
            'logout'         => $translated_strings['logout'],
            'signup_heading' => $translated_strings['signup_heading'],
            'Locations_dict' => wp_json_encode( $this->events_object->locations_dictionary ),
            // 'signup_nonce'   => wp_create_nonce('mz_signup_nonce'), out of use.
            'site_id'        => $this->site_id,
            'location'       => $this->studio_location_id,
            'with'           => NS\MZMBO()->i18n->get( 'with' ),
        );
        wp_localize_script( 'mz_mbo_events', 'mz_mindbody_schedule', $params );
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
    public function displayEvents() {

        ob_start();

        // Generated in handle_shortcode above, template_data.
        check_ajax_referer( 'mz_events_display_nonce', 'nonce' );

        $atts = $_REQUEST['atts'];

        $result['type'] = 'success';

        $template_loader = new Core\TemplateLoader();

        $this->events_object = new RetrieveEvents( $atts );

        // Register attributes.
        $this->handle_shortcode( $atts );

        // Call the API and if fails, return error message.
        $response = $this->events_object->get_mbo_results();
        if ( empty( $response ) ) {
            $result  = '<div>';
            $result .= __( 'Error returning events to display from Minbbody.', 'mz-mindbody-api' );
            $result .= '</div>';
            return $result;
        }

        $events = $this->events_object->sortEventsByTime();

        // Assign the date range to the $result.
        $date_range           = $this->events_object->display_time_frame;
        $result['date_range'] = sprintf(
            // translators: Explanation displaying a range of events between start, end date.
            __( 'Displaying events from %1$s to %2$s.', 'mz-mindbody-api' ),
            $date_range['start']->format( 'F j' ),
            $date_range['end']->format( 'F j' )
        );

        // Update the data array.
        $this->template_data['events'] = $events;
        $this->template_data['atts']   = $atts;

        $template_loader->set_template_data( $this->template_data );
        if ( 1 !== (int) $atts['list'] ) :
            $template_loader->get_template_part( 'event_listing_full' );
        else :
            $template_loader->get_template_part( 'event_listing_list' );
        endif;

        $result['message'] = ob_get_clean();

        if ( ( ! empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) )
            && ( 'xmlhttprequest' === strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) )
        ) {
            $result = wp_json_encode( $result );
            echo $result;
        } else {
            header( 'Location: ' . $_SERVER['HTTP_REFERER'] );
        }

        die();
    }
}
