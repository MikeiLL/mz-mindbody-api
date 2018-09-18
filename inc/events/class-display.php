<?php
namespace MZ_Mindbody\Inc\Events;

use MZ_Mindbody as NS;
use MZ_Mindbody\Inc\Core as Core;
use MZ_Mindbody\Inc\Common as Common;
use MZ_Mindbody\Inc\Common\Interfaces as Interfaces;

class Display extends Interfaces\ShortCode_Script_Loader
{
    /**
     * If shortcode script has been enqueued.
     *
     * @since    2.4.7
     * @access   public
     *
     * @used in handleShortcode, addScript
     * @var      boolean $addedAlready True if shorcdoe scripts have been enqueued.
     */
    static $addedAlready = false;

    /**
     * Shortcode attributes.
     *
     * @since    2.4.7
     * @access   public
     *
     * @used in handleShortcode, localizeScript, display_schedule
     * @var      array $atts Shortcode attributes function called with.
     */
    public $atts;

    /**
     * Events object.
     *
     * @since    2.4.7
     * @access   public
     *
     * @used in handleShortcode, get_events_modal
     * @var      object $events_object The class that retrieves the MBO events.
     */
    public $events_object;

    /**
     * Data to send to template
     *
     * @since    2.4.7
     * @access   public
     *
     * @used in handleShortcode, display_schedule
     * @var      @array    $data    array to send template.
     */
    public $template_data;

    /**
     * Event Location
     *
     * @since 1.0.0
     *
     * For backwards compatibility
     *
     * @access public
     * @var $location array of locations to display events for
     */
    public $location;

    /**
     * Event Locations
     *
     * @since 2.0.0 (estimate)
     *
     * @access public
     * @var $locations array of locations to display events for
     */
    public $locations;

    /**
     * Event List Only View
     *
     * @since 2.0.0 (estimate)
     *
     * @access public
     * @var $list_only boolean True indicates to just display a list of event Name, Date and Times
     */
    public $list_only;

    /**
     * Event Count
     *
     * @since 2.0.0 (estimate)
     *
     * @access public
     * @var $event_count int number of events returned per request to MBO
     */
    public $event_count;

    /**
     * Event Account
     *
     * @since 2.0.0 (estimate)
     *
     * @access public
     * @var $account int The MBO account to retrieve and display events from. Default in Options, overridden in shortcode atts
     */
    public $account;

    /**
     * Event Single Week DIsplay
     *
     * @since 2.0.0 (estimate)
     *
     * @access public
     * @var $week_only boolean True indicates to only display a weeks worth of events, starting with current day
     */
    public $week_only;

    /**
     * Event Locations
     *
     * @since 2.0.0 (estimate)
     *
     * @access public
     * @var $locations array of locations to display events for
     */
    public $number_of_events;

    /**
     *
     *
     * @since    2.4.7
     * @access   public
     *
     * @used in handleShortcode, display_events
     * @var      @array
     */
    public $clientID;

    /**
     * Site ID
     *
     * Used in Display Schedule sent to studioID in show schedule button in teacher modal.
     * Might be same as account.
     *
     * @since    2.4.7
     * @access   public
     * @var      int $siteID
     */
    public $siteID;

    public function handleShortcode($atts, $content = null)
    {

        $this->atts = shortcode_atts( array(
            'location' => '1',
            'locations' => '',
            'list' => 0,
            'event_count' => '0',
            'account' => 0,
            'week-only' => 0,
            'offset' => 0,
            'location_filter' => 0
        ), $atts );

        // Set siteID to option if not set explicitly in shortcode
        $this->siteID = (!empty($atts['account'])) ? $atts['account'] : Core\MZ_Mindbody_Api::$basic_options['mz_mindbody_siteID'];

        // Break locations up into array, if it hasn't already been.
        $this->atts['locations'] = (!is_array($this->atts['locations'])) ? explode(',', str_replace(' ', '', $this->atts['locations'])) : $this->atts['locations'];

        ob_start();

        $template_loader = new Core\Template_Loader();

        $this->events_object = new Retrieve_Events($this->atts);

        // Call the API and if fails, return error message.
        if (!$response = $this->events_object->get_mbo_results()) return "<div>" . __("Mindbody plugin settings error.", 'mz-mindbody-api') . "</div>";
        // Add Style with script adder
        self::addScript();

        $events = ($response['GetClassesResult']['ResultCount'] >= 1) ? $response['GetClassesResult']['Classes']['Class'] : __('No Events in current cycle', 'mz-mindbody-api');

        $events = $this->events_object->sort_events_by_time();

        $this->template_data = array(
            'atts' => $this->atts,
            'events' => $events,
            'display_time_frame' => $this->events_object->display_time_frame,
            'locations_dictionary' => $this->events_object->locations_dictionary,
            'locations_count' => count($this->atts['locations']),
            'no_events' => NS\MZMBO()->i18n->get('no_events_this_period'),
            'heading_date' => __('Date', 'mz_mindbody-api'),
            'heading_time' => __('Time', 'mz_mindbody-api'),
            'heading_event' => __('Event', 'mz_mindbody-api'),
            'heading_location' => __('Location', 'mz_mindbody-api'),
            'siteID' => $this->siteID,
            'with' => NS\MZMBO()->i18n->get('with'),
            'login' => NS\MZMBO()->i18n->get('login'),
            'login_to_sign_up' => NS\MZMBO()->i18n->get('login_to_sign_up'),
            'signup_nonce' => wp_create_nonce('mz_signup_nonce'),
            'registration_button' => NS\MZMBO()->i18n->get('registration_button'),
            'username' => NS\MZMBO()->i18n->get('username'),
            'password' => NS\MZMBO()->i18n->get('password'),
            'manage_on_mbo' => NS\MZMBO()->i18n->get('manage_on_mbo'),
            'all_locations_copy' => NS\MZMBO()->i18n->get('all_locations_copy')
        );

        $template_loader->set_template_data($this->template_data);
        $template_loader->get_template_part('event_listing_container');

        return ob_get_clean();
    }

    public function addScript()
    {
        if (!self::$addedAlready) {
            self::$addedAlready = true;

            wp_register_style('mz_mindbody_style', NS\PLUGIN_NAME_URL . 'dist/styles/main.css');
            wp_enqueue_style('mz_mindbody_style');

            wp_register_script('mz_mbo_bootstrap_script', NS\PLUGIN_NAME_URL . 'dist/scripts/main.js', array('jquery'), 1.0, true);
            wp_enqueue_script('mz_mbo_bootstrap_script');

            wp_register_script('mz_mbo_events', NS\PLUGIN_NAME_URL . 'dist/scripts/events-display.js', array('jquery'), 1.0, true);
            wp_enqueue_script('mz_mbo_events');

            $this->localizeScript();
        }
    }

    public function localizeScript()
    {

        $protocol = isset($_SERVER["HTTPS"]) ? 'https://' : 'http://';

        $translated_strings = NS\MZMBO()->i18n->get();

        $params = array(
            'ajaxurl' => admin_url('admin-ajax.php', $protocol),
            'nonce' => wp_create_nonce('mz_events_display_nonce'),
            'atts' => $this->atts,
            'account' => Core\MZ_Mindbody_Api::$basic_options['mz_mindbody_siteID'],
            'error' => __('Sorry but there was an error retrieving the events.', 'mz-mindbody-api'),
            'no_bio' => __('No biography listed for this staff member.', 'mz-mindbody-api'),
            'login' => $translated_strings['login'],
            'signup' => $translated_strings['sign_up'],
            'confirm_signup' => $translated_strings['confirm_signup'],
            'logout' => $translated_strings['logout'],
            'signup_heading' => $translated_strings['signup_heading'],
            'Locations_dict' => json_encode($locations_dictionary),
            'signup_nonce' =>  wp_create_nonce('mz_signup_nonce'),
            'loggedMBO' => ( 1 == (bool) NS\MZMBO()->session->get('MBO_GUID') ) ? 1 : 0,
            'siteID' => $this->siteID,
            'location' => $this->sLoc,
            'with' => NS\MZMBO()->i18n->get('with'),
            'client_first_name' => (!empty(NS\MZMBO()->session->get('MBO_GUID')['FirstName']) ? NS\MZMBO()->session->get('MBO_GUID')['FirstName'] : '')
        );
        wp_localize_script('mz_mbo_events', 'mz_mindbody_schedule', $params);
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
     * Echo json json_encode() version of HTML from template
     */
    public function display_events()
    {

        ob_start();

        check_ajax_referer($_REQUEST['nonce'], "mz_events_display_nonce", false);

        $atts = $_REQUEST['atts'];

        $result['type'] = "success";

        $template_loader = new Core\Template_Loader();

        $this->events_object = new Retrieve_Events($atts);

        // Register attributes
        $this->handleShortcode($atts);

        // Call the API and if fails, return error message.
        if (!$response = $this->events_object->get_mbo_results()) return "<div>" . __("Mindbody plugin settings error.", 'mz-mindbody-api') . "</div>";

        $events = ($response['GetClassesResult']['ResultCount'] >= 1) ? $response['GetClassesResult']['Classes']['Class'] : __('No Events in current cycle', 'mz-mindbody-api');

        $events = $this->events_object->sort_events_by_time();

        // Assign the date range to the $result
        $date_range = $this->events_object->display_time_frame;
        $result['date_range'] = sprintf(__('Displaying events from %1$s to %2$s.', 'mz-mindbody-api'),
            $date_range['start']->format('F j'),
            $date_range['end']->format('F j'));

        // Update the data array
        $this->template_data['events'] = $events;
        $this->template_data['atts'] = $atts;

        $template_loader->set_template_data($this->template_data);
        if ($atts['list'] != 1):
            $template_loader->get_template_part('event_listing_full');
        else:
            $template_loader->get_template_part('event_listing_list');
        endif;

        $result['message'] = ob_get_clean();

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $result = json_encode($result);
            echo $result;
        } else {
            header("Location: " . $_SERVER["HTTP_REFERER"]);
        }

        die();

    }


}

?>
