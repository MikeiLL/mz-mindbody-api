<?php
namespace MZ_Mindbody\Inc\Schedule;

/*
 * Class that holds and formats a single item from MBO API Schedule
 * for display.
 *
 * Schedule_Item construct receives a single schedule item from the MBO API array containing
 * sub-arrays like ClassDescription, Location, IsCancelled
 *
 * @param $schedule_item array
 */
class Schedule_Item {

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
     * Location ID from MBO.
     *
     * Single-location accounts this will probably be a one. Used in generating URL for class sign-up.
     *
     * @since    2.4.7
     * @access   public
     * @var      int $sLoc Which MBO location this schedule item occurs at.
     */
    public $sLoc;

    /**
     * Program ID
     *
     * ID if the Program the schedule item is assoctiated with. Used in generating URL for class sign-up.
     *
     * @since    2.4.7
     * @access   public
     * @var      int $sTG ID of program class is associated with.
     */
    public $sTG;

    /**
     * Studio ID
     *
     * This is possibly a particular "room" at a location. Used in generating URL for class sign-up.
     *
     * @since    2.4.7
     * @access   public
     * @var      int $studioid ID associated with MBO account studio
     */
    public $studioid;

    /**
     * Class instance ID
     *
     * Used in generating URL for class sign-up
     *
     * @since    2.4.7
     * @access   public
     * @var      int $class_instance_ID ID of this particular instance of the class
     */
    public $class_instance_ID;

    /**
     * 
     *
     * @since    2.4.7
     * @access   public
     * @var      string 
     */
    public $class_title_ID;

    /**
     * 
     *
     * @since    2.4.7
     * @access   public
     * @var      string 
     */
    public $sessionTypeName;

    /**
     * 
     *
     * @since    2.4.7
     * @access   public
     * @var      string 
     */
    public $classDescription;

    /**
     * 
     *
     * @since    2.4.7
     * @access   public
     * @var      string 
     */
    public $classImage = '';

    /**
     * 
     *
     * @since    2.4.7
     * @access   public
     * @var      string 
     */
    public $classImageArray;

    /**
     * 
     *
     * @since    2.4.7
     * @access   public
     * @var      string 
     */
    public $signupButton = '';

    /**
     * 
     *
     * @since    2.4.7
     * @access   public
     * @var      string 
     */
    public $locationAddress = '';

    /**
     * 
     *
     * @since    2.4.7
     * @access   public
     * @var      string 
     */
    public $locationAddress2 = '';

    /**
     * 
     *
     * @since    2.4.7
     * @access   public
     * @var      string 
     */
    public $locationNameDisplay = '';

    /**
     * 
     *
     * @since    2.4.7
     * @access   public
     * @var      string 
     */
    public $sign_up_title;

    /**
     * 
     *
     * @since    2.4.7
     * @access   public
     * @var      string 
     */
    public $sign_up_text = '';

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
    public $class_details;

    /**
     * 
     *
     * @since    2.4.7
     * @access   public
     * @var      string 
     */
    public $toward_capacity = '';

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
     * 
     *
     * @since    2.4.7
     * @access   public
     * @var      string 
     */
    public $isAvailable;

    /**
     * 
     *
     * @since    2.4.7
     * @access   public
     * @var      string 
     */
    public $locationName;

    /**
     * 
     *
     * @since    2.4.7
     * @access   public
     * @var      string 
     */
    public $staffImage;
    
    // Attributes we create

    /**
     * 
     *
     * @since    2.4.7
     * @access   public
     * @var      string 
     */
    public $displayCancelled;

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
     * @var      string $day_num
     */
    public $day_num; //for use in grid schedule display

    /**
     * 
     *
     * @since    2.4.7
     * @access   public
     * @var      string $teacher
     */
    public $teacher = '';

    /**
     * 
     *
     * @since    2.4.7
     * @access   public
     * @var      string $classLength
     */
    public $classLength = '';

    /**
     * 
     *
     * @since    2.4.7
     * @access   public
     * @var      string $time_of_day
     */
    public $time_of_day;

    /**
     * 
     *
     * @since    2.4.7
     * @access   public
     * @var      string $non_specified_class_times
     */
    public $non_specified_class_times = array();

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
     * @var      string $level
     */
    public $level; // accessing from another plugin

    /**
     * 
     *
     * @since    2.4.7
     * @access   public
     * @var      string $sub_link
     */
    public $sub_link = '';

    /**
     * 
     *
     * @since    2.4.7
     * @access   public
     * @var      string $staffModal
     */
    public $staffModal;

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
     * Populate attributes with data from MBO
     *
     * @since 2.4.7
     *
     * @param $schedule_item array of item attributes. See class description.
     */
    public function __construct($schedule_item) {

        $this->className = isset($schedule_item['ClassDescription']['Name']) ? $schedule_item['ClassDescription']['Name']: '';
        $this->startDateTime = $schedule_item['StartDateTime'];
        $this->sessionTypeName = isset($schedule_item['ClassDescription']['SessionType']['Name']) ? $schedule_item['ClassDescription']['SessionType']['Name'] : '';
        $this->staffName = isset($schedule_item['Staff']['Name']) ? $schedule_item['Staff']['Name'] : '';
        $this->classDescription = isset($schedule_item['ClassDescription']['Description']) ? $schedule_item['ClassDescription']['Description'] : '';
        $this->staffImage = isset($schedule_item['Staff']['ImageURL']) ? $schedule_item['Staff']['ImageURL'] : '';
        $this->class_title_ID = $schedule_item['ID'];
        $this->class_instance_ID = $schedule_item['ClassScheduleID'];
        $this->sLoc = $schedule_item['Location']['ID'];
        $this->studioid = $schedule_item['Location']['SiteID'];
        $this->sDate = date_i18n('m/d/Y', strtotime($schedule_item['StartDateTime']));
        $this->sTG = $schedule_item['ClassDescription']['Program']['ID'];
        $this->sTG = $schedule_item['ClassScheduleID'];
        $this->sign_up_title = __('Sign-Up', 'mz-mindbody-api');
        $this->manage_text = __('Manage on MindBody Site', 'mz-mindbody-api');
        $this->mbo_url = $this->mbo_url();
        $this->sType = -7;
        $this->staffID = $schedule_item['Staff']['ID'];

    }

    public function get_schedule_item(){
        return $this->schedule_item['ClassDescription']['SessionType']['Name'];
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
        return "https://clients.mindbodyonline.com/ws.asp?sDate={$this->sDate}&amp;sLoc={$this->sLoc}&amp;sTG={$this->sTG}&amp;sType={$this->sType}&amp;sclassid={$this->class_instance_ID}&amp;studioid={$this->studioid}";
    }


}

?>
