<?php
namespace MZ_Mindbody\Inc\Common\Interfaces;

use MZ_Mindbody\Inc\Core as Core;
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

        $transient_string = $this->generate_transient_name(array($this->mbo_account));

        if ( false === get_transient( $transient_string ) ) {
            // If there's not a transient already, call the API and create one

            if ($this->mbo_account !== 0) {
                // If account has been specified in shortcode, update credentials
                $mb->sourceCredentials['SiteIDs'][0] = $this->mbo_account;
            }
            set_transient($transient_string, $mb, 60 * 60 * 12);

        } else {
            $mb = get_transient( $transient_string );
        }
        return $mb->GetClasses($this->time_frame());
    }
	
	/*
	 * Return current week start and end timestamps.
	 *
	 * @since 2.4.7
	 * @return array 'start', 'end' of current week in timestamps
	 */
	public function current_week(){
		return get_weekstartend(current_time( 'mysql' ), Core\Init::$start_of_week);
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

    /*
     * Set up Time Frame with Start and End times for Schedule Request
     *
     * @since 2.4.7
     */
        abstract public function time_frame();

}
