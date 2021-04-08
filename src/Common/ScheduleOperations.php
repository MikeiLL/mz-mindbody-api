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
	 * Retrieve name of Start of Week for current WP install
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
			// Shouldn't be necessary but just in case
			default:
				return 'Monday';
		}
	}

	/**
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
	public static function get_blog_timezone() {

		$tzstring = get_option( 'timezone_string' );
		$offset   = get_option( 'gmt_offset' );

		// Manual offset...
		// @see http://us.php.net/manual/en/timezones.others.php
		// @see https://bugs.php.net/bug.php?id=45543
		// @see https://bugs.php.net/bug.php?id=45528
		// IANA timezone database that provides PHP's timezone support uses POSIX (i.e. reversed) style signs
		if ( empty( $tzstring ) && 0 != $offset && floor( $offset ) == $offset ) {
			$offset_st = $offset > 0 ? "-$offset" : '+' . absint( $offset );
			$tzstring  = 'Etc/GMT' . $offset_st;
		}

		// Issue with the timezone selected, set to 'UTC'
		if ( empty( $tzstring ) ) {
			$tzstring = 'UTC';
		}

		if ( $tzstring instanceof \DateTimeZone ) {
			return $tzstring;
		}

		$timezone = new \DateTimeZone( $tzstring );
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
	public static function current_to_day_of_week_today() {
		$current          = isset( $_GET['mz_date'] ) ? new \DateTime( $_GET['mz_date'], self::get_blog_timezone() ) : new \DateTime( null, self::get_blog_timezone() );
		$today            = new \DateTime( null, self::get_blog_timezone() );
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
			if ( $current_day_name != $day_name ) :
				$current->add( new \DateInterval( 'P1D' ) );
		 else :
			 break;
		 endif;
		endforeach;
		$clone_current = clone $current;
		return array( $current, date( 'Y-m-d', strtotime( $clone_current->format( 'y-m-d' ) ) ) );
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
