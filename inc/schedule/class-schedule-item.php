<?php
namespace MZ_Mindbody\Inc\Schedule;

use MZ_Mindbody;
use MZ_Mindbody\Inc\Core as Core;
use MZ_Mindbody\Inc\Libraries\HTML_Element;
use MZ_Mindbody\Inc\Libraries\Rarst\WordPress\DateTime as DateTime;
use MZ_Mindbody\Inc\Libraries as Libraries;

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
     * Returned image string from MBO
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
     * Display Class as Cancelled.
     *
     * @since    2.4.7
     * @access   public
     * @var      html $displayCancelled String to display if class is cancelled.
     */
    public $displayCancelled;

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
     * Name of Location as defined in MBO and associated with MBO location ID
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
     * Weekday Number 1-7 from php's date function
     *
     * This is used in the grid schedule display to know which weekday schedule event is associated with.
     *
     * @since    2.4.7
     * @access   public
     * @var      int $day_num
     */
    public $day_num;

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
     *
     * @since    2.4.7
     * @access   public
     * @var      boolean    $current_week_end    Datetime containing start of week requested.
     */
    public $is_substitute;

    /**
     * Whether or not the staff member associated with this event is a substitute.
     *
     *
     * @since    2.4.7
     * @access   public
     * @var      object    $sub_details    Instance of HTML class.
     */
    public $sub_details;

    /**
     * Populate attributes with data from MBO
     *
     * @since 2.4.7
     *
     * @param array $schedule_item array of item attributes. See class description.
     */
    public function __construct($schedule_item) {

        $this->className = isset($schedule_item['ClassDescription']['Name']) ? $schedule_item['ClassDescription']['Name']: '';
        $this->classImage = isset($schedule_item['ClassDescription']['ImageURL']) ? $schedule_item['ClassDescription']['ImageURL']: '';
        $this->startDateTime = $schedule_item['StartDateTime'];
        $this->endDateTime = $schedule_item['EndDateTime'];
        $this->sessionTypeName = isset($schedule_item['ClassDescription']['SessionType']['Name']) ? $schedule_item['ClassDescription']['SessionType']['Name'] : '';
        $this->staffName = isset($schedule_item['Staff']['Name']) ? $schedule_item['Staff']['Name'] : '';
        $this->classDescription = isset($schedule_item['ClassDescription']['Description']) ? $schedule_item['ClassDescription']['Description'] : '';
        $this->staffImage = isset($schedule_item['Staff']['ImageURL']) ? $schedule_item['Staff']['ImageURL'] : '';
        $this->class_title_ID = $schedule_item['ID'];
        $this->class_instance_ID = $schedule_item['ClassScheduleID'];
        $this->sLoc = $schedule_item['Location']['ID'];
        $this->locationName = $schedule_item['Location']['Name'];
        $this->studioid = $schedule_item['Location']['SiteID'];
        $this->sDate = date_i18n('m/d/Y', strtotime($schedule_item['StartDateTime']));
        $this->sTG = $schedule_item['ClassDescription']['Program']['ID'];
        $this->sTG = $schedule_item['ClassScheduleID'];
        $this->sign_up_title = __('Sign-Up', 'mz-mindbody-api');
        $this->manage_text = __('Manage on MindBody Site', 'mz-mindbody-api');
        $this->mbo_url = $this->mbo_url();
        $this->sType = -7;
        $this->staffID = $schedule_item['Staff']['ID'];
        $this->siteID = $schedule_item['Location']['SiteID'];
        $this->day_num = $this->get_day_number(date_i18n("N", strtotime($schedule_item['StartDateTime'])));
        $this->session_type_css = 'mz_' . sanitize_html_class($this->sessionTypeName, 'mz_session_type');
        $this->class_name_css = 'mz_' . sanitize_html_class($this->className, 'mz_class_name');
        $this->part_of_day = $this->part_of_day();
        $this->class_duration = $this->get_schedule_event_duration();
        $this->dislayCancelled = ($schedule_item['IsCanceled'] == 1) ? '<div class="mz_cancelled_class">' . __('Cancelled', 'mz-mindbody-api') . '</div>' : '';
        $this->is_substitute = $schedule_item['Substitute'];
        if (Core\Init::$advanced_options['elect_display_substitutes'] == 'on'):
            if ($this->is_substitute === true):
                $owners = new Retrieve_Class_Owners;
                $owner = $owners->find_class_owner($schedule_item);
                if ($owner !== false){
                    $this->sub_details = $owner['class_owner'];
                }
            endif;
        endif;
        // $this->class_name_link = $this->class_name_link_maker();
    }

    /**
     * Build the Class Name link object
     *
     * @return HTML_Element anchor tag.
     */
    //public function class_name_link_maker(){
//
    //    $linkArray = array(
    //        'data-staffName' => $this->staffName,
    //        'data-className' => $this->className,
    //        'data-classDescription' => rawUrlEncode($this->classDescription),
    //        'class' => 'modal-toggle mz_get_registrants ' . sanitize_html_class($this->className, 'mz_class_name'),
    //        'text' => $this->className,
    //        'data-target' => $this->data_target
    //    );
//
    //    if (Retrieve_Classes::$atts['show_registrants'] == 1) {
    //        $get_registrants_nonce = wp_create_nonce('mz_MBO_get_registrants_nonce');
    //        $linkArray['data-nonce'] = $get_registrants_nonce;
    //        $linkArray['data-classID'] = $this->class_instance_ID;
    //    }
    //    if ($class->staffImage != ''):
    //        $linkArray['data-staffImage'] = $this->staffImage;
    //    endif;
    //    $class_name_link = new Libraries\HTML_Element('a');
    //    $class_name_link->set('href', MZ_Mindbody\PLUGIN_NAME_URL . 'inc/frontend/views/modals/modal_descriptions.php');
    //    return $class_name_link->set($linkArray);
//
    //}

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
    private function get_day_number($php_day_number){
        /*
         * If week starts on Monday we're same as php,
         * and for now we're ignoring week starts aside from
         * Sunday or Monday. Sorry.
         */
        if (Core\Init::$start_of_week != 0) return $php_day_number;
        switch ($php_day_number) {
            case 7: return 1;
            break;
            default: return $php_day_number + 1;
        }
    }

    /**
     * Generate MBO URL
     *
     * Note the part of day class occurs in. Used to filter in display table for schedules
     *
     *
     * @return string "morning", "afternoon" or "night", translated
     */
    private function part_of_day(){
        $time_by_integer = date_i18n("G.i", strtotime($this->startDateTime));
        if ($time_by_integer < 12) {
            return __('morning', 'mz-mindbody-api');
        }else if ($time_by_integer > 16) {
            return __('evening', 'mz-mindbody-api');
        }else{
            return __('afternoon', 'mz-mindbody-api');
        }
        return '';
    }

    /**
     * Generate MBO URL
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
        return "https://clients.mindbodyonline.com/ws.asp?sDate={$this->sDate}&amp;sLoc={$this->sLoc}&amp;sTG={$this->sTG}&amp;sType={$this->sType}&amp;sclassid={$this->class_instance_ID}&amp;studioid={$this->studioid}";
    }


}

?>
