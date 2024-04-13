<?php
/**
 * Schedule Item
 *
 * This file contains the class that holds and formats
 * a single schedule event (class) from MBO API GetClasses Result.
 *
 * @package MzMindbody
 */

namespace MZoo\MzMindbody\Schedule;

use MZoo\MzMindbody as NS;
use MZoo\MzMindbody\Core as Core;
use MZoo\MzMindbody\Libraries\HtmlElement;
use MZoo\MzMindbody\Libraries\Rarst\WordPress\DateTime as DateTime;
use MZoo\MzMindbody\Libraries as Libraries;

/**
 * Class that holds and formats a single item from MBO API Schedule
 * for display.
 *
 * ScheduleItem construct receives a single schedule item from the MBO API array containing
 * sub-arrays like ClassDescription, Location, IsCancelled
 */
class ScheduleItem {
    // All of the attributes from MBO.
    /**
     * Class Name.
     *
     * @since  2.4.7
     * @access public
     * @var    string $class_name Name of the scheduled class.
     */
    public $class_name;

    /**
     * Timestamp when class starts.
     *
     * @since  2.4.7
     * @access public
     * @var    string $start_datetime Format is '2018-05-21T08:30:00'.
     */
    public $start_datetime;

    /**
     * Timestamp when class ends.
     *
     * @since  2.4.7
     * @access public
     * @var    string $end_datetime Format is '2018-05-21T08:30:00'.
     */
    public $end_datetime;

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
     * Program ID
     *
     * ID of the Program the schedule item is associated with. Used in generating URL for class sign-up.
     *
     * ['ClassDescription']['Program']['Id']
     *
     * @since  2.4.7
     * @access public
     * @var    int $class_program_id_stg ID of program class is associated with, sTG in MBO.
     */
    public $class_program_id_stg;

    /**
     * Class instance ID
     *
     * Might be used in generating URL for class sign-up
     *
     * @since  2.4.7
     * @access public
     * @var    int $class_schedule_id ID of this particular instance of the class.
     */
    public $class_schedule_id;

    /**
     * Class Title ID
     *
     * This is the integer associated with the specific instance of a class in MBO. This
     * is what we send to the API to register or de-register for a class.
     *
     * $schedule_item['Id']
     *
     * @since  2.4.7
     * @access public
     * @var    int
     */
    public $ID;

    /**
     * Session Type Name
     *
     * @since  2.4.7
     * @access public
     * @var    string $session_type_name
     */
    public $session_type_name;

    /**
     * Class Description
     *
     * @since  2.4.7
     * @access public
     * @var    string|html $class_description May contain HTML.
     */
    public $class_description;

    /**
     * Returned image string from MBO
     *
     * @since  2.4.7
     * @access public
     * @var    string $class_image
     */
    public $class_image = '';

    /**
     * Display Class as Cancelled.
     *
     * @since  2.4.7
     * @access public
     * @var    html $display_cancelled String to display if class is cancelled.
     */
    public $display_cancelled;

    /**
     * Location Address
     *
     * @since  2.4.7
     * @access public
     * @var    string
     */
    public $location_address = '';

    /**
     * Location Address 2
     *
     * @since  2.4.7
     * @access public
     * @var    string
     */
    public $location_address2 = '';

    /**
     * Location Name Display
     *
     * @since  2.4.7
     * @access public
     * @var    string $location_name_display from MBO.
     */
    public $location_name_display = '';

    /**
     * Sign Up Title
     *
     * @since  2.4.7
     * @access public
     * @var    string $sign_up_title for use in HTML.
     */
    public $sign_up_title;

    /**
     * Sign Up Text
     *
     * @since  2.4.7
     * @access public
     * @var    string $sign_up_text for use in HTML.
     */
    public $sign_up_text = '';

    /**
     * Manage Text
     *
     * @since  2.4.7
     * @access public
     * @var    string
     */
    public $manage_text;

    /**
     * Class Details
     *
     * String we build with basic info about the class
     *
     * @since  2.4.7
     * @access public
     * @var    string
     */
    public $class_details;

    /**
     * Toward Capacity
     *
     * @since  2.4.7
     * @access public
     * @var    string
     */
    public $toward_capacity = '';

    /**
     * Schedule Type
     *
     * @since  2.4.7
     * @access public
     * @var    string $schedule_type       Will probably be Class or Enrollment
     */
    public $schedule_type;

    /**
     * Staff Name
     *
     * @since  2.4.7
     * @access public
     * @var    string $staff_name
     */
    public $staff_name;

    /**
     * Name of Location as defined in MBO and associated with MBO location ID
     *
     * @since  2.4.7
     * @access public
     * @var    string $location_name from MBO.
     */
    public $location_name;

    /**
     * Staff Image
     *
     * @since  2.4.7
     * @access public
     * @var    string $staff_image from MBO.
     */
    public $staff_image;

    /**
     * Location/Site ID
     *
     * This is possibly a particular "room" at a location. Used in generating URL for class sign-up.
     *
     * @since  2.4.7
     * @access public
     * @var    string $site_id ID of location associated with class
     */
    public $site_id;

    /**
     * Level
     *
     * Class Description -> Level
     *
     * Accessing from mz_mbo_pages plugin
     *
     * @since  2.4.7
     * @access public
     * @var    string $level
     */
    public $level;

    /*
    *
    * ATTRIBUTES WE CREATE
    *
    */

    /**
     * MBO url TAB
     *
     * Which "tab" in the MBO interface the URL in link opens to.
     *
     * @since  2.4.7
     * @access public
     * @var    string $mbo_s_type_tab Which MBO interface tab link leads to.
     */
    public $mbo_s_type_tab;

    /**
     * MBO Staff ID
     *
     * Each staff member is assigned a unique ID in MBO
     *
     * @since  2.4.7
     * @access public
     * @var    int $staff_id Unique ID for staff member.
     */
    public $staff_id;

    /**
     * Weekday Number 1-7 from php's date function
     *
     * This is used in the grid schedule display to know which weekday schedule event is associated with.
     *
     * @since  2.4.7
     * @access public
     * @var    int $day_num
     */
    public $day_num;

    /**
     * Non Specified Class Times
     *
     * @since  2.4.7
     * @access public
     * @var    array $non_specified_class_times
     */
    public $non_specified_class_times = array();

    /**
     * Holder for MBO Url
     *
     * @since  2.4.7
     * @access public
     * @var    urlstring $mbo_url the url that links to MBO interface for class
     */
    public $mbo_url;

    /**
     * Substitute Link
     *
     * @since  2.4.7
     * @access public
     * @var    string $sub_link
     */
    public $sub_link = '';
    /**
     * MBO Account
     *
     * @since  2.4.7
     * @access public
     * @var    string $mbo_account MBO account in case multiple accounts are set.
     */
    public $mbo_account;

    /**
     * MBO Timestamp when class starts, formatted for including in URL string for MBO class link.
     *
     * @since  2.4.7
     * @access public
     * @var    string $date_for_mbo_link Format is '05/21/2018'.
     */
    public $date_for_mbo_link;

    /**
     * CSS-ready name of schedule item Session Type Name
     *
     * @since 2.4.7
     *
     * @var string $session_type_css in case styling required.
     */
    public $session_type_css;

    /**
     * CSS-ready name of schedule item Class Name
     *
     * @since 2.4.7
     *
     * @var string $class_name_css in case styling required.
     */
    public $class_name_css;

    /**
     * Part of Day
     *
     * Morning, Afternoon, Evening
     *
     * @since 2.4.7
     *
     * @var string $part_of_day used in filter by time of day.
     */
    public $part_of_day;

    /**
     * Class duration
     *
     * Difference between ClassStartTime and ClassEndTime
     * Format it like this $class_duration->format('%H:%I');
     *
     * @since 2.4.7
     *
     * @var Datetime $class_duration
     */
    public $class_duration;

    /**
     * Whether or not the staff member associated with this event is a substitute.
     *
     * @since  2.4.7
     * @access public
     * @var    boolean    $is_substitute    Datetime containing start of week requested.
     */
    public $is_substitute;

    /**
     * Whether or not the staff member associated with this event is a substitute.
     *
     * @since  2.4.7
     * @access public
     * @var    object $sub_details Instance of HTML class.
     */
    public $sub_details;

    /**
     * Class Name Link object for display in schedules.
     *
     * @since  2.4.7
     * @access public
     * @var    HTML object $class_name_link Instance of HTML class.
     */
    public $class_name_link;

    /**
     * Sign-Up Link object for display in schedules.
     *
     * @since  2.4.7
     * @access public
     * @var    HTML object    $sign_up    Instance of HTML class.
     */
    public $sign_up_link;

    /**
     * Shortcode attributes.
     *
     * TODO: Would like to avoid having to pass these in here.
     *
     * @since  2.4.7
     * @access public
     *
     * @used in create Link Array Functions: class_name_link_maker.
     *
     * @var array $atts Shortcode attributes function called with.
     */
    public $atts;

    /**
     * Populate attributes with data from MBO
     *
     * @since 2.4.7
     *
     * @param array $schedule_item item attributes. See class description.
     * @param array $atts from wp post shortcode.
     */
    public function __construct( $schedule_item, $atts = array() ) {

        $this->class_name        = isset( $schedule_item['ClassDescription']['Name'] ) ? $schedule_item['ClassDescription']['Name'] : '';
        $this->class_image       = isset( $schedule_item['ClassDescription']['ImageURL'] ) ? $schedule_item['ClassDescription']['ImageURL'] : '';
        $this->start_datetime    = $schedule_item['StartDateTime'];
        $this->end_datetime      = $schedule_item['EndDateTime'];
        $this->session_type_name = isset( $schedule_item['ClassDescription']['SessionType']['Name'] ) ? $schedule_item['ClassDescription']['SessionType']['Name'] : '';
        // Set Staff Name up.
        // First set first, last with default to blank string.
        $this->staff_name = isset( $schedule_item['Staff']['FirstName'] ) ? $schedule_item['Staff']['FirstName'] . ' ' . $schedule_item['Staff']['LastName'] : '';
        // If "Name" has been set, use that.
        if ( isset( $schedule_item['Staff']['Name'] ) ) {
            $this->staff_name = $schedule_item['Staff']['Name'];
        }
        $this->class_description     = isset( $schedule_item['ClassDescription']['Description'] ) ? $schedule_item['ClassDescription']['Description'] : '';
        $this->level                 = isset( $schedule_item['ClassDescription']['Level']['Name'] ) ? $schedule_item['ClassDescription']['Level']['Name'] : '';
        $this->staff_image           = isset( $schedule_item['Staff']['ImageUrl'] ) ? $schedule_item['Staff']['ImageUrl'] : '';
        $this->is_waitlist_available = isset( $schedule_item['IsWaitlistAvailable'] ) ? $schedule_item['IsWaitlistAvailable'] : '';
        $this->total_booked          = isset( $schedule_item['TotalBooked'] ) ? $schedule_item['TotalBooked'] : '';
        $this->max_capacity          = isset( $schedule_item['MaxCapacity'] ) ? $schedule_item['MaxCapacity'] : '';
        $this->ID                    = $schedule_item['Id'];
        $this->class_program_id_stg  = $schedule_item['ClassDescription']['Program']['Id'];
        $this->class_schedule_id     = $schedule_item['ClassScheduleId'];
        $this->studio_location_id    = $schedule_item['Location']['Id'];
        $this->location_name         = $schedule_item['Location']['Name'];
        $this->date_for_mbo_link     = gmdate( 'm/d/Y', strtotime( $schedule_item['StartDateTime'] ) );
        $this->sign_up_title         = __( 'Sign-Up', 'mz-mindbody-api' );
        $this->manage_text           = __( 'Manage on MindBody Site', 'mz-mindbody-api' );
        $this->mbo_s_type_tab        = -7;
        $this->staff_id              = $schedule_item['Staff']['Id'];
        $this->site_id               = ! empty( $atts['account'] ) ? $atts['account'] : Core\MzMindbodyApi::$basic_options['mz_mindbody_siteID'];
        $this->mbo_url               = $this->mbo_url();
        $this->day_num               = $this->get_day_number( wp_date( 'N', strtotime( $schedule_item['StartDateTime'] ) ) );
        $this->session_type_css      = 'mz_' . sanitize_html_class( $this->session_type_name, 'mz_session_type' );
        $this->class_name_css        = 'mz_' . sanitize_html_class( $this->class_name, 'mz_class_name' );
        $this->part_of_day           = $this->part_of_day();
        $this->class_duration        = $this->get_schedule_event_duration();
        $this->display_cancelled     = ( 1 === (int) $schedule_item['IsCanceled'] ) ? '<div class="mz_cancelled_class">' . __( 'Cancelled', 'mz-mindbody-api' ) . '</div>' : '';
        $this->is_substitute         = (int) $schedule_item['Substitute'];
        $this->schedule_type         = $schedule_item['ClassDescription']['Program']['ScheduleType'];
        $this->atts                  = $atts;
        if ( ( 'on' === Core\MzMindbodyApi::$advanced_options['elect_display_substitutes'] ) && empty( $atts['mbo_pages_call'] ) ) :
            // We add the mbo_pages_call attribute if calling from MBO Pages plugin so that sub details will be skipped.
            if ( true === $this->is_substitute ) :
                $owners = new RetrieveClassOwners();
                $owner  = $owners->find_class_owner( $schedule_item );
                if ( false !== $owner ) {
                    $this->sub_details = $owner['class_owner'];
                }
            endif;
        endif;
        $this->class_name_link   = $this->class_link_maker( 'class' );
        $this->staff_name_link   = $this->class_link_maker( 'staff' );
        $this->sign_up_link      = $this->class_link_maker( 'signup' );
        $this->grid_sign_up_link = $this->class_link_maker( 'signup', 'grid' );
        $this->class_details     = '<div class="mz_schedule_table mz_description_holder mz_location_' . $this->studio_location_id . ' ' . 'mz_' . $this->class_name . '">';
        $this->class_details    .= '<span class="mz_class_name">' . $this->class_name . '</span>';
        //$this->class_details    .= ' <span class="mz_class_with">' . NS\MZMBO()->i18n->get( 'with' ) . '</span>';
        $this->class_details    .= ' <span class="mz_class_staff">' . $this->staff_name . '</span>';
        $this->class_details    .= ' <div class="mz_class_description">' . $this->class_description . '</div>';
        $this->class_details    .= '</div>';
    }

    /**
     * Build the Class Name link object
     *
     * @param string $type Staff, Class or Signup link.
     * @param string $sub_type grid if grid schedule.
     * @return HtmlElement anchor tag.
     */
    private function class_link_maker( $type = 'class', $sub_type = false ) {
        /*
         * Need following eventually
         */

        $link_array = array();
        $link       = new Libraries\HtmlElement( 'a' );

        switch ( $type ) {
            case 'staff':
                $link_array['data-staffName'] = $this->staff_name;
                $link_array['data-staffID']   = $this->staff_id;
                $link_array['class']          = 'modal-toggle ' . sanitize_html_class( $this->staff_name, 'mz_staff_name' );
                $link_array['text']           = $this->staff_name;
                $link_array['data-target']    = 'mzStaffScheduleModal';
                // Used in Staff\Display.
                $link_array['data-nonce']  = wp_create_nonce( 'mz_staff_retrieve_nonce' );
                $link_array['data-siteID'] = $this->site_id;
                if ( ( true === $this->is_substitute ) && ( ! empty( $this->sub_details ) ) ) {
                    $link_array['data-sub'] = ( ! empty( $this->sub_details ) ) ? $this->sub_details : '';
                }
                $link_array['data-staffImage'] = ( '' !== $this->staff_image ) ? $this->staff_image : '';
                $link->set( 'href', NS\PLUGIN_NAME_URL . 'src/Frontend/views/modals/modal_descriptions.php' );
                break;

            case 'class':
                $link_array['data-className']        = $this->class_name;
                $link_array['data-staffName']        = $this->staff_name;
                $link_array['data-classDescription'] = rawUrlEncode( $this->class_description );
                $link_array['class']                 = 'modal-toggle mz_get_registrants ' . sanitize_html_class( $this->class_name, 'mz_class_name' );
                $link_array['text']                  = $this->class_name;
                $link_array['data-target']           = 'mzModal';

                if ( isset( $this->atts['show_registrants'] ) && ( 1 === (int) $this->atts['show_registrants'] ) ) {
                    // Used in Schedule\RetrieveRegistrants.
                    $link_array['data-nonce']   = wp_create_nonce( 'mz_mbo_get_registrants' );
                    $link_array['data-target']  = 'registrantModal';
                    $link_array['data-classID'] = $this->ID;
                }
                $link_array['data-staffImage'] = ( '' !== $this->staff_image ) ? $this->staff_image : '';
                $link->set( 'href', NS\PLUGIN_NAME_URL . 'src/Frontend/views/modals/modal_descriptions.php' );
                //$link->set( 'id', 'signUpForClass' );
                break;

            case 'signup':
                $link_array['class'] = 'btn btn-primary';
                $link_array['title'] = apply_filters( 'mz_mbo_registrations_available', __( 'Registrations Available', 'mz-mindbody-api' ) );
                $link_array['data-classID'] = $this->ID;
                $link_array['data-className'] = $this->class_name;
                $link_array['data-staffName'] = $this->staff_name;

                if ( ! empty( $this->max_capacity ) && $this->total_booked >= $this->max_capacity ) :
                    if ( false === $this->is_waitlist_available ) :
                        $link_array['class'] = 'btn btn-primary disabled';
                    endif;
                    if ( true === $this->is_waitlist_available ) :
                        $link_array['class'] = 'btn btn-primary waitlist-only';
                        $link_array['title'] = apply_filters( 'mz_mbo_waitlist_only', __( 'Waitlist Only', 'mz-mindbody-api' ) );
                    endif;
                endif;

                // If grid, we want icon and not text copy for signup.
                if ( 'grid' === $sub_type ) :
                    $link_array['text'] = '<svg class="icon sign-up"><use xlink:href="#si-bootstrap-log-in"/></use></svg>';
                else :
                    $link_array['text'] = __( 'Sign-Up', 'mz-mindbody-api' );
                    if ( $this->total_booked >= $this->max_capacity && true === $this->is_waitlist_available ) :
                        $link_array['text'] = apply_filters( 'mz_mbo_waitlist_only', __( 'Waitlist', 'mz-mindbody-api' ) );
                    endif;
                endif;

                $link_array['data-time'] = gmdate( Core\MzMindbodyApi::$date_format . ' ' . Core\MzMindbodyApi::$time_format, strtotime( $this->start_datetime ) );

                $link_array['target'] = '_blank';
                $link->set( 'href', $this->mbo_url );
                $link_array['data-target'] = 'mzSignUpModal';

                break;
        }

        $link->set( $link_array );

        return $link;
    }

    /**
     * Get Day Number
     *
     * PHP numbers days of the week starting on Monday at 1. If our week starts on Sunday,
     * then we need to shift so that Sunday is 1 and Saturday is 7.
     *
     * @param int $php_day_number a number from 1 - 7 for which day of week php assigns based on date().
     *
     * @return int 1 - 7 for assigning to specific shedule item to display in grid schedule
     */
    private function get_day_number( $php_day_number ) {
        /*
         * If week starts on Monday we're same as php,
         * and for now we're ignoring week starts aside from
         * Sunday or Monday. Sorry.
         */
        if ( 0 !== (int) Core\MzMindbodyApi::$start_of_week ) {
            return $php_day_number;
        }
        switch ( $php_day_number ) {
            case 7:
                return 1;
            break;
            default:
                return $php_day_number + 1;
        }
    }

    /**
     * Assign "Part of Day" based on datetime calculations.
     *
     * Note the part of day class occurs in. Used to filter in display table for schedules
     *
     * @return string "morning", "afternoon" or "night", translated
     */
    private function part_of_day() {
        $time_by_integer = gmdate( 'G.i', strtotime( $this->start_datetime ) );
        if ( $time_by_integer < 12 ) {
            return __( 'morning', 'mz-mindbody-api' );
        } elseif ( $time_by_integer > 16 ) {
            return __( 'evening', 'mz-mindbody-api' );
        } else {
            return __( 'afternoon', 'mz-mindbody-api' );
        }
        return '';
    }

    /**
     * Get event duration
     *
     * Calculate diff between start and end of event
     *
     * @return DateInterval between start and end of event
     */
    private function get_schedule_event_duration() {
        $start = new \DateTime( $this->start_datetime );
        $end   = new \DateTime( $this->end_datetime );
        return $start->diff( $end );
    }

    /**
     * Generate MBO URL
     *
     * Create a URL for signing up for class.
     *
     * @return urlstring
     */
    private function mbo_url() {
        $mbo_link  = 'https://clients.mindbodyonline.com/ASP/res_a.asp';
        $mbo_link .= "?classDate={$this->date_for_mbo_link}";
        $mbo_link .= "&amp;clsLoc={$this->studio_location_id}"; // may be schedule_ID in places.
        $mbo_link .= '&amp;tg=27';
        $mbo_link .= "&amp;classId={$this->class_schedule_id}";
        $mbo_link .= "&amp;studioid={$this->site_id}";
        return $mbo_link;
    }
}
