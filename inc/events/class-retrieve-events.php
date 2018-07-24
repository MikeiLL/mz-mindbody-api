<?php
namespace MZ_Mindbody\Inc\Events;

use MZ_Mindbody\Inc\Core as Core;
use MZ_Mindbody\Inc\Libraries as Libraries;
use MZ_Mindbody\Inc\Schedule as Schedule;
use MZ_Mindbody\Inc\Common\Interfaces as Interfaces;

/**
 * Class that is extended for Events Display Shortcode(s)
 */
class Retrieve_Events extends Interfaces\Retrieve_Classes {

    /**
     * Holder for events array returned by MBO API
     *
     * @since    2.4.7
     * @access   public
     * @var      array $classes Array of events returned from MBO API
     */
    public $classes;

    /**
     * Holds the display time frame for the instance.
     *
     * @since    2.4.7
     * @access   public
     * @var      array    $display_time_frame    StartDateTime and endDateTime Timestamps used in MBO API call, displayed in navigation.
     */
    public $display_time_frame;

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
     * Return Time Frame for request to MBO API
     *
     * @since 2.4.7
     *
     * @throws \Exception
     *
     * Default time_frame is two dates, start of current week as set in WP, and seven days from "now.
     *
     * @return array or start and end dates as required for MBO API
     */
    public function time_frame($timestamp = null){

        $timestamp = isset($timestamp) ? $timestamp : current_time( 'timestamp' );

        $start_time =  new \Datetime( date_i18n('Y-m-d', $timestamp) );

        $end_time = new \Datetime( date_i18n('Y-m-d', $timestamp) );

        $session_types = explode(',', Core\MZ_Mindbody_Api::$events_options['mz_mindbody_eventID']);

        $di = new \DateInterval('P'.Core\MZ_Mindbody_Api::$event_calendar_duration.'D');

        $end_time->add($di);

        $current_day_offset = new \Datetime( date_i18n('Y-m-d') );

        // If we are going in future or past based on offset
        if ( !empty($this->atts['offset']) ) {
            // Insure that we have an absolute number, because attr may be negative
            $abs = abs($this->atts['offset']);
            $di = new \DateInterval('P'.$abs.'W');
            // If it's a negative number, invert the interval
            if ($this->atts['offset'] < 0) $di->invert = 1;
            $start_time->add($di);
            $end_time->add($di);
            $current_day_offset->add($di);
        }

        $this->display_time_frame = array('start' => $start_time, 'end' => $end_time);

        // Set current_day_offset for filtering by sort_classes_by_date_then_time().
        $this->current_day_offset = $current_day_offset;

        $simple_timeframe = array('StartDateTime'=> $start_time->format('Y-m-d'), 'EndDateTime'=> $end_time->format('Y-m-d'));

        $full_call = array_merge($simple_timeframe, array('SessionTypeIDs'=>$session_types));

        return $full_call;
    }


    /**
     * Sort Events array by MBO time
     *
     * @since 2.4.7
     *
     * @param @timestamp defaults to current time
     *
     *
     * @return array of MBO schedule data, time
     */
    public function sort_events_by_time(){
        /* When there is only a single event in the client
         * schedule, the 'Classes' array contains that event, but when there are multiple
         * visits then the array of events is under 'Events'/'Event'
         */
        if (!empty($this->classes['GetClassesResult']['Classes']['Class'][0]['StartDateTime'])){
            // Multiple events
            $events_array_scope = $this->classes['GetClassesResult']['Classes']['Class'];
        } else {
            $events_array_scope =$this->classes['GetClassesResult']['Classes'];
        }

        foreach($events_array_scope as $class)
        {
            // Make a timestamp of just the day to use as key for that day's classes
            if (!empty($class['StartDateTime'])) {
                $dt = new \DateTime($class['StartDateTime']);
                $just_date =  $dt->format('Y-m-d');
            } else {
                var_dump($class);
                continue;
            }


            /* Create a new array with a key for each date YYYY-MM-DD
            and corresponding value an array of class details */

            $single_event = new Single_Event($class, $this->atts);

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

        return $this->classesByDateThenTime;
    }

}
