<?php
/**
 * Schedule Operations
 *
 * This file contains the class which contains methods used
 * in displaying the MBO schedules.
 *
 * @package MzMindbody
 */

namespace MZoo\MzMindbody\Common;

use MZoo\MzMindbody as NS;

/**
 * Schedule Operations
 *
 * Methods used in by various schedule shortcodes and functions.
 */
class ScheduleOperations {

    /**
     * Retrieve name of Start of Week for current WP install.
     */
    private function week_start_day() {
        switch ( get_option( 'start_of_week' ) ) {
            case 0:
                return 'Sunday';
            case 1:
                return 'Monday';
            case 2:
                return 'Tuesday';
            case 3:
                return 'Wednesday';
            case 4:
                return 'Thursday';
            case 5:
                return 'Friday';
            case 6:
                return 'Saturday';
            // Shouldn't be necessary but just in case.
            default:
                return 'Monday';
        }
    }

    /**
     * Returns an array of two date objects:
     *
     * @since  1.0
     * @source (initially adapted)
     * http://stackoverflow.com/questions/186431/calculating-days-of-week-given-a-week-number
     * Used by Sorter::sortclassesByDateThenTime function
     * also used by mZ_mbo_pages_pages() in Mz MBO Pages plugin
     *
     * @return array Start Date, End Date and Previous Range Start Date.
     */
    public static function current_to_day_of_week_today() {
        $current          = isset( $_GET['mz_date'] ) ? new \DateTime( $_GET['mz_date'], wp_timezone() ) : new \DateTime( null, wp_timezone() );
        $today            = new \DateTime( null, wp_timezone() );
        $current_day_name = $today->format( 'D' );
        $days_of_the_week = array(
            'Mon',
            'Tue',
            'Wed',
            'Thu',
            'Fri',
            'Sat',
            'Sun',
        );
        foreach ( $days_of_the_week as $day_name ) :
            if ( $current_day_name !== $day_name ) :
                $current->add( new \DateInterval( 'P1D' ) );
            else :
                break;
            endif;
        endforeach;
        $clone_current = clone $current;
        return array( $current, gmdate( 'Y-m-d', strtotime( $clone_current->format( 'y-m-d' ) ) ) );
    }

    /**
     * MZ Validate Date
     *
     * @param string $string To check for non-date characters.
     */
    public static function mz_validate_date( $string ) {
        if ( preg_match( '/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $string ) ) {
            return $string;
        } else {
            return 'mz_validate_weeknum error';
        }
    }
}
