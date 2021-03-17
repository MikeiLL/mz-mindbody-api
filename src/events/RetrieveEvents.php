<?php

namespace MzMindbody\Inc\Events;

use MzMindbody as NS;
use MzMindbody\Inc\Core as Core;
use MzMindbody\Inc\Libraries as Libraries;
use MzMindbody\Inc\Schedule as Schedule;
use MzMindbody\Inc\Common\Interfaces as Interfaces;

/**
 * Class that is extended for Events Display Shortcode(s)
 */
class RetrieveEvents extends Interfaces\RetrieveClasses
{



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
     * @var      array    $display_timeFrame    StartDateTime and endDateTime Timestamps
     *                                          used in MBO API call, displayed in navigation.
     */
    public $display_timeFrame;
/**
     * Holds the current day, with offset, based on "offset" attribute/parameter.
     *
     * set by timeFrame() and used by sortClassesByDateThenTime()
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
     * Default timeFrame is two dates, start of current week as set in WP, and seven days from "now.
     *
     * @return array or start and end dates as required for MBO API
     */
    public function timeFrame($timestamp = null)
    {

        $timestamp = isset($timestamp) ? $timestamp : current_time('timestamp');
        $start_time =  new \Datetime(date_i18n('Y-m-d', $timestamp));
        $end_time = new \Datetime(date_i18n('Y-m-d', $timestamp));
        $session_types = explode(',', Core\MzMindbodyApi::$events_options['mz_mindbody_eventID']);
        $duration = ((!empty($this->atts['week-only'])) && ($this->atts['week-only'] == 1)) ? 7 : Core\MzMindbodyApi::$event_calendar_duration;
        $di = new \DateInterval('P' . $duration . 'D');
        $end_time->add($di);
        $current_day_offset = new \Datetime(date_i18n('Y-m-d'));
// If we are going in future or past based on offset
        if (!empty($this->atts['offset'])) {
// Insure that we have an absolute number, because attr may be negative
            $abs = abs($this->atts['offset']);
            $days_to_offset = $duration * $abs + 1;
            $di = new \DateInterval('P' . $days_to_offset . 'D');
// If it's a negative number, invert the interval
            if ($this->atts['offset'] < 0) {
                $di->invert = 1;
            }
            $start_time->add($di);
            $end_time->add($di);
        }

        $this->display_timeFrame = array('start' => $start_time, 'end' => $end_time);
        $simple_timeframe = array('StartDateTime' => $start_time->format('Y-m-d'), 'EndDateTime' => $end_time->format('Y-m-d'));
        $full_call = array_merge($simple_timeframe, array('SessionTypeIDs' => $session_types));
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
    public function sortEventsByTime()
    {


        foreach ($this->classes as $class) {
//NS\MZMBO()->helpers->print($this->atts['locations']);
        //NS\MZMBO()->helpers->print($class);
            // Make a timestamp of just the day to use as key for that day's classes
            if (!empty($class['StartDateTime'])) {
                $dt = new \DateTime($class['StartDateTime']);
                $just_date =  $dt->format('Y-m-d');
            } else {
            // If no StartDateTime
                continue;
            }

            // Don't include classes that aren't in $this->atts locations array.
            if (!in_array($class['Location']['Id'], $this->atts['locations'])) {
                continue;
            }

            // Populate the Locations Dictionary
            $this->populateLocationsDictionary($class);
/* Create a new array with a key for each date YYYY-MM-DD
            and corresponding value an array of class details */

            $single_event = new SingleEvent($class, $this->atts);
            if (!empty($this->classesByDateThenTime[$just_date])) {
                array_push($this->classesByDateThenTime[$just_date], $single_event);
            } else {
                $this->classesByDateThenTime[$just_date] = array($single_event);
            }
        }
        /* They are not ordered by date so order them by date */
        ksort($this->classesByDateThenTime);
        foreach ($this->classesByDateThenTime as $classDate => &$classes) {
        /*
             * $classes is an array of all classes for given date
             * Take each of the class arrays and order it by time
             * $classesByDateThenTime should have a length of seven, one for
             * each day of the week.
             */
            usort($classes, function ($a, $b) {

                if ($a->startDateTime == $b->startDateTime) {
                    return 0;
                }
                return $a->startDateTime < $b->startDateTime ? -1 : 1;
            });
        }

        return $this->classesByDateThenTime;
    }
}
