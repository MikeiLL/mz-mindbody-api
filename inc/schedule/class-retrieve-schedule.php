<?php
namespace MZ_Mindbody\Inc\Schedule;

use MZ_Mindbody;
use MZ_Mindbody\Inc\Core as Core;
use MZ_Mindbody\Inc\Common as Common;
use MZ_Mindbody\Inc\Common\Interfaces as Interfaces;

class Retrieve_Schedule extends Interfaces\Retrieve_Classes {

	/**
	 * Return Time Frame for request to MBO API
	 *
	 * @since 2.4.7
	 *
	 * Default time_frame is two dates, start of current week as set in WP, and seven days from "now.
     *
     * @throws \Exception
	 *
	 * @return array or start and end dates as required for MBO API
	 */
	public function time_frame($timestamp = null){
	    $timestamp = isset($timestamp) ? $timestamp : current_time( 'timestamp' );
		$current_week = $this->single_week($timestamp);
		$seven_days_from_now = $this->seven_days_later($timestamp);
		$start_time = new \Datetime( date_i18n('Y-m-d', $current_week['start']) );
		$end_time = new \Datetime( date_i18n('Y-m-d', $seven_days_from_now) );
      	$current_day_offset = new \Datetime( date_i18n('Y-m-d') );
      	$current_week_end = new \Datetime( date_i18n('Y-m-d', $current_week['end']) );

		// If we are going in future or past based on offset
		if ( !empty($this->atts['offset']) ) {
		    // Insure that we have an absolute number, because attr may be negative
		    $abs = abs($this->atts['offset']);
		    $di = new \DateInterval('P'.$abs.'W');
		    // If it's a negative number, invert the interval
            if ($this->atts['offset'] < 0) $di->invert = 1;
            $start_time->add($di);
            $end_time->add($di);
            $current_week_end->add($di);
            $current_day_offset->add($di);
        }

        // Set current_day_offset for filtering by sort_classes_by_date_then_time().
        $this->current_day_offset = $current_day_offset;

		// Assign start_date & end_date to instance so can be accessed in grid schedule display
        $this->start_date = $start_time;
        $this->current_week_end = $current_week_end;

		return array('StartDateTime'=> $start_time->format('Y-m-d'), 'EndDateTime'=> $end_time->format('Y-m-d'));
	}

	
	
}