<?php
namespace MZ_Mindbody\Inc\Backend;

use MZ_Mindbody\Inc\Common\Interfaces as Interfaces;

class Retrieve_Debug extends Interfaces\Retrieve {

    /*
	 * Return Time Frame for request to MBO API
	 *
	 * @since 2.4.7
	 *
	 * Default time_frame is two dates, start of current week as set in WP, and seven days from "now.
	 *
	 * @return array or start and end dates as required for MBO API
	 */
    public function time_frame($timestamp = null){
        $time = new \Datetime( date('Y-m-d', current_time( 'timestamp' )) );
        return array('StartDateTime'=> $time->format('Y-m-d'), 'EndDateTime'=> $time->format('Y-m-d'));
    }


    /*
     * Return data from MBO api
     *
     * @since 2.4.7
     *
     * @param @timestamp defaults to current time
     *
     * @return array of MBO schedule data
     */
    public function get_mbo_results($timestamp = null){

        $mb = $this->instantiate_mbo_API();

        if (!$mb) return false;

        if ($mb == 'NO_SOAP_SERVICE') {
            return $mb;
        }
        $this->classes = $mb->GetClasses($this->time_frame());
        return $mb->debug();
    }

}
