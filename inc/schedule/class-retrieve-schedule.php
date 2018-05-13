<?php
namespace MZ_Mindbody\Inc\Schedule;

use MZ_Mindbody;
use MZ_Mindbody\Inc\Core as Core;
use MZ_Mindbody\Inc\Common as Common;
use MZ_Mindbody\Inc\Common\Interfaces as Interfaces;

class Retrieve_Schedule extends Interfaces\Retrieve {

	/*
	 * Return Time Frame for request to MBO API
	 *
	 * @since 2.4.7
	 *
	 * Default time_frame is two dates, start of current week as set in WP, and seven days from "now.
	 *
	 * @return array or start and end dates as required for MBO API
	 */
	public function time_frame(){
		$current_week = $this->current_week();
		$seven_days_from_now = $this->seven_days_from_now();
		$start_time = new \Datetime( date_i18n('Y-m-d', $current_week['start']) );
		$end_time = new \Datetime( date_i18n('Y-m-d', $seven_days_from_now) );
		return array('StartDateTime'=> $start_time->format('Y-m-d'), 'EndDateTime'=> $end_time->format('Y-m-d'));
	}
	
	/*
	 * Get data from MBO apiVersion
	 *
	 * @since 2.4.7
	 * @return array of MBO schedule data
	 */
	public function get_mbo_results(){

        $mb = $this->instantiate_mbo_API();        
		if ($mb == 'NO_SOAP_SERVICE') {
				return $mb;
			}
		if ($this->account == 0) {
			return $mb->GetClasses($this->time_frame());
		} else {
			$mb->sourceCredentials['SiteIDs'][0] = $this->account; 
			return $mb->GetClasses($this->time_frame());
		}
	}
	
	
}