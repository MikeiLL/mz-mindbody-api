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

    private $schedule_item; // holder for item itself

    // All of the attributes from MBO
    protected $startTimeStamp;
    protected $endTimeStamp;
    protected $className;
    public $startDateTime;
    protected $endDateTime;
    protected $sDate;
    protected $sLoc;
    protected $sTG;
    protected $studioid;
    protected $class_instance_ID;
    protected $class_title_ID;
    protected $sessionTypeName;
    protected $classDescription;
    protected $classImage = '';
    protected $classImageArray;
    protected $displayCancelled;
    protected $staffName;
    protected $isAvailable;
    protected $locationName;
    protected $staffImage;
    protected $day_num; //for use in grid schedule display
    protected $teacher = '';
    protected $classLength = '';
    protected $signupButton = '';
    protected $locationAddress = '';
    protected $locationAddress2 = '';
    protected $locationNameDisplay = '';
    protected $sign_up_title;
    protected $sign_up_text = '';
    protected $manage_text;
    protected $class_details;
    protected $toward_capacity = '';
    protected $time_of_day;
    protected $non_specified_class_times = array();
    protected $scheduleType;
    protected $mbo_url;
    protected $event_start_and_end;
    protected $level; // accessing from another plugin
    protected $sub_link = '';
    protected $staffModal;
    protected $mbo_account; // the MBO account in case multiple accounts are set

    /*
     * @param $schedule_item array of item attributes. See class description.
     */
    public function __construct($schedule_item) {
        // $this->schedule_item = $schedule_item;
        $this->className = $schedule_item['ClassDescription']['SessionType']['Name'];
        $this->startDateTime = $schedule_item['StartDateTime'];
    }

    public function get_schedule_item(){
        return $this->schedule_item['ClassDescription']['SessionType']['Name'];
    }


}

?>
