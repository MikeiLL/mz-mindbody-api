<?php
/**
 * Retrieve Staff
 *
 * This file contains the class that extends Retrieve, specifically
 * for fetching Mindbody staff members.
 *
 * @package MzMindbody
 */

namespace MZoo\MzMindbody\Staff;

use MZoo\MzMindbody as NS;
use MZoo\MzMindbody\Core as Core;
use MZoo\MzMindbody\Libraries as Libraries;
use MZoo\MzMindbody\Schedule as Schedule;
use MZoo\MzMindbody\Common\Interfaces as Interfaces;

/**
 * Class that is extended for Staff Display Shortcode(s)
 */
class RetrieveStaff extends Interfaces\Retrieve {

    /**
     * Holder for staff array returned by MBO API
     *
     * @since  2.4.7
     * @access public
     * @var    array $staff_result Staff returned from MBO API
     */
    public $staff_result;

    /**
     * Constructor
     *
     * Default $atts is locations array with location id #1.
     *
     * @param array $atts Shortcode attributes.
     */
    public function __construct( $atts = array( 'locations' => array( 1 ) ) ) {

        parent::__construct();

        $this->atts = $atts;

        // Initialize to sandbox account.
        $this->mbo_account = '-99';

        if ( ! empty( Core\MzMindbodyApi::$basic_options['mz_mindbody_siteID'] ) ) :
            // Default to admin configured site ID.
            $this->mbo_account = Core\MzMindbodyApi::$basic_options['mz_mindbody_siteID'];
            // Unless user has overridden it in shortcode.
            if ( ! empty( $atts['account'] ) ) {
                $this->mbo_account = $atts['account'];
            }
        endif;
    }

    /**
     * Return data from MBO api, store it in a transient and
     * as object attribute.
     *
     * @since 2.4.7
     *
     * @param array $staff_ids Staff IDs to return info for.
     *
     * @return $this->staff_result object holding staff details.
     */
    public function get_mbo_results( $staff_ids = array() ) {

        $mb = $this->instantiate_mbo_api();
        if ( ! $mb ) {
            return false;
        }

        // All staff members, I think.
        $all = ( 0 === count( $staff_ids ) );
        // Make specific transient for specific staff members.
        $transient_string = ( $all ) ? 'staff' : 'staff' . implode( '_', $staff_ids );
        $transient_string = $this->generate_transient_name( $transient_string );
        if ( false === get_transient( $transient_string ) ) {
            // If there's not a transient already, call the API and create one.

            if ( 0 !== $this->mbo_account ) {
                // If account has been specified in shortcode, update credentials.
                $mb->source_credentials['SiteIDs'][0] = $this->mbo_account;
            }

            if ( $all ) {
                $this->staff_result = $mb->GetStaff();
            } else {
                $this->staff_result = $mb->GetStaff( array( 'StaffIDs' => $staff_ids ) );
            }

            if ( ! empty( $this->staff_result['StaffMembers'] ) ) {
                set_transient( $transient_string, serialize( $this->staff_result['StaffMembers'] ), 60 * 60 * 12 );
                $this->staff_result = $this->staff_result['StaffMembers'];
            }
        } else {
            $this->staff_result = unserialize( get_transient( $transient_string ) );
        }

        return $this->staff_result;
    }
    /**
     * Sort Staff array by MBO SortOrder, then by LastName
     *
     * @since 2.4.7
     * source: https://stackoverflow.com/a/2282247/2223106
     *
     * First populate two arrays: $important and $basic, then sort the entire array
     * returned by MBO based on those criteria.
     *
     * @param array $atts Shorcode atts from calling function.
     *
     * @return array of MBO staff members, sorted by SortOrder, then LastName.
     */
    public function sort_staff_by_sort_order( $atts = array() ) {

        $count = 0;
        // Obtain a list of columns.
        foreach ( $this->staff_result as $key => $row ) {
            // Remove any Staff members that are in the hide shortcode attribute.
            if ( ! empty( $atts['hide'] )
                && ( in_array( strtolower( $row['Name'] ), array_map( 'strtolower', $atts['hide'] ), true ) )
            ) {
                unset( $this->staff_result[ $count ] );
                $count++;
                continue;
            }
            // Remove staff members without image unless set to display them in shortcode.
            if ( ! ( isset( $atts['include_imageless'] ) && ( 0 !== (int) $atts['include_imageless'] ) )
                && ( empty( $this->staff_result[ $count ]['ImageUrl'] ) )
            ) {
                unset( $this->staff_result[ $count ] );
                $count++;
                continue;
            }

            // Populate two arrays to use in array_multisort.
            $important[ $key ] = $row['SortOrder'];
            $basic[ $key ]     = $row['LastName'];
            $count++;
        }

        array_multisort(
            $important,
            SORT_NUMERIC,
            SORT_ASC,
            $basic,
            SORT_REGULAR,
            SORT_ASC,
            $this->staff_result
        );
        return $this->get_staff_member_objects( $atts );
    }

    /**
     * Generate an array of Staff Member objects from array of Staff Members
     *
     * @param array $atts Shorcode atts from calling function.
     * @return object MZoo\MzMindbody\Staff\StaffMember().
     */
    private function get_staff_member_objects( $atts = array() ) {

        $staff_listing = $this->staff_result;
        return array_map(
            function ( $item ) use ( $atts ) {

                return new StaffMember( $item, $atts );
            },
            $staff_listing
        );
    }
}
