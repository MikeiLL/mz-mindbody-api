<?php
namespace MZ_Mindbody\Inc\Common\Interfaces;

use MZ_Mindbody\Inc\Core as Core;
use MZ_Mindbody\Inc\Libraries as Libraries;
use MZ_Mindbody\Inc\Schedule as Schedule;
use MZ_Mindbody as NS;

/*
 * Class that is extended for Schedule Display Shortcode(s)
 *
 * @param @type string $time_format Format string for php strtotime function Default: "g:i a"
 * @param @type array OR numeric $locations Single or list of MBO location numerals Default: 1
 * @param @type boolean $hide_cancelled Whether or not to display cancelled classes. Default: 0
 * @param @type array $hide Items to be removed from calendar
 * @param @type boolean $advanced Whether or not allowing online class sign-up via plugin
 * @param @type boolean $show_registrants Whether or not to display class registrants in modal popup
 * @param @type boolean $registrants_count  Whether we want to show count of registrants in a class (TODO - finish) @default: 0
 * @param @type string $calendar_format Depending on final display, we may create items in Single_event class differently.
 *																			Default: 'horizontal'
 * @param @type boolean $delink Make class name NOT a link
 * @param @type string $class_type MBO API has 'Enrollment' and 'DropIn'. 'Enrolment' is a "workdhop". Default: 'Enrollment'
 * @param @type numeric $account Which MBO account is being interfaced with.
 * @param @type boolean $this_week If true, show only week from today.
 */
abstract class Retrieve_Classes extends Retrieve {

    /**
     * Date Format for php date display
     *
     * @since    2.4.7
     * @access   public
     * @var
     */

    public $date_format;

    /**
     * Time format for php time display
     *
     * @since    2.4.7
     * @access   public
     * @var
     */
    public $time_format;

    /**
     *
     *
     * @since    2.4.7
     * @access   public
     * @var
     */
    public $this_week;

    /**
     * Schedule array sorted by first date then time.
     *
     * Used in horizontal schedule display
     *
     * @since    2.4.7
     * @access   public
     * @var      arrray $classesByDateThenTime
     */
    public $classesByDateThenTime;

    /**
     * Schedule array sorted by time, then date
     *
     * Used in grid schedule display
     *
     * @since    2.4.7
     * @access   public
     * @var      array $classesByTimeThenDate
     */
    public $classesByTimeThenDate;

    /**
     *
     *
     * @since    2.4.7
     * @access   public
     * @var
     */
    public $classes;

    /**
     * All locations included in current schedule
     *
     * Used to filter by location via jQuery in display
     *
     * @since    2.4.7
     * @access   public
     * @var      TODO comfirm type $locations_dictionary
     */
    public $locations_dictionary;

    /**
     * Locations array
     *
     * Defaults to the number one which is the default MBO location
     *
     * @since    2.4.7
     * @access   public
     * @var      array $locations
     */
    public $locations;

    /**
     * Attributes sent to shortcode.
     *
     * @since    2.4.7
     * @access   public
     * @var      array    $atts    Shortcode attributes filtered via shortcode_atts().
     */
    public $atts;

    /**
     * Holds the time frame for the instance.
     *
     * @since    2.4.7
     * @access   public
     * @var      array    $time_frame    StartDateTime and endDateTime for MBO API call.
     */
    public $time_frame;

    /**
     * Holds the current day, with offset, based on "offset" attribute/parameter.
     *
     * set by time_frame() and used by sort_classes_by_date_then_time()
     *
     * @since    2.4.7
     * @access   public
     * @var      string    $current_day_offset    Formatted Datetime object.
     */
    public $current_day_offset;

    /**
     * Holds the MBO "class type".
     *
     * $class_type MBO API has 'Enrollment' and 'DropIn'. 'Enrolment' is a "workdhop". Default: 'Enrollment'
     *
     * @since    2.4.7
     * @access   public
     * @var      string    $class_type    String containing MBO "class type".
     */
    public $class_type;

    /**
     * Holds the first date of current results.
     *
     * This is used to display current week in grid schedule.
     *
     * @assigned in time_frame() method
     *
     * @since    2.4.7
     * @access   public
     * @var      Datetime object    $start_date    Datetime containing start of week requested.
     */
    public $start_date;

    /**
     * Holds the last date of current week.
     *
     * This is used to set end of current week in grid array sorting in sort_classes_by_time_then_date() method.
     *
     * @assigned in time_frame() method
     *
     * @since    2.4.7
     * @access   public
     * @var      Datetime object    $current_week_end    Datetime containing start of week requested.
     */
    public $current_week_end;

    public function __construct($atts = array('key' => 'val')){

        parent::__construct();
        
        $this->date_format = Core\Init::$date_format;
        $this->time_format = Core\Init::$time_format;
        $this->classesByDateThenTime = array();
        $this->classes = array();
        $this->atts = $atts;
        $this->time_frame = $this->time_frame();
        $this->locations = array(1);
        $this->locations_dictionary = array();
        $this->class_type = 'Enrollment';
        
    }


    /**
     * Get a timestamp, return data from MBO api, store it in a transient and
     * as object attribute.
     *
     * @since 2.4.7
     *
     * @param @timestamp defaults to current time
     *
     *
     * @return array of MBO schedule data
     */
    public function get_mbo_results($timestamp = null){

        $timestamp = isset($timestamp) ? $timestamp : current_time( 'timestamp' );

        $mb = $this->instantiate_mbo_API();

        if ( !$mb || $mb == 'NO_SOAP_SERVICE' ) return false;

        /* Set array string based on if called from Events Object
         * or Schedule Object
         */
        $sc_string = (array_key_exists('SessionTypeIDs', $this->time_frame )) ? 'get_events' : 'get_schedule';

        $transient_string = $this->generate_transient_name($sc_string);

        if ( false === get_transient( $transient_string ) ) {
            // If there's not a transient already, call the API and create one

            if ($this->mbo_account !== 0) {
                // If account has been specified in shortcode, update credentials
                $mb->sourceCredentials['SiteIDs'][0] = $this->mbo_account;
            }

            $this->classes = $mb->GetClasses($this->time_frame);

            set_transient($transient_string, $this->classes, 60 * 60 * 12);

        } else {
            $this->classes = get_transient( $transient_string );
        }

        return $this->classes;
    }

	/**
	 * Get timestamp, return week start and end timestamps based
	 * on wordpress start of week config.
	 *
	 * @since 2.4.7
	 *
	 * @param @timestamp which date to return week start and end of
	 *
	 * @return array 'start', 'end' of current week in timestamps
	 */
	public function single_week($timestamp){
		return get_weekstartend(date("Y-m-d H:i:s", $timestamp), Core\Init::$start_of_week);
	}

	/**
	 * Return timestamp of seven days from now.
	 *
	 * @since 2.4.7
	 *
	 * @return timestamp of seven days from now
	 */
	public function seven_days_later($timestamp){
		return strtotime("+6 day", $timestamp);
	}

	/**
	 * Displayable current week start and end timestamps.
	 *
	 * @since 2.4.7
	 * @return html string of start and end of current week
	 */
	public function current_week_display(){
		$time_frame = $this->single_week(current_time( 'timestamp' ));
		$return = 'Week start: ' . date('l, M d, Y', $time_frame[start]) . '<br/>';
		$return .= 'Week end: ' . date('l, M d, Y', $time_frame[end]);
		return $return;
	}

    /**
     * Return an array of MBO Class Objects, ordered by date, then time.
     *
     * This is used in Horizontal view. It receives the filtered results from the MBO API call ($mz_classes)
     * and builds an array of Class Event Objects, sequenced by date and time.
     *
     *
     * @param @type array $mz_classes
     *
     * @return @type array of Objects from Single_event class, in Date (and time) sequence.
     */
    public function sort_classes_by_date_then_time() {

        foreach($this->classes['GetClassesResult']['Classes']['Class'] as $class)
        {
            // If configured to do so in shortcode, skip classes that are cancelled.
            if ( ( !empty($this->atts['hide_cancelled']) ) && ( $class['IsCanceled'] == 1 ) ) continue;

            // Make a timestamp of just the day to use as key for that day's classes
            $dt = new \DateTime($class['StartDateTime']);
            $just_date =  $dt->format('Y-m-d');
            
            // Populate the Locations Dictionary
            if (!in_array($class['Location']['ID'], $this->locations)) { continue; }
            
            if (!array_key_exists($class['Location']['ID'], $this->locations_dictionary)):
                $this->locations_dictionary[$class['Location']['ID']] = $class['Location']['Name'];
            endif;

            // If class was previous to today ignore it
            if ( $just_date < $this->current_day_offset->format('Y-m-d') ) continue;

            /* Create a new array with a key for each date YYYY-MM-DD
            and corresponding value an array of class details */

            $single_event = new Schedule\Schedule_Item($class);

            if(!empty($this->classesByDateThenTime[$just_date])) {
                 if (
                     // Filter out events that who's location isn't in location list.
                     // Currently this list doesn't exist here.
                     (!in_array($class['Location']['ID'], $this->locations)) ||
                     ($class['ClassDescription']['Program']['ScheduleType'] == $this->class_type)
                 ) {
                     continue;
                 }
                //$mz_classesByDateThenTime[$classDate] = array_merge($mz_classesByDateThenTime[$classDate], array($class));
                array_push($this->classesByDateThenTime[$just_date], $single_event);
            } else {
                if (
                    (!in_array($class['Location']['ID'], $this->locations)) ||
                    ($class['ClassDescription']['Program']['ScheduleType'] == $this->class_type)
                ) {
                    continue;
                }
                //$mz_classesByDateThenTime[$classDate]['classes'] = $single_event;
                $this->classesByDateThenTime[$just_date] = array($single_event);
            }
        }
        /* They are not ordered by date so order them by date */
        ksort($this->classesByDateThenTime);
        foreach($this->classesByDateThenTime as $classDate => &$classes)
        {
            /*
             * $classes is an array of all classes for given date
             * Take each of the class arrays and order it by time
             * $classesByDateThenTime should have a length of seven, one for
             * each day of the week.
             */
            usort($classes, function($a, $b) {
                if($a->startDateTime == $b->startDateTime) {
                    return 0;
                }
                return $a->startDateTime < $b->startDateTime ? -1 : 1;
            });
        }
        return $this->classesByDateThenTime;
    }

    /**
     * Return an array of MBO Class Objects, ordered by date.
     *
     * This is used in Grid view. It gets the filtered results from the MBO API call and builds a matrix, top level of which is
     * seven arrays, one for each of seven days in a week (for a calendar column), each one of the Day columns contains an array
     * of Class Event objects, sequenced by time of day, earliest to latest.
     *
     *
     *
     * @return @type array of Objects from Single_event class, in Date (and time) sequence.
     */
    public function sort_classes_by_time_then_date() {

        $classesByTime = array();

        foreach($this->classes['GetClassesResult']['Classes']['Class'] as $class)
        {
            // Ignore classes that are not part of current week (ending Sunday)
            if (new \DateTime($class['StartDateTime']) >= $this->current_week_end):
                continue;
            endif;

            // If configured to do so in shortcode, skip classes that are cancelled.
            if ( ( !empty($this->atts['hide_cancelled']) ) && ( $class['IsCanceled'] == 1 ) ) continue;

            /* Create a new array with a key for each time
            and corresponding value an array of class details
            for classes at that time. */
            $classTime = date_i18n("G.i", strtotime($class['StartDateTime'])); // for numerical sorting

            $single_event = new Schedule\Schedule_Item($class);

            if(!empty($this->classesByTimeThenDate[$classTime])) {
                if (
                    (!in_array($class['Location']['ID'], $this->locations)) ||
                    ($class['ClassDescription']['Program']['ScheduleType'] == $this->class_type)
                ) {
                    continue;
                }
                array_push($this->classesByTimeThenDate[$classTime]['classes'], $single_event);
            } else {
                if (
                    (!in_array($class['Location']['ID'], $this->locations)) ||
                    ($class['ClassDescription']['Program']['ScheduleType'] == $this->class_type)
                ) {
                    continue;
                }
                // Assign the first element ( of this time slot ?).
                $display_time = (date_i18n(Core\Init::$advanced_options['time_format'], strtotime($class['StartDateTime'])));
                $this->classesByTimeThenDate[$classTime] = array(
                                                                'display_time' => $display_time,
                                                                // Add part_of_day for filter as well
                                                                'part_of_day' => $single_event->part_of_day,
                                                                'classes' => array($single_event)
                                                            );
            }
        }
        /* Timeslot keys in new array are not time-sequenced so do so*/
        ksort($this->classesByTimeThenDate);
        foreach($this->classesByTimeThenDate as $scheduleTime => &$classes)
        {
            /*
            $mz_classes is an array of all class_event objects for given time
            Take each of the class arrays and order it by days 1-7
            */
            usort($classes['classes'], function($a, $b) {
                if(date_i18n("N", strtotime($a->startDateTime)) == date_i18n("N", strtotime($b->startDateTime))) {
                    return 0;
                }
                return $a->startDateTime < $b->startDateTime ? -1 : 1;
            });
            $classes['classes'] = $this->week_of_timeslot($classes['classes'], 'day_num');
        }
        return $this->classesByTimeThenDate;
    }


    /**
     * Make a clean array with seven corresponding slots and populate
     * based on indicator (day) for each class. There may be more than
     * one even for each day and empty arrays will represent empty time slots.
     */
    private function week_of_timeslot($array, $indicator){
        $seven_days = array_combine(range(1, 7), array(array(), array(), array(),
            array(), array(), array(), array()));
        foreach($seven_days as $key => $value){
            foreach ($array as $class) {
                if ($class->$indicator == $key){
                    array_push($seven_days[$key], $class);
                }
            }
        }
        return $seven_days;
    }
    /*
     * Set up Time Frame with Start and End times for Schedule Request
     *
     * @since 2.4.7
     */
        abstract public function time_frame($timestamp);

}
