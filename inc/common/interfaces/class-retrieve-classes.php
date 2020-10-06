<?php
namespace MZ_Mindbody\Inc\Common\Interfaces;

use MZ_Mindbody\Inc\Core as Core;
use MZ_Mindbody\Inc\Libraries as Libraries;
use MZ_Mindbody\Inc\Schedule as Schedule;
use MZ_Mindbody as NS;
use MZ_Mindbody\Inc\Libraries\Rarst\WordPress\DateTime as Datetime;

/**
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
 * @param @type string $class_type MBO API has 'Enrollment' and 'Class'. 'Enrolment' is a "workdhop". Default: 'Enrollment'
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
     * Used to filter by location via jQuery in display, also to
     * print location name if multiple locations shown in same schedule.
     * Key is MBO location ID and value is location name.
     *
     * @since    2.4.7
     * @access   public
     * @var      array $locations_dictionary
     */
    public $locations_dictionary;

    /**
     * Attributes sent to shortcode.
     *
     * @since    2.4.7
     * @access   public
     * @var      array    $atts    Shortcode attributes filtered via shortcode_atts().
     */
    public $atts;

    /**
     * MBO Account.
     *
     * Which MBO account to pull data from, default to Options setting, but can be overridden in shortcode
     *
     * @since    2.0.0
     * @access   protected
     * @var      int    $mbo_account    Which MBO account to pull data from.
     */
    protected $mbo_account;

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
     * Holds the native MBO "schedule type".
     *
     * $class_type MBO API has native 'Enrollment' and 'Class'. 'Enrolment' is a "workshop". Default: 'Enrollment'
     *
     * @since    2.4.7
     * @access   public
     * @var      array    $schedule_types    Array containing MBO "class types" to display.
     */
    public $schedule_types;

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

    public function __construct($atts = array(
                                'locations' => array(1)
                                )){

        parent::__construct();
        $this->date_format = Core\MZ_Mindbody_Api::$date_format;
        $this->time_format = Core\MZ_Mindbody_Api::$time_format;
        $this->classesByDateThenTime = array();
        $this->classes = array();
        $this->atts = $atts;
        if (!empty(Core\MZ_Mindbody_Api::$basic_options['mz_mindbody_siteID'])):
            $this->mbo_account = !empty($atts['account']) ? $atts['account'] : Core\MZ_Mindbody_Api::$basic_options['mz_mindbody_siteID'];
        else:
            $this->mbo_account = '-99';
        endif;
        $this->time_frame = $this->time_frame();
        $this->locations_dictionary = array();
        $this->schedule_types = !empty(Core\MZ_Mindbody_Api::$advanced_options['schedule_types']) ? Core\MZ_Mindbody_Api::$advanced_options['schedule_types'] : array('Class');
        // Allow shortcode to override global setting for schedule_types
        if (!empty($this->atts['schedule_types'])) $this->schedule_types = $this->atts['schedule_types'];
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

        if ( !$mb) return false;

        /* Set array string based on if called from Events Object
         * or Schedule Object.
         *
         * SessionTypeIDs key only exists for Events display.
         */
        $sc_string = (array_key_exists('SessionTypeIDs', $this->time_frame )) ? 'get_events' : 'get_schedule';
        
        $transient_string = $this->generate_transient_name($sc_string);
        
        if ( false === get_transient( $transient_string ) ) {
            // If there's not a transient already, call the API and create one

            if ($this->mbo_account !== 0) {
                // If account has been specified in shortcode, update credentials
                $mb->sourceCredentials['SiteIDs'][0] = $this->mbo_account;
            }
            
            try {
                $schedule_data = $mb->GetClasses($this->time_frame);
            } catch ( \Exception $e ) {
                NS\MZMBO()->helpers->print($e->getMessage());
                return false;
            }
                        
            if ( empty($schedule_data) || empty($schedule_data['Classes'][0]['Id']) ):

                echo "<!-- " . print_r($schedule_data, true) . " --> ";

                return false;

            endif;

			if ($schedule_data['PaginationResponse']['TotalResults'] === 0) return "No Classes";

            // Otherwise (if successful API call) assign result to $this->classes.
            $this->classes = $schedule_data['Classes'];

            // Store the transient for 12 hours or admin set duration
            $transient_duration = !empty(Core\MZ_Mindbody_Api::$advanced_options['schedule_transient_duration']) ? Core\MZ_Mindbody_Api::$advanced_options['schedule_transient_duration'] : 43200;
            set_transient($transient_string, $this->classes, $transient_duration);

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
		return get_weekstartend(date("Y-m-d H:i:s", $timestamp), Core\MZ_Mindbody_Api::$start_of_week);
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

        /* When there is only a single event in the client
         * schedule, the 'Classes' array contains that event, but when there are multiple
         * visits then the array of events is under 'Events'/'Event'
         *
         This may not be necessary
        if (!empty($this->classes[0]['StartDateTime'])){
            // Multiple events
            $classes_array_scope = $this->classes[0];
        } else {
            $classes_array_scope = $this->classes;
        }
        */

        foreach($this->classes as $class)
        {

            // TODO Don't do this twice. Filter once for BOTH schedule displays
            // Filter out some items
            if ($this->filter_class($class) === false) continue;

            // Populate the Locations Dictionary
            $this->populate_locations_dictionary($class);

            // Make a timestamp of just the day to use as key for that day's classes
            $dt = new \DateTime($class['StartDateTime']);
            $just_date =  $dt->format('Y-m-d');

            // If class was previous to today ignore it
            if ( $just_date < $this->current_day_offset->format('Y-m-d') ) continue;

            /* Create a new array with a key for each date YYYY-MM-DD
            and corresponding value an array of class details */

            $single_event = new Schedule\Schedule_Item($class, $this->atts);

            if(!empty($this->classesByDateThenTime[$just_date])) {
                array_push($this->classesByDateThenTime[$just_date], $single_event);
            } else {
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
        //TODO Make pad_empty_calendar_days work
        return $this->classesByDateThenTime;
    }
    
    /*
     * Get Classes By Date Then Time and return, padded for any days without events.
     * @since 2.6.4
     *
     * Currently not working because $this->start_date is start of week as per WP config.
     * Needs to be current day of week, but not necessarily THIS week.
     *
     * @param array classesByDateThenTime array of sequenced classes.
     * @return array of classes, padded to a full seven days, wether or not classes exist
     */
    private function pad_empty_calendar_days($classesByDateThenTime){

        $week_of_dates = [$this->start_date->format('Y-m-d') => ''];
        for($i = 1; $i < 7; $i++){
            $week_of_dates[$this->start_date->add(new \DateInterval('P1D'))->format('Y-m-d')] = '';
        }
        return array_merge($week_of_dates, $classesByDateThenTime);
    }

    /**
     * Return an array of MBO Class Objects, ordered by date.
     *
     * This is used in Grid view. It gets the filtered results from the MBO API call and builds a matrix, top level of which is
     * seven arrays, one for each of seven days in a week (for a calendar column), each one of the Day columns contains an array
     * of Class Event objects, sequenced by time of day, earliest to latest. There may be multiple classes occurring at same time,
     * which are contained in another sub-array.
     *
     *
     *
     * @return @type array of Objects from Single_event class, in Date (and time) sequence.
     */
    public function sort_classes_by_time_then_date() {

        $classesByTime = array();

        /* When there is only a single event in the client
         * schedule, the 'Classes' array contains that event, but when there are multiple
         * visits then the array of events is under 'Events'/'Event'
         *
         This may not be necessary
        if (!empty($this->classes[0]['StartDateTime'])){
            // Multiple events
            $classes_array_scope = $this->classes[0];
        } else {
            $classes_array_scope = $this->classes;
        }
        */

        foreach($this->classes as $class)
        {

            // Filter out some items
            if ($this->filter_class($class) === false) continue;

            // Populate the Locations Dictionary
            $this->populate_locations_dictionary($class);

            // Ignore classes that are not part of current week (ending Sunday)
            $class_datetime = new \DateTime($class['StartDateTime']);
            if ($class_datetime->format('Y-m-d') > $this->current_week_end->format('Y-m-d')):
                continue;
            endif;

            /*
             * Create a new array with a key for each TIME (time of day, not date)
             * and corresponding value an array of class details
             * for classes at that time.
             *
             */

            $classTime = date_i18n("G.i", strtotime($class['StartDateTime'])); // for numerical sorting

            $single_event = new Schedule\Schedule_Item($class, $this->atts);

            // If there's is already an array for this time slot, add to it.
            if(!empty($this->classesByTimeThenDate[$classTime])) {
                // Create a $single_event which is a "class" object, and start the classes array with it.
                array_push($this->classesByTimeThenDate[$classTime]['classes'], $single_event);

            } else {
                // Assign the first element of this time slot.
                $display_time = (date_i18n(Core\MZ_Mindbody_Api::$time_format, strtotime($class['StartDateTime'])));
                $this->classesByTimeThenDate[$classTime] = array(
                                                                'display_time' => $display_time,
                                                                // Add part_of_day for filter as well
                                                                'part_of_day' => $single_event->part_of_day,
                                                                'classes' => array($single_event)
                                                            );
            }
        }
        // Timeslot keys in new array are not time-sequenced so do so.
        ksort($this->classesByTimeThenDate);
        foreach($this->classesByTimeThenDate as $scheduleTime => &$classes)
        {
            /*
             * $classes is an array of all class_event objects for given time
             * Take each of the class arrays and order it by days 1-7
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
     * one event for each day and empty arrays will represent empty time slots.
     */
    private function week_of_timeslot($classes, $indicator){
        $seven_days = array_combine(range(1, 7), array(array(), array(), array(),
            array(), array(), array(), array()));
        foreach($seven_days as $key => $value){
            foreach ($classes as $class) {
                if ($class->$indicator == $key){
                    array_push($seven_days[$key], $class);
                }
            }
        }
        return $seven_days;
    }

    /**
     * Filter out Classes that we don't want.
     *
     * @param array $class
     * @return boolean
     */
    protected function filter_class($class){
        
        if (
            (!in_array($class['Location']['Id'], $this->atts['locations'])) ||
            (!in_array($class['ClassDescription']['Program']['ScheduleType'], $this->schedule_types))
        ) {
            return false;
        }
        if (!empty($this->atts['session_types'])) {
            if (!in_array($class['ClassDescription']['SessionType']['Name'], $this->atts['session_types'])) {       
                return false;
            }
        }
        // Support old "class_types" shortcode att:
        if (!empty($this->atts['class_types'])) {
            if (!in_array($class['ClassDescription']['SessionType']['Name'], $this->atts['class_types'])) {
                return false;
            }
        }
        // If shortcode not set to hide classes that are cancelled.
        if ( !empty($this->atts['hide_cancelled']) ) {
        	if ( $class['IsCanceled'] == 1  ) return false;
        }
        
        // Uncomment to view date in browser
        //NS\MZMBO()->helpers->print(date_i18n(Core\MZ_Mindbody_Api::$date_format, strtotime($class['StartDateTime'])));

        return true;
    }

    /**
     * Populate Locations Dictionary
     *
     * Populate the objects Locations Dictionary, which will be used to create Location links
     * as well as to populate the Filter on schedules which filter multiple locations.
     *
     * @param array $class a single "class" returned from MBO API
     */
    protected function populate_locations_dictionary($class){
        // We only need to do this once for each location.
        if (count($this->locations_dictionary) === count($this->atts['locations'])) return;
        // Build a link TODO use HTML Element Class
        $locationName = $class['Location']['Name'];
        $location_name_css = sanitize_html_class($locationName, 'mz_location_class');
        $locationAddress = $class['Location']['Address'];
        $locationAddress2 = $class['Location']['Address2'];
        $url_encoded_address = urlencode($locationAddress.$locationAddress2);
        $locationNameDisplay = '<span class="location_name '.$location_name_css.'"><a href="http://maps.google.com/maps?q='.$url_encoded_address.'" target="_blank" title="'. $locationAddress. '">' . $locationName . '</a>';

        if (!array_key_exists($class['Location']['Id'], $this->locations_dictionary)):
            $this->locations_dictionary[$class['Location']['Id']] = array(
                                                                        'name' => $locationName,
                                                                        'link' => $locationNameDisplay,
                                                                        'class' => preg_replace('/\W+/','-',strtolower(strip_tags($locationName)))
                                                                    );
        endif;
    }

    /**
     * Set up Time Frame with Start and End times for Schedule Request
     *
     * @since 2.4.7
     */
        abstract public function time_frame($timestamp);

}
