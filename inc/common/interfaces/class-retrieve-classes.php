<?php
namespace MZ_Mindbody\Inc\Common\Interfaces;

use MZ_Mindbody\Inc\Core as Core;
use MZ_Mindbody\Inc\Libraries as Libraries;
use MZ_Mindbody\Inc\Schedule as Schedule;

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

    public $time_format;
    public $date_format;
    public $locations;
    public $hide_cancelled;
    public $hide;
    public $advanced;
    public $show_registrants;
    public $registrants_count;
    public $calendar_format;
    public $delink;
    public $class_type;
    public $account;
    public $this_week;
    public $classesByDate;
    public $classes;

    public function __construct(){

        parent::__construct();
        $advanced_settings = get_option('mz_mbo_advanced');
        $this->date_format = isset($advanced_settings['date_format']) ? $advanced_settings['date_format'] : get_option('date_format');
        $this->time_format = isset($advanced_settings['time_format']) ? $advanced_settings['time_format'] : get_option('time_format');
        $this->locations = array(1);
        $this->hide_cancelled = 0;
        $this->hide = array();
        $this->advanced = 0;
        $this->show_registrants = 0;
        $this->registrants_count = 0;
        $this->calendar_format = 'horizontal';
        $this->class_type = 'Enrollment';
        $this->account = 0;
        $this->this_week = 0;
        $this->classesByDate = array();
        $this->classes = array();
        
    }


    /*
     * Get a timestamp, return data from MBO api
     *
     * @since 2.4.7
     *
     * @param @timestamp defaults to current time
     *
     * @return array of MBO schedule data
     */
    public function get_mbo_results($timestamp = null){

        $timestamp = isset($timestamp) ? $timestamp : current_time( 'timestamp' );
        $mb = $this->instantiate_mbo_API();

        if ($mb == 'NO_SOAP_SERVICE') {
            $this->classes = $mb;
            return false;
        }

        $transient_string = $this->generate_transient_name(array($this->mbo_account));

        if ( false === get_transient( $transient_string ) ) {
            // If there's not a transient already, call the API and create one

            if ($this->mbo_account !== 0) {
                // If account has been specified in shortcode, update credentials
                $mb->sourceCredentials['SiteIDs'][0] = $this->mbo_account;
            }
            set_transient($transient_string, $mb, 60 * 60 * 12);

        } else {
            $mb = get_transient( $transient_string );
        }
        $this->classes = $mb->GetClasses($this->time_frame());
        return $this->classes;
    }

	/*
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

	/*
	 * Return timestamp of seven days from now.
	 *
	 * @since 2.4.7
	 *
	 * @return timestamp of seven days from now
	 */
	public function seven_days_later($timestamp){
		return strtotime("+6 day", $timestamp);
	}

	/*
	 * Displayable current week start and end timestamps.
	 *
	 * @since 2.4.7
	 * @return html string of start and end of current week
	 */
	public function current_week_display(){
		$time_frame = $this->single_week();
		$return = 'Week start: ' . date('M d, Y', $time_frame[start]) . '<br/>';
		$return .= 'Week end: ' . date('M d, Y', $time_frame[end]);
		return $return;
	}

    /*
     * Return an array of MBO Class Objects, ordered by date.
     *
     * This is used in Horizontal view. It receives the filtered results from the MBO API call ($mz_classes)
     * and builds an array of Class Event Objects, sequenced by date and time.
     *
     *
     * @param @type array $mz_classes
     *
     * @return @type array of Objects from Single_event class, in Date (and time) sequence.
     */
    public function sort_classes_by_date_and_time() {

        // This is the array that will hold the classes we want to display
        // $this->classesByDate;

        foreach($this->classes['GetClassesResult']['Classes']['Class'] as $class)
        {//
            // Skip classes that are cancelled
            if ($this->hide_cancelled == 1):
                if ($class['IsCanceled'] == 1):
                    continue;
                endif;
            endif;

            // Make a timestamp of just the day to use as key for that day's classes
            $dt = new \DateTime($class['StartDateTime']);
            $just_date =  $dt->format('Y-m-d');


            // If class was previous to today ignore it
            if ($just_date < date('Y-m-d', current_time( 'timestamp'))) continue;

            /* Create a new array with a key for each date YYYY-MM-DD
            and corresponding value an array of class details */

            $single_event = new Schedule\Schedule_Item($class);

            if(!empty($this->classesByDate[$just_date])) {
                // if (
                //     // Filter out events that who's location isn't in location list.
                //     // Currently this list doesn't exist here.
                //     (!in_array($class['Location']['ID'], $locations)) ||
                //     ($class['ClassDescription']['Program']['ScheduleType'] == $class_type)
                // ) {
                //     continue;
                // }
                //$mz_classesByDate[$classDate] = array_merge($mz_classesByDate[$classDate], array($class));
                array_push($this->classesByDate[$just_date], $single_event);
            } else {
                // if (
                //     (!in_array($class['Location']['ID'], $locations)) ||
                //     ($class['ClassDescription']['Program']['ScheduleType'] == $class_type)
                // ) {
                //     continue;
                // }
                //$mz_classesByDate[$classDate]['classes'] = $single_event;
                $this->classesByDate[$just_date] = array($single_event);
            }
        }
        /* They are not ordered by date so order them by date */
        ksort($this->classesByDate);
        foreach($this->classesByDate as $classDate => &$classes)
        {
            /*
             * $classes is an array of all classes for given date
             * Take each of the class arrays and order it by time
             * $classesByDate should have a length of seven, one for
             * each day of the week.
             */
            usort($classes, function($a, $b) {
                if($a->startDateTime == $b->startDateTime) {
                    return 0;
                }
                return $a->startDateTime < $b->startDateTime ? -1 : 1;
            });
        }
        return $this->classesByDate;
    }

    /*
     * Set up Time Frame with Start and End times for Schedule Request
     *
     * @since 2.4.7
     */
        abstract public function time_frame($timestamp);

}
