<?php
namespace MZ_Mindbody\Inc\Staff;

use MZ_Mindbody as NS;
use MZ_Mindbody\Inc\Core as Core;
use MZ_Mindbody\Inc\Libraries as Libraries;
use MZ_Mindbody\Inc\Schedule as Schedule;
use MZ_Mindbody\Inc\Common\Interfaces as Interfaces;

/**
 * Class that is extended for Staff Display Shortcode(s)
 */
class Retrieve_Staff extends Interfaces\Retrieve {

    /**
     * Holder for staff array returned by MBO API
     *
     * @since    2.4.7
     * @access   public
     * @var      array $staff Array of staff returned from MBO API
     */
    public $staff_result;

    /**
     * Return data from MBO api, store it in a transient and
     * as object attribute.
     *
     * @since 2.4.7
     *
     * @param @staffIDs array of Staff IDs to return info for
     *
     *
     * @return array of MBO Staff data
     */
    public function get_mbo_results( $staffIDs = array() ){

        $mb = $this->instantiate_mbo_API();

        if ( !$mb || $mb == 'NO_SOAP_SERVICE' ) return false;

        // All staff members?
        $all = (0 == count($staffIDs));

        // Make specific transient for specific staff members
        $transient_string = ($all) ? 'staff' : 'staff' . implode('_', $staffIDs );

        $transient_string = $this->generate_transient_name($transient_string);

        if ( false === get_transient( $transient_string ) ) {
            // If there's not a transient already, call the API and create one

            if ($this->mbo_account !== 0) {
                // If account has been specified in shortcode, update credentials
                $mb->sourceCredentials['SiteIDs'][0] = $this->mbo_account;
            }

            if ($all) {
                $this->staff_result = $mb->GetStaff();
            } else {
                $this->staff_result = $mb->GetStaff( array('StaffIDs'=> $staffIDs ));
            }

            if (!empty($this->staff_result['GetStaffResult']['StaffMembers']['Staff'])) {
                set_transient($transient_string, serialize($this->staff_result), 60 * 60 * 12);
            }

        } else {
            $this->staff_result = unserialize(get_transient( $transient_string ));
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
     * @param $atts array of shorcode atts from calling function
     *
     * @return array of MBO staff members, sorted by SortOrder, then LastName
     */
    public function sort_staff_by_sort_order($atts = array()){

        $count = 0;
        // Obtain a list of columns
        foreach ($this->staff_result['GetStaffResult']['StaffMembers']['Staff'] as $key => $row) {
            // Remove any Staff members that are in the hide shortcode attribute
            if (in_array(strtolower($row['Name']), array_map('strtolower', $atts['hide']))) {
                unset($this->staff_result['GetStaffResult']['StaffMembers']['Staff'][$count]);
                continue;
            }
            $important[$key]  = $row['SortOrder'];
            $basic[$key] = $row['LastName'];
            $count++;
        }

        array_multisort($important, SORT_NUMERIC, SORT_ASC,
            $basic, SORT_REGULAR, SORT_ASC,
            $this->staff_result['GetStaffResult']['StaffMembers']['Staff']);

        return $this->get_staff_member_objects($atts);
    }

    /**
     * Generate a list of Staff Member objects from array of Staff Members
     */
    private function get_staff_member_objects($atts = array()){
        return array_map( function($item) {
            return new Staff_Member($item, $atts);
        }, $this->staff_result['GetStaffResult']['StaffMembers']['Staff']);
    }

}
