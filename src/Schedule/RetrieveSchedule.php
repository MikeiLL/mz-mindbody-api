<?php
/**
 * Retrieve Schedule
 *
 * This file contains the class that extends Retrieve, specifically
 * for fetching Mindbody "classes."
 *
 * @package MzMindbody
 */

namespace MZoo\MzMindbody\Schedule;

use MZoo\MzMindbody as NS;
use MZoo\MzMindbody\Core as Core;
use MZoo\MzMindbody\Common as Common;
use MZoo\MzMindbody\Common\Interfaces as Interfaces;

/**
 * Retrieve Schedule
 *
 * Extend Retrieve Classes with methods to retrieve and display schedule.
 */
class RetrieveSchedule extends Interfaces\RetrieveClasses {

    /**
     * Return Time Frame for request to MBO API
     *
     * @since 2.4.7
     *
     * Default time_frame is two dates, start of current week as set in WP, and seven days from "now."
     *
     * @param timestamp $timestamp Time to begin timeframe from.
     * @param string    $duration  Strtotime format string for duration.
     * @return array of start and end dates as required for MBO API.
     */
    public function time_frame( $timestamp = null, $duration = '+6 day' ) {

        // Since WP v5.3 current_time('timestamp') is depreciated.
        $today = strtotime( wp_date( 'Y-m-d H:i:s' ) );

        $timestamp = isset( $timestamp ) ? $timestamp : $today;
        // Can override timestamp here for testing $timestamp = '2020-5-1'.

        $current_week     = $this->singleWeek( $timestamp );
        $end_timestamp = strtotime( $duration, $timestamp );
        if ( ( ! empty( $this->atts['type'] ) && ( 'day' === $this->atts['type'] ) ) ) :
            $start_time = new \Datetime( gmdate( 'Y-m-d', $today ) );
            $end_time   = new \Datetime( gmdate( 'Y-m-d', $today ) );
            // Can test with $end_time = new \DateTime('tomorrow').
        else :
            $start_time = new \Datetime( gmdate( 'Y-m-d', $current_week['start'] ) );
            $end_time   = new \Datetime( gmdate( 'Y-m-d', $end_timestamp ) );
        endif;
        $current_day_offset = new \Datetime( gmdate( 'Y-m-d' ) );
        $current_week_end   = new \Datetime( gmdate( 'Y-m-d', $current_week['end'] ) );

        // If we are going in future or past based on offset.
        if ( ! empty( $this->atts['offset'] ) ) {
            // Insure that we have an absolute number, because attr may be negative.
            $abs = abs( $this->atts['offset'] );
            if ( ( ! empty( $this->atts['type'] ) && ( 'day' === $this->atts['type'] ) ) ) :
                $di = new \DateInterval( 'P' . $abs . 'D' );
        else :
            $di = new \DateInterval( 'P' . $abs . 'W' );
        endif;

        // If it's a negative number, invert the interval.
        if ( 0 > $this->atts['offset'] ) {
            $di->invert = 1;
        }
        $start_time->add( $di );
        $end_time->add( $di );
        $current_week_end->add( $di );
        $current_day_offset->add( $di );
        }

        // Set current_day_offset for filtering by sortClassesByDateThenTime().
        $this->current_day_offset = $current_day_offset;

        // Assign start_date & end_date to instance so can be accessed in grid schedule display.
        $this->start_date       = $start_time;
        $this->current_week_end = $current_week_end;

        return array(
            'StartDateTime' => $start_time->format( 'Y-m-d' ),
            'EndDateTime'   => $end_time->format( 'Y-m-d' ),
        );
    }
}
