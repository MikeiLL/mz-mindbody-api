<?php
namespace MZ_Mindbody\Inc\Backend;

use MZ_Mindbody as NS;
use MZ_Mindbody\Inc\Common\Interfaces as Interfaces;

class Retrieve_Debug extends Interfaces\Retrieve {

	public $property_test = 'thing';
	
    /**
	 * Return Time Frame for request to MBO API
	 *
	 * @since 2.4.7
     *
     * @throws \Exception
	 *
	 * Default time_frame is two dates, start of current week as set in WP, and seven days from "now.
	 *
	 * @return array or start and end dates as required for MBO API
	 */
    public function time_frame($timestamp = null){
        $time = new \Datetime( date('Y-m-d', current_time( 'timestamp' )) );
        return array('StartDateTime'=> $time->format('Y-m-d'), 'EndDateTime'=> $time->format('Y-m-d'));
    }


    /**
     * Return data from MBO api
     *
     * @since 2.4.7
     *
     * @param @timestamp defaults to current time
     *
     * @throws \Exception
     *
     * @return array of MBO schedule data
     */
    public function get_mbo_results($timestamp = null, $version_five = false ){

        $mb = ($version_five === false) ? $this->instantiate_mbo_API() : $this->instantiate_mbo_API( 5 );
				
        if (!$mb) return false;

        if ( !is_object($mb) && is_string($mb) && strpos($mb, 'NO_API_SERVICE') ) {
            return $mb;
        }
        
        if ($version_five !== false) $this->classes = $mb->GetClasses($this->time_frame());
        
        return $mb->debug();
    }

}
