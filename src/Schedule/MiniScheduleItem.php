<?php

namespace MZoo\MzMindbody\Schedule;

use MZoo\MzMindbody;
use MZoo\MzMindbody\Core as Core;
use MZoo\MzMindbody\Libraries\HtmlElement;
use MZoo\MzMindbody\Libraries as Libraries;

/**
 * Simplified version of the ScheduleItem class.
 *
 * These objects hold the schedule items returned in a Client Schedule
 * returned by GetClientSchedule.
 *
 * @Used By Class_ClientPortal
 *
 * TODO This should be a subclass of ScheduleItem
 *
 * @param $ScheduleItem array
 */
class MiniScheduleItem {



	// All of the attributes from MBO
	/**
	 * Class Name.
	 *
	 * @since  2.4.7
	 * @access public
	 * @var    string $className Name of the scheduled class.
	 */
	public $className;

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
	 * $ScheduleItem['Id']
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
	public $manage_text;


	/**
	 *
	 *
	 * @since  2.4.7
	 * @access public
	 * @var    string
	 */
	public $scheduleType;

	/**
	 *
	 *
	 * @since  2.4.7
	 * @access public
	 * @var    string
	 */
	public $staffName;

	/**
	 * Name of Location as defined in MBO and associated with MBO location ID
	 *
	 * @since  2.4.7
	 * @access public
	 * @var    string
	 */
	public $locationName;

	/**
	 * Location ID
	 *
	 * @since  2.4.7
	 * @access public
	 * @var    string $studioID ID of location associated with class
	 */
	public $site_id;

	// Attributes we create


	/**
	 * MBO url TAB
	 *
	 * Which "tab" in the MBO interface the URL in link opens to.
	 *
	 * @since  2.4.7
	 * @access public
	 * @var    int Which MBO interface tab link leads to.
	 */
	public $sType;

	/**
	 * MBO Staff ID
	 *
	 * Each staff member is assigned a unique ID in MBO
	 *
	 * @since  2.4.7
	 * @access public
	 * @var    int Unique ID for staff member.
	 */
	public $staffID;

	/**
	 *
	 *
	 * @since  2.4.7
	 * @access public
	 * @var    string $classLength
	 */
	public $classLength = '';

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
	 * @var    string $mbo_account
	 */
	public $mbo_account; // the MBO account in case multiple accounts are set

	/**
	 * MBO Timestamp when class starts, formatted for including in URL string for MBO class link.
	 *
	 * @since  2.4.7
	 * @access public
	 * @var    string $sDate Format is '05/21/2018'.
	 */
	public $sDate;

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
	 * Class Name Link object for display in schedules.
	 *
	 * @since  2.4.7
	 * @access public
	 * @var    HTML object    $class_name_link    Instance of HTML class.
	 */
	public $class_name_link;

	/**
	 * Populate attributes with data from MBO
	 *
	 * @since 2.4.7
	 *
	 * @param array $ScheduleItem array of item attributes. See class description.
	 */
	public function __construct( $ScheduleItem, $atts = array() ) {
		$this->className      = isset( $ScheduleItem['Name'] ) ? $ScheduleItem['Name'] : '';
		$this->start_datetime = $ScheduleItem['StartDateTime'];
		$this->end_datetime   = $ScheduleItem['EndDateTime'];
		$this->staffName      = isset( $ScheduleItem['Staff']['Name'] ) ? $ScheduleItem['Staff']['Name'] : '';
		$this->ID             = $ScheduleItem['Id'];
		$this->site_id        = ! empty( $atts['account'] ) ? $atts['account'] : Core\MzMindbodyApi::$basic_options['mz_mindbody_siteID'];
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
		$mbo_link .= "?sDate={$this->sDate}";
		$mbo_link .= "&amp;sLoc={$this->location_ID}";
		$mbo_link .= '&amp;sType=7';
		$mbo_link .= "&amp;sclassid={$this->class_schedule_id}";
		$mbo_link .= "&amp;studioid={$this->site_id}";
		return $mbo_link;
	}
}
