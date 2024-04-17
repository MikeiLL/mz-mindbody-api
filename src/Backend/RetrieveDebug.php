<?php
/**
 * Retrieve Debug
 *
 * This file contains the class with methods for
 * testing the API within the WordPress Admin.
 *
 * @package MzMindbody
 */

namespace MZoo\MzMindbody\Backend;

use MZoo\MzMindbody as NS;
use MZoo\MzMindbody\Common\Interfaces as Interfaces;

/**
 * Retrieve Debug
 *
 * This class holds the methods used in WP Admin to debug MBO connection.
 */
class RetrieveDebug extends Interfaces\Retrieve {

    /**
     * Return Time Frame for request to MBO API
     *
     * @since 2.4.7
     *
     * Default time_frame is two dates, start of current week as set in WP, and seven days from "now.
     *
     * @param timestamp $timestamp required to make MBO request for schedule.
     * @return array or start and end dates as required for MBO API
     */
    public function time_frame( $timestamp = null ) {
        $time = new \Datetime( gmdate( 'Y-m-d', strtotime( wp_date( 'Y-m-d H:i:s' ) ) ) );
        return array(
            'StartDateTime' => $time->format( 'Y-m-d' ),
            'EndDateTime'   => $time->format( 'Y-m-d' ),
        );
    }


    /**
     * Return data from MBO api
     *
     * @since 2.4.7
     *
     * @param timestamp $timestamp required to make MBO request for schedule.
     *
     * @return array of MBO schedule data.
     */
    public function get_mbo_results( $timestamp = null ) {

        $mb = $this->instantiate_mbo_api();

        if ( ! $mb ) {
            return false;
        }

        if ( ! is_object( $mb ) && is_string( $mb ) && strpos( $mb, 'NO_API_SERVICE' ) ) {
            return $mb;
        }

        try {
            $this->classes = $mb->GetClasses( $this->time_frame() );
        } catch ( \Exception $e ) {
            $details = __( 'Check Max number of daily API calls in the Advanced tab.', 'mz-mindbody-api' );
            return '<div class="notice notice-error settings-error">' . $e->getMessage() . ' ' . $details . '</div>';
        }

        return $mb->debug();
    }
}
