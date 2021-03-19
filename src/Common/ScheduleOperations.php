<?php

namespace MZoo\MzMindbody\Common;

use MZoo\MzMindbody as NS;

class ScheduleOperations
{

    /**
     * Summary.
     *
     * Gets a YYYY-mm-dd date and returns an array of three elements:
     *      array(start of requested week, end of requested week)
     *      following week start date
     *      previous week start date.
     *
     * @since  1.0
     * @source (initially adapted)
     * http://stackoverflow.com/questions/186431/calculating-days-of-week-given-a-week-number
     * Used by mz_show_schedule(), mz_show_events(), mz_mindbody_debug_text()
     * also used by mZ_mbo_pages_pages() in mZ MBO Pages plugin
     *
     * @param  var $date     Start date for date range.
     * @param  var $duration Optional. Description. Default.
     * @return matrix: array(Start Date, End Date) and Previous Range Start Date.
     */
    public static function mz_getDateRange( $date, $duration = 7 )
    {
        $seconds_in_a_day = 86400;
        $start            = new \DateTime($date);
        $now              = new \DateTime(null, self::get_blog_timezone());
        switch ( $now->format('l') ) {
        case 'Tuesday':
            $into_following_week = 1;
        case 'Wednesday':
            $into_following_week = 2;
        case 'Thursday':
            $into_following_week = 3;
            break;
        case 'Friday':
            $into_following_week = 4;
            break;
        case 'Saturday':
            $into_following_week = 5;
            break;
        case 'Sunday':
            $into_following_week = 6;
            break;
        default:
            $into_following_week = 0;
            break;
        }
        $end        = clone $start;
        $previous   = clone $start;
        $subsequent = clone $start;
        $subsequent->add(new \DateInterval('P' . ( $duration ) . 'D'));
        $temp = new \DateInterval('P' . ( $duration + $into_following_week ) . 'D');
        $end->add(new \DateInterval('P' . ( $duration + $into_following_week ) . 'D'));
        $previous->sub(new \DateInterval('P' . $duration . 'D'));
        $return[0] = array(
        'StartDateTime' => $start->format('Y-m-d'),
        'EndDateTime'   => $end->format('Y-m-d'),
        );

        $return[1] = $subsequent->modify('Monday this week')->format('Y-m-d');
        $return[2] = $previous->modify('Monday this week')->format('Y-m-d');
        // NS\MZMBO()->helpers->print($return);
        return $return;
    }

    /*
    * Retrieve name of Start of Week for current WP install
    */
    private function week_start_day()
    {
        switch ( get_option('start_of_week') ) {
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
            // Shouldn't be necessary but just in case
        default:
            return 'Monday';
        }
    }

    /*
    *  Returns the blog timezone
    *
    * Gets timezone settings from the db. If a timezone identifier is used just turns
    * it into a \DateTimeZone. If an offset is used, it tries to find a suitable timezone.
    * If all else fails it uses UTC.
    *
    * Source: http://wordpress.stackexchange.com/a/198453/48604
    *
    * @return \DateTimeZone The blog timezone
    */
    public static function get_blog_timezone()
    {

        $tzstring = get_option('timezone_string');
        $offset   = get_option('gmt_offset');

        // Manual offset...
        // @see http://us.php.net/manual/en/timezones.others.php
        // @see https://bugs.php.net/bug.php?id=45543
        // @see https://bugs.php.net/bug.php?id=45528
        // IANA timezone database that provides PHP's timezone support uses POSIX (i.e. reversed) style signs
        if (empty($tzstring) && 0 != $offset && floor($offset) == $offset ) {
            $offset_st = $offset > 0 ? "-$offset" : '+' . absint($offset);
            $tzstring  = 'Etc/GMT' . $offset_st;
        }

        // Issue with the timezone selected, set to 'UTC'
        if (empty($tzstring) ) {
            $tzstring = 'UTC';
        }

        if ($tzstring instanceof \DateTimeZone ) {
            return $tzstring;
        }

        $timezone = new \DateTimeZone($tzstring);
        return $timezone;
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
    public static function current_to_day_of_week_today()
    {
        $current          = isset($_GET['mz_date']) ? new \DateTime($_GET['mz_date'], self::get_blog_timezone()) : new \DateTime(null, self::get_blog_timezone());
        $today            = new \DateTime(null, self::get_blog_timezone());
        $current_day_name = $today->format('D');
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
            if ($current_day_name != $day_name ) :
                $current->add(new \DateInterval('P1D'));
         else :
             break;
         endif;
        endforeach;
        $clone_current = clone $current;
        return array( $current, date('Y-m-d', strtotime($clone_current->format('y-m-d'))) );
    }

    /*
    * Make a clean array with seven corresponding slots and populate
    * based on indicator (day) for each class. There may be more than
    * one even for each day and empty arrays will represent empty time slots.
    */
    public static function weekOfTimeslot( $array, $indicator )
    {
        $seven_days = array_combine(
            range(1, 7),
            array(
            array(),
            array(),
            array(),
            array(),
            array(),
            array(),
            array(),
            )
        );
        foreach ( $seven_days as $key => $value ) {
            foreach ( $array as $class ) {
                if ($class->$indicator == $key ) {
                    array_push($seven_days[ $key ], $class);
                }
            }
        }
        return $seven_days;
    }

    /*
    * @param string
    */
    public static function mz_validate_date( $string )
    {
        if (preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $string) ) {
            return $string;
        } else {
            return 'mz_validate_weeknum error';
        }
    }
}
