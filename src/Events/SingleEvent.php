<?php
/**
 * Single Event
 *
 * This file contains the class that holds and formats
 * a single event from MBO API GetClasses Result.
 *
 * @package MzMindbody
 */

namespace MZoo\MzMindbody\Events;

use MZoo\MzMindbody as NS;
use MZoo\MzMindbody\Core as Core;
use MZoo\MzMindbody\Libraries as Library;

/**
 * Class that holds and formats a single event from MBO API GetClasses Result.
 *
 * These are generally MBO "Enrollments" as opposed to Drop-Ins
 *
 * @since 2.4.7
 *
 * @used in events/class-display.
 *
 * @param $event array from MBO
 * @param $atts array from shortcode attribute call
 */
class SingleEvent {



    /**
     * Event Class Schedule ID
     *
     * @since 2.4.7
     *
     * @used in making link to signup
     *
     * @access public
     * @var    $class_schedule_id int TODO differentiate from ['ClassDescription']['Id']
     */
    public $class_schedule_id;

    /**
     * Event Class ID
     *
     * @since 2.4.7
     *
     * @access public
     * @var    $ID int TODO differentiate from ['ClassScheduleId']
     */
    public $ID;

    /**
     * Event Start Time
     *
     * @since 2.4.7
     *
     * @access public
     * @var    $start_datetime datetime as pulled in from MBO
     */
    public $start_datetime;

    /**
     * Event End Time
     *
     * @since 2.4.7
     *
     * @access public
     * @var    $end_datetime datetime as pulled in from MBO
     */
    public $end_datetime;

    /**
     * Event Name
     *
     * @since 2.4.7
     *
     * @access public
     * @var    $class_name string as pulled in from ['ClassDescription']['Name']
     */
    public $class_name;

    /**
     * Event Image
     *
     * @since 2.4.7
     *
     * @access public
     * @var    $class_image string url to image hosted with MBO
     */
    public $class_image;

    /**
     * Event Staff Name
     *
     * @since 2.4.7
     *
     * @access public
     * @var    $staff_name string as pulled in from ['Staff']['Name']
     */
    public $staff_name;

    /**
     * Event Staff Biography
     *
     * @since 2.4.7
     *
     * @access public
     * @var    $staff_bio string as pulled in from ['Staff']['Bio']
     */
    public $staff_bio;

    /**
     * Event Image
     *
     * @since 2.4.7
     *
     * @access public
     * @var    $staff_image string url to image hosted with MBO
     */
    public $staff_image;

    /**
     * Event Name
     *
     * @since 2.4.7
     *
     * @access public
     * @var    $event_description string as pulled in from ['ClassDescription']['Description']
     */
    public $event_description;

    /**
     * Event Location ID
     *
     * @since 2.4.7
     *
     * @access public
     * @var    $location_id string
     */
    public $location_id;

    /**
     * Event Location Name
     *
     * @since 2.4.7
     *
     * @access public
     * @var    $location_name string
     */
    public $location_name;

    /**
     * Event Location Address
     *
     * @since 2.4.7
     *
     * @access public
     * @var    $location_address string
     */
    public $location_address;

    /**
     * Event Location Address2
     *
     * @since 2.4.7
     *
     * @access public
     * @var    $location_address2 string
     */
    public $location_address2;

    /**
     * Event Location City
     *
     * @since 2.4.7
     *
     * @access public
     * @var    $location_city string
     */
    public $location_city;

    /**
     * Event Location State Provo Code
     *
     * @since 2.4.7
     *
     * @access public
     * @var    $location_state_prov_code string
     */
    public $location_state_prov_code;

    /**
     * Event Location Postal/Zip Code
     *
     * @since 2.4.7
     *
     * @access public
     * @var    $location_postal_code string
     */
    public $location_postal_code;

    /**
     * Shortcode Attributes
     *
     * @since 2.4.7
     *
     * @access protected
     * @var    $atts array The shortcode atts from shortcode out of which Event is instantiated
     */
    protected $atts;

    /**
     * Class Name Link object for display in schedules.
     *
     * @since  2.4.7
     * @access public
     * @var    string    $date_for_mbo_link    'Y/m/d' date string for MBO link.
     */
    public $date_for_mbo_link;

    /**
     * Class Name Link object for display in schedules.
     *
     * @since  2.4.7
     * @access public
     * @var    int    $site_id    MBO Account for this event.
     */
    public $site_id;

    /**
     * Link object for display in schedules.
     *
     * @since  2.4.7
     * @access public
     * @var    string    $mbo_url    URL to event on MBO site.
     */
    public $mbo_url;

    /**
     * Class Name Link object for display in schedules.
     *
     * @since  2.4.7
     * @access public
     * @var    HTML object     $class_name_link    Link to be rendered with ->build() method.
     */
    public $class_name_link;

    /**
     * Class Name Link object for display in schedules.
     *
     * @since  2.4.7
     * @access public
     * @var    HTML object     $staff_name_link    Link to be rendered with ->build() method.
     */
    public $staff_name_link;

    /**
     * Class Name Link object for display in schedules.
     *
     * @since  2.4.7
     * @access public
     * @var    HTML object    $sign_up_link    Link to be rendered with ->build() method.
     */
    public $sign_up_link;

    /**
     * Populate attributes with data from MBO
     *
     * @since 2.4.7
     *
     * @access public
     * @param  array $event array of class/event attributes.
     * @param  array $atts  array of shortcode attributes from calling shortcode.
     */
    public function __construct( $event, $atts = array() ) {

        $this->class_schedule_id = $event['ClassScheduleId'];
        $this->start_datetime    = $event['StartDateTime'];
        $this->end_datetime      = $event['EndDateTime'];
        $this->class_name        = $event['ClassDescription']['Name'];
        $this->ID                = $event['ClassDescription']['Id'];

        $this->first_name = $event['Staff']['FirstName'];
        $this->last_name  = $event['Staff']['LastName'];
        // Set Staff Name up.
        // First set first, last with default to blank string.
        $this->staff_name = isset( $this->first_name ) ? $this->first_name . ' ' . $this->last_name : '';
        // If "Name" has been set, use that.
        if ( isset( $event['Staff']['Name'] ) ) {
            $this->staff_name = $event['Staff']['Name'];
        }
        $this->staff_image              = $event['Staff']['ImageUrl'];
        $this->staff_bio                = $event['Staff']['Bio'];
        $this->event_description        = $event['ClassDescription']['Description'];
        $this->class_image              = $event['ClassDescription']['ImageURL'];
        $this->location_id              = $event['Location']['Id'];
        $this->location_name            = $event['Location']['Name'];
        $this->location_address         = $event['Location']['Address'];
        $this->location_address2        = $event['Location']['Address2'];
        $this->location_city            = $event['Location']['City'];
        $this->location_state_prov_code = $event['Location']['StateProvCode'];
        $this->location_postal_code     = $event['Location']['PostalCode'];
        $this->start_date               = gmdate( Core\MzMindbodyApi::$date_format, strtotime( $event['StartDateTime'] ) );
        $this->start_time               = gmdate( Core\MzMindbodyApi::$time_format, strtotime( $event['StartDateTime'] ) );

        // Leave end_date blank if same as start day.
        $maybe_end_date        = gmdate( Core\MzMindbodyApi::$date_format, strtotime( $event['EndDateTime'] ) );
        $this->end_date        = ( $maybe_end_date === $this->start_date ) ? '' : $maybe_end_date;
        $this->end_time        = gmdate( Core\MzMindbodyApi::$time_format, strtotime( $event['EndDateTime'] ) );
        $this->atts            = $atts;
        $this->site_id         = ! empty( $atts['account'] ) ? $atts['account'] : Core\MzMindbodyApi::$basic_options['mz_mindbody_siteID'];
        $this->mbo_url         = $this->mbo_url();
        $this->class_name_link = $this->event_link_maker( 'class' );
        $this->staff_name_link = $this->event_link_maker( 'staff' );
        $this->sign_up_link    = $this->event_link_maker( 'signup' );
    }

    /**
     * Event Link Maker
     *
     * Generate anchor tag for event, staff, signup.
     *
     * @param string $type Which type of link to generate.
     * @return string html anchor tag.
     */
    private function event_link_maker( $type = 'class' ) {
        $class_name_link = new Library\HtmlElement( 'a' );
        $class_name_link->set( 'href', NS\PLUGIN_NAME_URL . 'src/Frontend/views/modals/modal_descriptions.php' );
        $link_array = array();
        switch ( $type ) {
            case 'staff':
                $link_array['data-staffImage'] = ( '' !== $this->staff_image ) ? $this->staff_image : '';
                $link_array['data-staffBio']   = ( '' !== $this->staff_bio ) ? esc_html($this->staff_bio) : '';
                $link_array['text']            = $this->staff_name;
                $link_array['data-staffName']  = $this->staff_name;
                $link_array['data-target']     = 'mzStaffScheduleModal';
                $link_array['class']           = 'modal-toggle ' . sanitize_html_class( $this->staff_name, 'mz_staff_name' );
                break;

            case 'signup':
                $link_array['class'] = 'btn btn-primary';

                $link_array['text'] = __( 'Sign-Up', 'mz-mindbody-api' );

                $link_array['data-time'] = gmdate(
                    Core\MzMindbodyApi::$date_format . ' ' . Core\MzMindbodyApi::$time_format,
                    strtotime( $this->start_datetime )
                );

                $link_array['target'] = '_blank';

                $class_name_link->set( 'href', $this->mbo_url );

                break;

            case 'class':
                $link_array['data-className']        = $this->class_name;
                $link_array['data-staffName']        = $this->staff_name;
                $link_array['data-classDescription'] = ( '' !== $this->event_description ) ? $this->event_description : '';
                $link_array['data-eventImage']       = ( '' !== $this->class_image ) ? $this->class_image : '';
                $link_array['text']                  = $this->class_name;
                $link_array['data-target']           = 'mzDescriptionModal';
                $link_array['class']                 = 'modal-toggle ' . sanitize_html_class(
                    $this->class_name,
                    'mz_class_name'
                );
        }

        $class_name_link->set( $link_array );
        return $class_name_link;
    }

    /**
     * Generate MBO URL
     *
     * Create a URL for signing up for class.
     *
     * @return urlstring
     */
    private function mbo_url() {
        $mbo_link  = 'https://clients.mindbodyonline.com/asp/res_a.asp';
        $mbo_link .= "?classDate={$this->date_for_mbo_link}";
        $mbo_link .= "&amp;clsLoc={$this->location_id}";
        $mbo_link .= '&amp;tg=4';
        $mbo_link .= "&amp;classId={$this->class_schedule_id}";
        $mbo_link .= '&amp;courseID=';
        $mbo_link .= "&amp;studioid={$this->site_id}";
        return $mbo_link;
    }
}
