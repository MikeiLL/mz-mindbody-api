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
	 */
	public function time_frame($start_time){
		return get_weekstartend(current_time( 'mysql' ), $this->start_of_week);
	}
	
	
}