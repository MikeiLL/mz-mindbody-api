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
    public $startTimeStamp;
    public $endTimeStamp;
    public $className;
    public $startDateTime;
    public $endDateTime;
    public $sDate;
    public $sLoc;
    public $sTG;
    public $studioid;
    public $class_instance_ID;
    public $class_title_ID;
    public $sessionTypeName;
    public $classDescription;
    public $classImage = '';
    public $classImageArray;
    public $displayCancelled;
    public $staffName;
    public $isAvailable;
    public $locationName;
    public $staffImage;
    public $day_num; //for use in grid schedule display
    public $teacher = '';
    public $classLength = '';
    public $signupButton = '';
    public $locationAddress = '';
    public $locationAddress2 = '';
    public $locationNameDisplay = '';
    public $sign_up_title;
    public $sign_up_text = '';
    public $manage_text;
    public $class_details;
    public $toward_capacity = '';
    public $time_of_day;
    public $non_specified_class_times = array();
    public $scheduleType;
    public $mbo_url;
    public $event_start_and_end;
    public $level; // accessing from another plugin
    public $sub_link = '';
    public $staffModal;
    public $mbo_account; // the MBO account in case multiple accounts are set

    /*
     * @param $schedule_item array of item attributes. See class description.
     */
    public function __construct($schedule_item) {
        // $this->schedule_item = $schedule_item;
        $this->className = $schedule_item['ClassDescription']['SessionType']['Name'];
        $this->startDateTime = $schedule_item['StartDateTime'];
        $this->sessionTypeName = $schedule_item['ClassDescription']['SessionType']['Name'];
        $this->staffName = $schedule_item['Staff']['Name'];
    }

    public function get_schedule_item(){
        return $this->schedule_item['ClassDescription']['SessionType']['Name'];
    }


}

?>
