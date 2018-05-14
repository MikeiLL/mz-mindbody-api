<?php
namespace MZ_Mindbody\Inc\Backend;

use MZ_Mindbody\Inc\Common\Interfaces as Interfaces;

class Retrieve_Debug extends Interfaces\Retrieve {

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

        $transient_string = ''; // $this->generate_transient_name(array($this->account));

        if ( false === get_transient( $transient_string ) ) {
            // If there's not a transient already, call the API and create one

            if ($this->account !== 0) {
                // If account has been specified in shortcode, update credentials
                $response->sourceCredentials['SiteIDs'][0] = $this->account;
            }
            set_transient($transient_string, $response, 60 * 60 * 12);

        } else {
            $response = get_transient( $transient_string );
        }

        return $response->GetClasses($this->time_frame());
    }

}
