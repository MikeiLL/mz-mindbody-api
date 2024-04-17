<?php
/**
 * Retrieve Registrants
 *
 * This file contains the class that extends Retrieve, specifically
 * for fetching registrants for specified MBO event/class.
 *
 * @package MzMindbody
 */

namespace MZoo\MzMindbody\Schedule;

use MZoo\MzMindbody as NS;
use MZoo\MzMindbody\Core as Core;
use MZoo\MzMindbody\Libraries as Libraries;
use MZoo\MzMindbody\Schedule as Schedule;
use MZoo\MzMindbody\Common\Interfaces as Interfaces;

/**
 * Class that is extended for Schedule Display Ajax Retrieve Registrant(s).
 */
class RetrieveRegistrants extends Interfaces\Retrieve {

    /**
     * Holds the Get Class Visits Results for a given class.
     *
     * @since  2.4.7
     * @access public
     * @var    array $class_visits Array of names of class registrants.
     */
    public $class_visits;


    /**
     * Holds the registrants for a given class.
     *
     * @since  2.4.7
     * @access public
     * @var    array $registrants Array of names of class registrants.
     */
    public $registrants;


    /**
     * Get a timestamp, return data from MBO api, store it in a transient and
     * as object attribute.
     *
     * @since 2.4.7
     *
     * @param int $classid from Mindbody.
     *
     * @return array of MBO schedule data
     */
    public function get_mbo_results( $classid = 0 ) {

        if ( empty( $classid ) ) {
            return false;
        }

        $mb = $this->instantiate_mbo_api();

        if ( ! $mb || 'NO_API_SERVICE' === $mb ) {
            return false;
        }

        if ( 0 !== $this->mbo_account ) {
            // If account has been specified in shortcode, update credentials.
            $mb->source_credentials['SiteIDs'][0] = $this->mbo_account;
        }

        $this->class_visits = $mb->GetClassVisits( array( 'ClassID' => $classid ) );

        return $this->class_visits;
    }

    /**
     * Get Registrants called via Ajax
     */
    function ajax_get_registrants() {
        // Generated in Schedule\ScheduleItem.
        check_ajax_referer( 'mz_display_schedule', 'nonce' );

        $classid = $_REQUEST['classID'];

        $result['type'] = 'success';

        $registrants = $this->get_registrants( $classid );

        if ( ! $registrants ) :
            $result['type'] = 'error';
        endif;

        $result['message'] = $registrants;

        if ( ! empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) &&
            'xmlhttprequest' === strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) ) {
            $result = wp_json_encode( $result );
            echo $result;
        } else {
            header( 'Location: ' . $_SERVER['HTTP_REFERER'] );
        }

        die();
    }
    // End Ajax Get Registrants.

    /**
     * Get Registrants from MBO
     *
     * @param int $classid Unique ID for specific class to get registrants for.
     */
    function get_registrants( $classid ) {

        $class_visits = $this->get_mbo_results( $classid );

        if ( ! $class_visits ) :
            return false;
        else :
            if ( empty( $class_visits['Class']['Visits'] ) ) :
                return __( 'No registrants yet.', 'mz-mindbody-api' );
        else :
            $registrant_ids = array_map(function($registrant) {
                return $registrant['ClientId'];
            }, $class_visits['Class']['Visits']);

            // send list of registrants to GetRegistrants.
            $mb = $this->instantiate_mbo_api();

            if ( ! $mb || 'NO_API_SERVICE' === $mb ) {
                return false;
            }

            if ( 0 !== $this->mbo_account ) {
                // If account has been specified in shortcode, update credentials.
                $mb->source_credentials['SiteIDs'][0] = $this->mbo_account;
            }

            // send true to GetClients to get multiple clients.
            $this->registrants = $mb->GetClients( array( 'clientIds' => $registrant_ids ), true );

            // Add first name, last initial.
            $registrant_names = array_map(function($registrant) {
                return $registrant['FirstName'] . ' ' . substr( $registrant['LastName'], 0, 1 );
            }, $this->registrants['Clients']);

            endif;
        endif;

        return $registrant_names;
    }
}
