<?php
namespace MZ_Mindbody\Inc\Common\Interfaces;

use MZ_Mindbody\Inc\Libraries as Libraries;

abstract class Retrieve_Classes extends Retrieve {

    /*
     * Get data from MBO api
     *
     * @since 2.4.7
     * @return array of MBO schedule data
     */
    public function get_mbo_results(){

        $mb = $this->instantiate_mbo_API();
        if ($mb == 'NO_SOAP_SERVICE') {
            return $mb;
        }
        if ( false === get_transient( $mz_mb_cache ) ) {
            if ($this->account == 0) {
                return $mb->GetClasses($this->time_frame());
            } else {
                $mb->sourceCredentials['SiteIDs'][0] = $this->account;
                return $mb->GetClasses($this->time_frame());
            }
            set_transient($mz_mb_cache, $mb, 60 * 60 * 12);
        } else {
            return get_transient( $mz_mb_cache );
        }
    }
	
	/*
	 * Return current week start and end timestamps.
	 *
	 * @since 2.4.7
	 * @return array 'start', 'end' of current week in timestamps
	 */
	public function current_week(){
		return get_weekstartend(current_time( 'mysql' ), $this->start_of_week);
	}
	
	/*
	 * Return timestamp of seven days from now.
	 *
	 * @since 2.4.7
	 *
	 * @return timestamp of seven days from now
	 */
	public function seven_days_from_now(){
		return strtotime("+7 day", current_time( 'timestamp' ));
	}
	
	/*
	 * Displayable current week start and end timestamps.
	 *
	 * @since 2.4.7
	 * @return html string of start and end of current week
	 */
	public function current_week_display(){
		$time_frame = $this->current_week();
		$return = 'Week start: ' . date('M d, Y', $time_frame[start]) . '<br/>';
		$return .= 'Week end: ' . date('M d, Y', $time_frame[end]);
		return $return;
	}

}
