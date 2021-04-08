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
	// All of the attributes from MBO
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
	 * @var    int $sTG ID of program class is associated with.
	 */
	public $sTG;

	/**
	 * Class instance ID
	 *
	 * Might be used in generating URL for class sign-up
	 *
	 * @since  2.4.7
	 * @access public
	 * @var    int $class_schedule_id ID of this particular instance of the class
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
	 *
	 *
	 * @since  2.4.7
	 * @access public
	 * @var    string
	 */
	public $sessionTypeName;

	/**
	 * Class Description
	 *
	 * @since  2.4.7
	 * @access public
	 * @var    string May contain HTML.
	 */
	public $classDescription;

	/**
	 * Returned image string from MBO
	 *
	 * @since  2.4.7
	 * @access public
	 * @var    string
	 */
	public $classImage = '';

	/**
	 *
	 *
	 * @since  2.4.7
	 * @access public
	 * @var    string
	 */
	public $classImageArray;

	/**
	 * Display Class as Cancelled.
	 *
	 * @since  2.4.7
	 * @access public
	 * @var    html $displayCancelled String to display if class is cancelled.
	 */
	public $displayCancelled;

	/**
	 *
	 *
	 * @since  2.4.7
	 * @access public
	 * @var    string
	 */
	public $signupButton = '';

	/**
	 *
	 *
	 * @since  2.4.7
	 * @access public
	 * @var    string
	 */
	public $locationAddress = '';

	/**
	 *
	 *
	 * @since  2.4.7
	 * @access public
	 * @var    string
	 */
	public $locationAddress2 = '';

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
	 *
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
	 *
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
	 *
	 *
	 * @since  2.4.7
	 * @access public
	 * @var    string
	 */
	public $isAvailable;

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
	public $staffImage;

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
	 *
	 *
	 * @since  2.4.7
	 * @access public
	 * @var    string $non_specified_class_times
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
	 *
	 *
	 * @since  2.4.7
	 * @access public
	 * @var    string $event_start_and_end
	 */
	public $event_start_and_end;

	/**
	 *
	 *
	 * @since  2.4.7
	 * @access public
	 * @var    string $sub_link
	 */
	public $sub_link = '';

	/**
	 *
	 *
	 * @since  2.4.7
	 * @access public
	 * @var    string $staffModal
	 */
	public $staffModal;

	/**
	 *
	 *
	 * @since  2.4.7
	 * @access public
	 * @var    string $mbo_account
	 */
	public $mbo_account; // the MBO account in case multiple accounts are set

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
	 * @param string $session_type_css.
	 */
	public $session_type_css;

	/**
	 * CSS-ready name of schedule item Class Name
	 *
	 * @since 2.4.7
	 *
	 * @param string $class_name_css
	 */
	public $class_name_css;

	/**
	 * Part of Day
	 *
	 * Morning, Afternoon, Evening
	 *
	 * @since 2.4.7
	 *
	 * @param string $part_of_day
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
	 * @param Datetime $class_duration
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
	 * @var    object    $sub_details    Instance of HTML class.
	 */
	public $sub_details;

	/**
	 * Class Name Link object for display in schedules.
	 *
	 * @since  2.4.7
	 * @access public
	 * @var    HTML object    $class_name_link    Instance of HTML class.
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
	 * @used in create Link Array Functions: class_name_link_maker
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

		$this->class_name      = isset( $schedule_item['ClassDescription']['Name'] ) ? $schedule_item['ClassDescription']['Name'] : '';
		$this->classImage      = isset( $schedule_item['ClassDescription']['ImageURL'] ) ? $schedule_item['ClassDescription']['ImageURL'] : '';
		$this->start_datetime  = $schedule_item['StartDateTime'];
		$this->end_datetime    = $schedule_item['EndDateTime'];
		$this->sessionTypeName = isset( $schedule_item['ClassDescription']['SessionType']['Name'] ) ? $schedule_item['ClassDescription']['SessionType']['Name'] : '';
		// Set Staff Name up.
		// First set first, last with default to blank string
		$this->staff_name = isset( $schedule_item['Staff']['FirstName'] ) ? $schedule_item['Staff']['FirstName'] . ' ' . $schedule_item['Staff']['LastName'] : '';
		// If "Name" has been set, use that
		if ( isset( $schedule_item['Staff']['Name'] ) ) {
			$this->staff_name = $schedule_item['Staff']['Name'];
		}
		$this->classDescription      = isset( $schedule_item['ClassDescription']['Description'] ) ? $schedule_item['ClassDescription']['Description'] : '';
		$this->level                 = isset( $schedule_item['ClassDescription']['Level']['Name'] ) ? $schedule_item['ClassDescription']['Level']['Name'] : '';
		$this->staffImage            = isset( $schedule_item['Staff']['ImageUrl'] ) ? $schedule_item['Staff']['ImageUrl'] : '';
		$this->is_waitlist_available = isset( $schedule_item['IsWaitlistAvailable'] ) ? $schedule_item['IsWaitlistAvailable'] : '';
		$this->total_booked          = isset( $schedule_item['TotalBooked'] ) ? $schedule_item['TotalBooked'] : '';
		$this->max_capacity          = isset( $schedule_item['MaxCapacity'] ) ? $schedule_item['MaxCapacity'] : '';
		$this->ID                    = $schedule_item['Id'];
		$this->sTG                   = $schedule_item['ClassDescription']['Program']['Id'];
		$this->class_schedule_id     = $schedule_item['ClassScheduleId'];
		$this->studio_location_id    = $schedule_item['Location']['Id'];
		$this->location_name         = $schedule_item['Location']['Name'];
		$this->date_for_mbo_link     = date( 'm/d/Y', strtotime( $schedule_item['StartDateTime'] ) );
		$this->sign_up_title         = __( 'Sign-Up', 'mz-mindbody-api' );
		$this->manage_text           = __( 'Manage on MindBody Site', 'mz-mindbody-api' );
		$this->mbo_s_type_tab        = -7;
		$this->staff_id              = $schedule_item['Staff']['Id'];
		$this->site_id               = ! empty( $atts['account'] ) ? $atts['account'] : Core\MzMindbodyApi::$basic_options['mz_mindbody_siteID'];
		$this->mbo_url               = $this->mbo_url();
		$this->day_num               = $this->get_day_number( wp_date( 'N', strtotime( $schedule_item['StartDateTime'] ) ) );
		$this->session_type_css      = 'mz_' . sanitize_html_class( $this->sessionTypeName, 'mz_session_type' );
		$this->class_name_css        = 'mz_' . sanitize_html_class( $this->class_name, 'mz_class_name' );
		$this->part_of_day           = $this->part_of_day();
		$this->class_duration        = $this->get_schedule_event_duration();
		$this->dislayCancelled       = ( $schedule_item['IsCanceled'] == 1 ) ? '<div class="mz_cancelled_class">' . __( 'Cancelled', 'mz-mindbody-api' ) . '</div>' : '';
		$this->is_substitute         = $schedule_item['Substitute'];
		$this->schedule_type         = $schedule_item['ClassDescription']['Program']['ScheduleType'];
		$this->atts                  = $atts;
		if ( ( Core\MzMindbodyApi::$advanced_options['elect_display_substitutes'] == 'on' ) && empty( $atts['mbo_pages_call'] ) ) :
			// We add the mbo_pages_call attribute if calling from MBO Pages plugin so that sub details will be skipped
			if ( $this->is_substitute === true ) :
				$owners = new RetrieveClassOwners();
				$owner  = $owners->find_class_owner( $schedule_item );
				if ( $owner !== false ) {
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
		$this->class_details    .= ' <span class="mz_class_with">' . NS\MZMBO()->i18n->get( 'with' ) . '</span>';
		$this->class_details    .= ' <span class="mz_class_staff">' . $this->staff_name . '</span>';
		$this->class_details    .= ' <div class="mz_class_description">' . $this->classDescription . '</div>';
		$this->class_details    .= '</div>';
	}

	/**
	 * Build the Class Name link object
	 *
	 * @return HtmlElement anchor tag.
	 */
	private function class_link_maker( $type = 'class', $sub_type = false ) {
		/**
		 * Need following eventually
		 */

		$linkArray = array();
		$link      = new Libraries\HtmlElement( 'a' );

		switch ( $type ) {
			case 'staff':
				$linkArray['data-staffName'] = $this->staff_name;
				$linkArray['data-staffID']   = $this->staff_id;
				$linkArray['class']          = 'modal-toggle ' . sanitize_html_class( $this->staff_name, 'mz_staff_name' );
				$linkArray['text']           = $this->staff_name;
				$linkArray['data-target']    = 'mzStaffScheduleModal';
				// Used in Staff\Display.
				$linkArray['data-nonce']  = wp_create_nonce( 'mz_staff_retrieve_nonce' );
				$linkArray['data-siteID'] = $this->site_id;
				if ( ( $this->is_substitute === true ) && ( ! empty( $this->sub_details ) ) ) {
					 $linkArray['data-sub'] = ( ! empty( $this->sub_details ) ) ? $this->sub_details : '';
				}
				$linkArray['data-staffImage'] = ( $this->staffImage != '' ) ? $this->staffImage : '';
				$link->set( 'href', NS\PLUGIN_NAME_URL . 'src/Frontend/views/modals/modal_descriptions.php' );
				break;

			case 'class':
				$linkArray['data-className']        = $this->class_name;
				$linkArray['data-staffName']        = $this->staff_name;
				$linkArray['data-classDescription'] = rawUrlEncode( $this->classDescription );
				$linkArray['class']                 = 'modal-toggle mz_get_registrants ' . sanitize_html_class( $this->class_name, 'mz_class_name' );
				$linkArray['text']                  = $this->class_name;
				$linkArray['data-target']           = 'mzModal';

				if ( isset( $this->atts['show_registrants'] ) && ( $this->atts['show_registrants'] == 1 ) ) {
					// Used in Schedule\RetrieveRegistrants.
					$linkArray['data-nonce']   = wp_create_nonce( 'mz_mbo_get_registrants' );
					$linkArray['data-classID'] = $this->ID;
					$linkArray['data-target']  = 'registrantModal';
				}
				$linkArray['data-staffImage'] = ( $this->staffImage != '' ) ? $this->staffImage : '';
				$link->set( 'href', NS\PLUGIN_NAME_URL . 'src/Frontend/views/modals/modal_descriptions.php' );
				break;

			case 'signup':
				$linkArray['class'] = 'btn btn-primary';

				$linkArray['title'] = apply_filters( 'mz-mbo-registrations-available', __( 'Registrations Available', 'mz-mindbody-api' ) );

				if ( ! empty( $this->max_capacity ) && $this->total_booked >= $this->max_capacity ) :
					if ( false == $this->is_waitlist_available ) :
						$linkArray['class'] = 'btn btn-primary disabled';
					endif;
					if ( true == $this->is_waitlist_available ) :
						$linkArray['class'] = 'btn btn-primary waitlist-only';
						$linkArray['title'] = apply_filters( 'mz-mbo-waitlist-only', __( 'Waitlist Only', 'mz-mindbody-api' ) );
					endif;
				endif;

				// If grid, we want icon and not text copy for signup.
				if ( $sub_type === 'grid' ) :
					$linkArray['text'] = '<svg class="icon sign-up"><use xlink:href="#si-bootstrap-log-in"/></use></svg>';
					else :
						$linkArray['text'] = __( 'Sign-Up', 'mz-mindbody-api' );
						if ( $this->total_booked >= $this->max_capacity && true == $this->is_waitlist_available ) :
							$linkArray['text'] = apply_filters( 'mz-mbo-waitlist-button-text', __( 'Waitlist', 'mz-mindbody-api' ) );
						endif;
					endif;

					$linkArray['data-time'] = date( Core\MzMindbodyApi::$date_format . ' ' . Core\MzMindbodyApi::$time_format, strtotime( $this->start_datetime ) );

					$linkArray['target'] = '_blank';
					$link->set( 'href', $this->mbo_url );

					// endif;

				break;
		}

		$link->set( $linkArray );

		return $link;
	}

	/**
	 * Get Day Number
	 *
	 * PHP numbers days of the week starting on Monday at 1. If our week starts on Sunday,
	 * then we need to shift so that Sunday is 1 and Saturday is 7.
	 *
	 * @param $php_day_number int a number from 1 - 7 for which day of week php assigns based on date().
	 *
	 * @return int 1 - 7 for assigning to specific shedule item to display in grid schedule
	 */
	private function get_day_number( $php_day_number ) {
		/*
		 * If week starts on Monday we're same as php,
		 * and for now we're ignoring week starts aside from
		 * Sunday or Monday. Sorry.
		 */
		if ( Core\MzMindbodyApi::$start_of_week != 0 ) {
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
		$time_by_integer = date( 'G.i', strtotime( $this->start_datetime ) );
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
		$mbo_link  = 'https://clients.mindbodyonline.com/ws.asp';
		$mbo_link .= "?sDate={$this->date_for_mbo_link}";
		$mbo_link .= "&amp;sLoc={$this->studio_location_id}"; // may be schedule_ID in places.
		$mbo_link .= '&amp;sType=7';
		$mbo_link .= "&amp;sclassid={$this->class_schedule_id}";
		$mbo_link .= "&amp;studioid={$this->site_id}";
		return $mbo_link;
	}
}
