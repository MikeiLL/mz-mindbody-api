<?php
/**
 * Retrieve Events
 *
 * This file contains the class that extends Retrieve, specifically
 * for fetching Mindbody "classes."
 *
 * @package MzMindbody
 */

namespace MZoo\MzMindbody\Events;

use MZoo\MzMindbody as NS;
use MZoo\MzMindbody\Core as Core;
use MZoo\MzMindbody\Libraries as Libraries;
use MZoo\MzMindbody\Schedule as Schedule;
use MZoo\MzMindbody\Common\Interfaces as Interfaces;

/**
 * Class that is extended for Events Display Shortcode(s)
 */
class RetrieveEvents extends Interfaces\RetrieveClasses {

    /**
     * Holder for events array returned by MBO API
     *
     * @since  2.4.7
     * @access public
     * @var    array $classes Array of events returned from MBO API
     */
    public $classes;
    /**
     * Holds the display time frame for the instance.
     *
     * @since  2.4.7
     * @access public
     * @var    array    $display_time_frame    StartDateTime and end_datetime Timestamps
     *                                          used in MBO API call, displayed in navigation.
     */
    public $display_time_frame;
    /**
     * Holds the current day, with offset, based on "offset" attribute/parameter.
     *
     * Set by time_frame() and used by sortClassesByDateThenTime()
     *
     * @since  2.4.7
     * @access public
     * @var    string    $current_day_offset    Formatted Datetime object.
     */
    public $current_day_offset;

    /**
     * Return Time Frame for request to MBO API
     *
     * Default time_frame is two dates, start of current week as set in WP, and seven days from "now.
     *
     * @since 2.4.7
     * @param timestamp $timestamp Time to begin timeframe from.
     * @return array or start and end dates as required for MBO API
     */
    public function time_frame( $timestamp = null ) {

        $timestamp  = isset( $timestamp ) ? $timestamp : strtotime( wp_date( 'Y-m-d H:i:s' ) );
        $start_time = new \Datetime( wp_date( 'Y-m-d', $timestamp ) );
        $end_time   = new \Datetime( wp_date( 'Y-m-d', $timestamp ) );
        // Init Session_types variable to empty.
        $session_types = array();
        // Init duration to 60.
        $duration = 60;
        if ( ! empty( Core\MzMindbodyApi::$events_options['mz_mindbody_eventID'] ) ) {
            $session_types = explode( ',', Core\MzMindbodyApi::$events_options['mz_mindbody_eventID'] );
        }

        $duration = Core\MzMindbodyApi::$event_calendar_duration;
        if ( ( ! empty( $this->atts['week-only'] ) ) && ( 1 === (int) $this->atts['week-only'] ) ) {
            $duration = 7;
        }

        $di = new \DateInterval( 'P' . $duration . 'D' );
        $end_time->add( $di );
        $current_day_offset = new \Datetime( wp_date( 'Y-m-d' ) );

        // If we are going in future or past based on offset.
        if ( ! empty( $this->atts['offset'] ) ) {
            // Insure that we have an absolute number, because attr may be negative.
            $abs            = abs( $this->atts['offset'] );
            $days_to_offset = $duration * $abs + 1;
            $di             = new \DateInterval( 'P' . $days_to_offset . 'D' );
            // If it's a negative number, invert the interval.
            if ( $this->atts['offset'] < 0 ) {
                $di->invert = 1;
            }
            $start_time->add( $di );
            $end_time->add( $di );
        }

        $this->display_time_frame = array(
            'start' => $start_time,
            'end'   => $end_time,
        );
        $simple_timeframe         = array(
            'StartDateTime' => $start_time->format( 'Y-m-d' ),
            'EndDateTime'   => $end_time->format( 'Y-m-d' ),
        );

        $full_call = array_merge( $simple_timeframe, array( 'SessionTypeIDs' => $session_types ) );
        return $full_call;
    }


    /**
     * Sort Events array by MBO time
     *
     * @since 2.4.7
     *
     * @return array of MBO schedule data, time
     */
    public function sortEventsByTime() {

        foreach ( $this->classes as $class ) {
            // Make a timestamp of just the day to use as key for that day's classes.
            if ( ! empty( $class['StartDateTime'] ) ) {
                $dt        = new \DateTime( $class['StartDateTime'] );
                $just_date = $dt->format( 'Y-m-d' );
            } else {
                // If no StartDateTime.
                continue;
            }

            // Don't include classes that aren't in $this->atts locations array.
            // Coerced to integers in Display so we can use strict testing here.
            if ( ! in_array( $class['Location']['Id'], $this->atts['locations'], true ) ) {
                continue;
            }

            // Populate the Locations Dictionary.
            $this->populateLocationsDictionary( $class );

            /*
             * Create a new array with a key for each date YYYY-MM-DD
             * and corresponding value an array of class details.
             */
            $single_event = new SingleEvent( $class, $this->atts );
            if ( ! empty( $this->classes_by_date_then_time[ $just_date ] ) ) {
                array_push( $this->classes_by_date_then_time[ $just_date ], $single_event );
            } else {
                $this->classes_by_date_then_time[ $just_date ] = array( $single_event );
            }
        }
        /* They are not ordered by date so order them by date. */
        ksort( $this->classes_by_date_then_time );
        foreach ( $this->classes_by_date_then_time as $class_date => &$classes ) {
            /*
            * $classes is an array of all classes for given date
            * Take each of the class arrays and order it by time
            * $classes_by_date_then_time should have a length of seven, one for
            * each day of the week.
            */
            usort(
                $classes,
                function ( $a, $b ) {

                    if ( $a->start_datetime === $b->start_datetime ) {
                        return 0;
                    }
                    return $a->start_datetime < $b->start_datetime ? -1 : 1;
                }
            );
        }

        return $this->classes_by_date_then_time;
    }
}
