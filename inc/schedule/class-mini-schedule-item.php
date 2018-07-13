<?php
namespace MZ_Mindbody\Inc\Schedule;

use MZ_Mindbody;
use MZ_Mindbody\Inc\Core as Core;
use MZ_Mindbody\Inc\Libraries\HTML_Element;
use MZ_Mindbody\Inc\Libraries\Rarst\WordPress\DateTime as DateTime;
use MZ_Mindbody\Inc\Libraries as Libraries;

/**
 * Simplified version of the Schedule_Item class.
 *
 * These objects hold the schedule items returned in a Client Schedule
 * returned by GetClientSchedule.
 *
 * @Used By Class_Client_Portal
 *
 * @param $schedule_item array
 */
class Mini_Schedule_Item {

    // All of the attributes from MBO
    /**
     * Class Name.
     *
     * @since    2.4.7
     * @access   public
     * @var      string $className Name of the scheduled class.
     */
    public $className;

    /**
     * Timestamp when class starts.
     *
     * @since    2.4.7
     * @access   public
     * @var      string $startDateTime Format is '2018-05-21T08:30:00'.
     */
    public $startDateTime;

    /**
     * Timestamp when class ends.
     *
     * @since    2.4.7
     * @access   public
     * @var      string $endDateTime Format is '2018-05-21T08:30:00'.
     */
    public $endDateTime;


    /**
     * Class instance ID
     *
     * Might be used in generating URL for class sign-up
     *
     * @since    2.4.7
     * @access   public
     * @var      int $class_schedule_id ID of this particular instance of the class
     */
    public $class_schedule_id;

    /**
     * Class Title ID
     *
     * This is the integer associated with the specific instance of a class in MBO. This
     * is what we send to the API to register or de-register for a class.
     *
     * $schedule_item['ID']
     *
     * @since    2.4.7
     * @access   public
     * @var      int
     */
    public $ID;

    /**
     * 
     *
     * @since    2.4.7
     * @access   public
     * @var      string 
     */
    public $manage_text;


    /**
     * 
     *
     * @since    2.4.7
     * @access   public
     * @var      string 
     */
    public $scheduleType;

    /**
     * 
     *
     * @since    2.4.7
     * @access   public
     * @var      string 
     */
    public $staffName;

    /**
     * Name of Location as defined in MBO and associated with MBO location ID
     *
     * @since    2.4.7
     * @access   public
     * @var      string 
     */
    public $locationName;

    /**
     * Location ID
     *
     * @since    2.4.7
     * @access   public
     * @var      string $studioID ID of location associated with class
     */
    public $siteID;
    
    // Attributes we create


    /**
     * MBO url TAB
     *
     * Which "tab" in the MBO interface the URL in link opens to.
     *
     * @since    2.4.7
     * @access   public
     * @var      int Which MBO interface tab link leads to.
     */
    public $sType;

    /**
     * MBO Staff ID
     *
     * Each staff member is assigned a unique ID in MBO
     *
     * @since    2.4.7
     * @access   public
     * @var      int Unique ID for staff member.
     */
    public $staffID;

    /**
     * 
     *
     * @since    2.4.7
     * @access   public
     * @var      string $classLength
     */
    public $classLength = '';

    /**
     * Holder for MBO Url
     *
     * @since    2.4.7
     * @access   public
     * @var      urlstring $mbo_url the url that links to MBO interface for class
     */
    public $mbo_url;

    /**
     * 
     *
     * @since    2.4.7
     * @access   public
     * @var      string $event_start_and_end
     */
    public $event_start_and_end;

    /**
     * 
     *
     * @since    2.4.7
     * @access   public
     * @var      string $mbo_account
     */
    public $mbo_account; // the MBO account in case multiple accounts are set

    /**
     * MBO Timestamp when class starts, formatted for including in URL string for MBO class link.
     *
     * @since    2.4.7
     * @access   public
     * @var      string $sDate Format is '05/21/2018'.
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
     *
     * @since    2.4.7
     * @access   public
     * @var      HTML object    $class_name_link    Instance of HTML class.
     */
    public $class_name_link;

    /**
     * Populate attributes with data from MBO
     *
     * @since 2.4.7
     *
     * @param array $schedule_item array of item attributes. See class description.
     */
    public function __construct($schedule_item, $atts = array()) {

        $this->className = isset($schedule_item['Name']) ? $schedule_item['Name'] : '';
        $this->startDateTime = $schedule_item['StartDateTime'];
        $this->endDateTime = $schedule_item['EndDateTime'];

        // $this->sessionTypeName = isset($schedule_item['ClassDescription']['SessionType']['Name']) ? $schedule_item['ClassDescription']['SessionType']['Name'] : '';

        $this->staffName = isset($schedule_item['Staff']['Name']) ? $schedule_item['Staff']['Name'] : '';

        $this->ID = $schedule_item['ID'];
        // $this->class_schedule_id = $schedule_item['ClassScheduleID'];
        // $this->sLoc = $schedule_item['Location']['ID'];

        //$this->locationName = $schedule_item['Location']['Name'];

        // $this->sDate = date_i18n('m/d/Y', strtotime($schedule_item['StartDateTime']));
        // $this->sTG = $schedule_item['ClassDescription']['Program']['ID'];
        // $this->mbo_url = $this->mbo_url();
        // $this->sType = -7;
        // $this->staffID = $schedule_item['Staff']['ID'];
        // $this->siteID = $schedule_item['Location']['SiteID'];
        // $this->session_type_css = 'mz_' . sanitize_html_class($this->sessionTypeName, 'mz_session_type');
        // $this->class_name_css = 'mz_' . sanitize_html_class($this->className, 'mz_class_name');
        // $this->class_duration = $this->get_schedule_event_duration();
        // $this->dislayCancelled = ($schedule_item['LateCanceled'] == 1) ? '<div class="mz_cancelled_class">' . __('Cancelled', 'mz-mindbody-api') . '</div>' : '';

    }



    /**
     * Calculate class duration
     *
     * Note the part of day class occurs in. Used to filter in display table for schedules
     *
     *
     * @return string "morning", "afternoon" or "night", translated
     */
    private function get_schedule_event_duration(){
        $start = new DateTime\WpDateTime($this->startDateTime);
        $end = new DateTime\WpDateTime($this->endDateTime);
        return $start->diff($end);
    }

    /**
     * Generate MBO URL
     *
     * Create a URL for signing up for class.
     *
     *
     * @return urlstring
     */
    private function mbo_url() {
        return "https://clients.mindbodyonline.com/ws.asp?sDate={$this->sDate}&amp;sLoc={$this->sLoc}&amp;sType={$this->sType}&amp;sclassid={$this->class_schedule_id}&amp;studioid={$this->studioid}";
    }


}

?>
