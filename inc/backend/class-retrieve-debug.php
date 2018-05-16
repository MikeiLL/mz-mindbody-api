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

        $timestamp = isset($timestamp) ? $timestamp : current_time( 'timestamp' );

        $mb = $this->instantiate_mbo_API();

        if ($mb == 'NO_SOAP_SERVICE') {
            $this->classes = $mb;
            return false;
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
        $this->classes = $mb->GetClasses($this->time_frame());
        return $mb;
    }

}
