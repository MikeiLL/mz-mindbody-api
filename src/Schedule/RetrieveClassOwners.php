<?php
/**
 * Retrieve Class Owners
 *
 * This file contains the class that extends Retrieve, specifically
 * for fetching a "guess" at the instructor for a given MBO schedule
 * event. It has to guess, based on a few weeks of schedule, who is
 * teaching the class most frequently.
 *
 * @package MzMindbody
 */

namespace MZoo\MzMindbody\Schedule;

use MZoo\MzMindbody as NS;
use MZoo\MzMindbody\Core as Core;
use MZoo\MzMindbody\Common as Common;
use MZoo\MzMindbody\Common\Interfaces as Interfaces;

/**
 * Retrieve Class Owners Class
 *
 * Methods for retrieving the person deduced to be the
 * "owner" of a repeating MBO scheduled class.
 */
class RetrieveClassOwners extends Interfaces\RetrieveClasses {

    /**
     * Return Time Frame for request to MBO API
     *
     * @since 2.4.7
     *
     * Default time_frame is two dates, 4 weeks previous to start of current week as set in WP, and four weeks from now.
     *
     * @param timestamp $timestamp Time at which to begin timeframe cycle.
     *
     * @return array or start and end dates as required for MBO API
     */
    public function time_frame( $timestamp = null ) {
        $start_time = new \Datetime( wp_date( 'Y-m-d', strtotime( wp_date( 'Y-m-d H:i:s' ) ) ) );
        $end_time   = new \Datetime( wp_date( 'Y-m-d', strtotime( wp_date( 'Y-m-d H:i:s' ) ) ) );
        $di         = new \DateInterval( 'P4W' );
        $end_time->add( $di );
        // Now let's go four weeks previous as well.
        $di->invert = 1;
        $start_time->add( $di );

        return array(
            'StartDateTime' => $start_time->format( 'Y-m-d' ),
            'EndDateTime'   => $end_time->format( 'Y-m-d' ),
        );
    }

    /**
     * Deduce Class Owners
     *
     * Wrapper function for populate_regularly_scheduled_classes accessed by admin via ajax.
     *
     * Uses the helpers->print() method to output the matrix of probably "class owners" which is then
     * logged to the console via javascript.
     */
    public function deduce_class_owners() {
        // Generated in Admin\Admin localize_script().
        check_admin_referer( 'mz_deduce_class_owners', 'nonce' );

        if ( false === $this->get_mbo_results() ) {
            echo '<div>' . __( 'Error deducing MBO class owners.', 'mz-mindbody-api' ) . '</div>';
        }

        ob_start();
        $result['type'] = 'success';

        $owners = $this->populate_regularly_scheduled_classes( 'message' );

        //NS\MZMBO()->helpers->print( $owners );

        $result['message'] = ob_get_clean();

        if ( ! empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) &&
            'xmlhttprequest' === strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) ) {
            $result = wp_json_encode( $result );
            echo $result;
        } else {
            header( 'Location: ' . $_SERVER['HTTP_REFERER'] );
        }

        wp_die();
    }

    /**
     * Populate Matrix of Regularly Scheduled Classes
     *
     * This generates an array, saved as a transient, used when a Class Instructor is "Substitute" to see who the regular teacher is. It's an
     * ugly approach to determining which classes are unique and assigned to a particular instructor.
     *
     * The array is a matrix of these:
     * [59382] => Array
     *      (
     *      [class_name] => GX | Barre
     *      [class_description] => <div><strong>Born of the original Lotte
     *      [class_owner] => Darice Balamotti
     *      [class_owner_id] => 117
     *      [image_url] => https://clients.mindbodyonline.com/studios/Intensity/reservations/5.jpg
     *      [time] => 16:16
     *      [location] => 1
     *      [day] => Array
     *          (
     *          )
     * )
     *
     * @param string $message Whether or not to echo a message and return the resulting array.
     * @return void|string $message Message if requested by parameter.
     */
    public function populate_regularly_scheduled_classes( $message = 'no message' ) {
        $class_owners = array();
        $class_count  = 0;

        if ( ! isset( $this->classes ) ) :
            // No classes so populate the classes array.
            $this->get_mbo_results();
        endif;

        $schedules = $this->classes;

        foreach ( $schedules as $class ) :
            $class_count++;

            // If already a substitute, continue.
            if ( 1 === (int) $class['Substitute'] ) {
                continue;
            }

            $day_of_class = wp_date( 'l', $class['StartDateTime'] );

            $class_image = isset( $class['ClassDescription']['ImageURL'] ) ? $class['ClassDescription']['ImageURL'] : '';

            $image_path_array = explode( '?imageversion=', $class_image );

            $class_description_substring = substr( strip_tags( $class['ClassDescription']['Description'] ), 0, 55 );

            $staff_name = isset( $class['Staff']['Name'] ) ? $class['Staff']['Name'] : $class['Staff']['FirstName'] . ' ' . $class['Staff']['LastName'];

            $temp = array(
                'class_name'        => strip_tags( $class['ClassDescription']['Name'] ),
                'class_description' => $class_description_substring,
                'image_url'         => array_shift( $image_path_array ),
                'time'              => wp_date( 'g:ia', $class['StartDateTime'] ),
                'location'          => $class['Location']['Id'],
                'day'               => $day_of_class,
                'class_owner'       => strip_tags( $staff_name ),
                'class_owner_id'    => strip_tags( $class['Staff']['Id'] ),
            );

            // Loop through the entire array, and sub-array and if there's already a match, do nothing.
            foreach ( $class_owners as $owner ) :
                $diff = array_diff( $owner, $temp );
                if ( count( $diff ) === 0 ) :
                    // There's already one like this.
                    continue 2; // Jump out of first OUTER loop (2).
                endif;

                /*
                 * DEBUG: Check to see if this is a class which matches in all but staff.
                 * $keys = array_keys($diff);
                 * if ( $keys[0] ==  'class_owner' && $keys[1] ==  'class_owner_id'):
                 * return array($owner, $temp);
                 *  endif;
                 */
            endforeach;

            // If there isn't already a match add this item to the array.
            $class_owners[] = $temp;
        endforeach;

        delete_transient( 'mz_class_owners' );
        set_transient( 'mz_class_owners', $class_owners, 60 * 60 * 24 * 7 );

        if ( 'message' === $message ) :
            return $class_owners;
        endif;
    }

    /**
     * Find Class Owner
     *
     * Compare Class against each "owner" in $class_owners and return first match.
     *
     * @param array $class MBO generated array of class details.
     * @return false|array of MBO instructor details.
     */
    public function find_class_owner( $class ) {

        // Create an object that will be compared against the $class_owners matrix.

        $day_of_class = wp_date( 'l', $class['StartDateTime'] );

        $class_image = isset( $class['ClassDescription']['ImageURL'] ) ? $class['ClassDescription']['ImageURL'] : '';

        $image_path_array = explode( '?imageversion=', $class_image );

        $class_description_substring = substr( strip_tags( $class['ClassDescription']['Description'] ), 0, 55 );

        $temp = array(
            'class_name'        => strip_tags( $class['ClassDescription']['Name'] ),
            'class_description' => $class_description_substring,
            'image_url'         => array_shift( $image_path_array ),
            'time'              => wp_date( 'g:ia', $class['StartDateTime'] ),
            'location'          => $class['Location']['Id'],
            'day'               => $day_of_class,
            'class_owner'       => strip_tags( $class['Staff']['Name'] ),
            'class_owner_id'    => strip_tags( $class['Staff']['Id'] ),
        );

        // Fetch the Class_Owners transient and loop through it 'till we find a match.
        // First make sure we have the "class_owners" and that it's not an empty set.
        $class_owners = get_transient( 'mz_class_owners' );
        if ( false !== $class_owners ) :
            if ( count( $class_owners ) === 0 ) :
                // Class_owners were empty, so re-generate now.
                $this->populate_regularly_scheduled_classes();
                // And run this again.
                return $this->find_class_owner( $class );
            endif;
            foreach ( $class_owners as $owner ) :
                // If the only difference is the instructor, we have a match.
                $diff = array_diff( $owner, $temp );

                /*
                * Check to see if this is a class which matches in all but staff.
                * This works because class_owner and class_owner_id are the last
                * two items in the $owner array. So if they are first in $diff, we know
                * all the other keys matched.
                */
                $keys = array_keys( $diff );
                if ( 'class_owner' === $keys[0] && 'class_owner_id' === $keys[1] ) :
                    return $owner;
                endif;
            endforeach;
        else :
            // Couldn't fetch the transient, so generate it now.
            $this->populate_regularly_scheduled_classes();
            // And run this again.
            return $this->find_class_owner( $class );
        endif;

        // If we didn't find a likely "owner" return false.
        return false;
    }
}
