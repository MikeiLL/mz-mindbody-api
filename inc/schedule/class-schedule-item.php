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
    public $signupButton = '';
    public $locationAddress = '';
    public $locationAddress2 = '';
    public $locationNameDisplay = '';
    public $sign_up_title;
    public $sign_up_text = '';
    public $manage_text;
    public $class_details;
    public $toward_capacity = '';
    public $scheduleType;
    public $staffName;
    public $isAvailable;
    public $locationName;
    public $staffImage;
    
    // Attributes we create
    public $displayCancelled;
    public $day_num; //for use in grid schedule display
    public $teacher = '';
    public $classLength = '';
    public $time_of_day;
    public $non_specified_class_times = array();
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

        $this->className = isset($schedule_item['ClassDescription']['Name']) ? $schedule_item['ClassDescription']['Name']: '';
        $this->startDateTime = isset($schedule_item['StartDateTime']) ? $schedule_item['StartDateTime'] : '';
        $this->sessionTypeName = isset($schedule_item['ClassDescription']['SessionType']['Name']) ? $schedule_item['ClassDescription']['SessionType']['Name'] : '';
        $this->staffName = isset($schedule_item['Staff']['Name']) ? $schedule_item['Staff']['Name'] : '';
        $this->classDescription = isset($schedule_item['ClassDescription']['Description']) ? $schedule_item['ClassDescription']['Description'] : '';
        $this->staffImage = isset($schedule_item['Staff']['ImageURL']) ? $schedule_item['Staff']['ImageURL'] : '';
        $this->class_title_ID = isset($schedule_item['ID']) ? $schedule_item['ID'] : '';
        $this->class_instance_ID = isset($schedule_item['ClassScheduleID']) ? $schedule_item['ClassScheduleID'] : '';
    }

    public function get_schedule_item(){
        return $this->schedule_item['ClassDescription']['SessionType']['Name'];
    }


}

?>
