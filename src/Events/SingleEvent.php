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
	 * @var    $classImage string url to image hosted with MBO
	 */
	public $classImage;

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
	 * @var    $staffBio string as pulled in from ['Staff']['Bio']
	 */
	public $staffBio;

	/**
	 * Event Image
	 *
	 * @since 2.4.7
	 *
	 * @access public
	 * @var    $staffImage string url to image hosted with MBO
	 */
	public $staffImage;

	/**
	 * Event Name
	 *
	 * @since 2.4.7
	 *
	 * @access public
	 * @var    $Description string as pulled in from ['ClassDescription']['Description']
	 */
	public $Description;

	/**
	 * Event Location ID
	 *
	 * @since 2.4.7
	 *
	 * @access public
	 * @var    $location_ID string
	 */
	public $location_ID;

	/**
	 * Event Location Name
	 *
	 * @since 2.4.7
	 *
	 * @access public
	 * @var    $location_Name string
	 */
	public $location_Name;

	/**
	 * Event Location Address
	 *
	 * @since 2.4.7
	 *
	 * @access public
	 * @var    $location_Address string
	 */
	public $location_Address;

	/**
	 * Event Location Address2
	 *
	 * @since 2.4.7
	 *
	 * @access public
	 * @var    $location_Address2 string
	 */
	public $location_Address2;

	/**
	 * Event Location City
	 *
	 * @since 2.4.7
	 *
	 * @access public
	 * @var    $location_City string
	 */
	public $location_City;

	/**
	 * Event Location State Provo Code
	 *
	 * @since 2.4.7
	 *
	 * @access public
	 * @var    $location_StateProvCode string
	 */
	public $location_StateProvCode;

	/**
	 * Event Location Postal/Zip Code
	 *
	 * @since 2.4.7
	 *
	 * @access public
	 * @var    $location_PostalCode string
	 */
	public $location_PostalCode;

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
		// First set first, last with default to blank string
		$this->staff_name = isset( $this->first_name ) ? $this->first_name . ' ' . $this->last_name : '';
		// If "Name" has been set, use that
		if ( isset( $event['Staff']['Name'] ) ) {
			$this->staff_name = $event['Staff']['Name'];
		}
		$this->staffImage             = $event['Staff']['ImageURL'];
		$this->staffBio               = $event['Staff']['Bio'];
		$this->Description            = $event['ClassDescription']['Description'];
		$this->classImage             = $event['ClassDescription']['ImageURL'];
		$this->location_ID            = $event['Location']['Id'];
		$this->location_Name          = $event['Location']['Name'];
		$this->location_Address       = $event['Location']['Address'];
		$this->location_Address2      = $event['Location']['Address2'];
		$this->location_City          = $event['Location']['City'];
		$this->location_StateProvCode = $event['Location']['StateProvCode'];
		$this->location_PostalCode    = $event['Location']['PostalCode'];
		$this->start_date             = date( Core\MzMindbodyApi::$date_format, strtotime( $event['StartDateTime'] ) );
		$this->start_time             = date( Core\MzMindbodyApi::$time_format, strtotime( $event['StartDateTime'] ) );

		// Leave end_date blank if same as start day
		$maybe_end_date        = date( Core\MzMindbodyApi::$date_format, strtotime( $event['EndDateTime'] ) );
		$this->end_date        = ( $this->start_date == $maybe_end_date ) ? '' : $maybe_end_date;
		$this->end_time        = date( Core\MzMindbodyApi::$time_format, strtotime( $event['EndDateTime'] ) );
		$this->atts            = $atts;
		$this->site_id         = ! empty( $atts['account'] ) ? $atts['account'] : Core\MzMindbodyApi::$basic_options['mz_mindbody_siteID'];
		$this->mbo_url         = $this->mbo_url();
		$this->class_name_link = $this->eventLinkMaker( 'class' );
		$this->staff_name_link = $this->eventLinkMaker( 'staff' );
		$this->sign_up_link    = $this->eventLinkMaker( 'signup' );
	}

	private function eventLinkMaker( $type = 'class' ) {
		$class_name_link = new Library\HtmlElement( 'a' );
		$class_name_link->set( 'href', NS\PLUGIN_NAME_URL . 'src/Frontend/views/modals/modal_descriptions.php' );
		$link_array = array();
		switch ( $type ) {
			case 'staff':
				$link_array['data-staffImage'] = ( $this->staffImage != '' ) ? $this->staffImage : '';
				$link_array['data-staffBio']   = ( $this->staffBio != '' ) ? $this->staffBio : '';
				$link_array['text']            = $this->staff_name;
				$link_array['data-staffName']  = $this->staff_name;
				$link_array['data-target']     = 'mzStaffScheduleModal';
				$link_array['class']           = 'modal-toggle ' . sanitize_html_class( $this->staff_name, 'mz_staff_name' );
				break;

			case 'signup':
				$link_array['class'] = 'btn btn-primary';

				$link_array['text'] = __( 'Sign-Up', 'mz-mindbody-api' );

				$link_array['data-time'] = date( Core\MzMindbodyApi::$date_format . ' ' . Core\MzMindbodyApi::$time_format, strtotime( $this->start_datetime ) );

				$link_array['target'] = '_blank';

				$class_name_link->set( 'href', $this->mbo_url );

				break;

			case 'class':
				$link_array['data-className']        = $this->class_name;
				$link_array['data-staffName']        = $this->staff_name;
				$link_array['data-classDescription'] = ( $this->Description != '' ) ? $this->Description : '';
				$link_array['data-eventImage']       = ( $this->classImage != '' ) ? $this->classImage : '';
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
		$mbo_link  = 'https://clients.mindbodyonline.com/ws.asp';
		$mbo_link .= "?sDate={$this->date_for_mbo_link}";
		$mbo_link .= "&amp;sLoc={$this->location_ID}";
		$mbo_link .= '&amp;sType=7';
		$mbo_link .= "&amp;sclassid={$this->class_schedule_id}";
		$mbo_link .= "&amp;studioid={$this->site_id}";
		return $mbo_link;
	}
}
