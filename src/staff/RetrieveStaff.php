<?php

namespace MzMindbody\Inc\Staff;

use MzMindbody as NS;
use MzMindbody\Inc\Core as Core;
use MzMindbody\Inc\Libraries as Libraries;
use MzMindbody\Inc\Schedule as Schedule;
use MzMindbody\Inc\Common\Interfaces as Interfaces;

/**
 * Class that is extended for Staff Display Shortcode(s)
 */
class RetrieveStaff extends Interfaces\Retrieve
{



    /**
     * Holder for staff array returned by MBO API
     *
     * @since    2.4.7
     * @access   public
     * @var      array $staff Array of staff returned from MBO API
     */
    public $staff_result;

    public function __construct($atts = array(
                                'locations' => array(1)
                                ))
    {

        parent::__construct();
        $this->atts = $atts;
        if (!empty(Core\MzMindbody_Api::$basic_options['mz_mindbody_siteID'])) :
            $this->mbo_account = !empty($atts['account']) ? $atts['account'] : Core\MzMindbody_Api::$basic_options['mz_mindbody_siteID'];
        else :
            $this->mbo_account = '-99';
        endif;
    }

    /**
     * Return data from MBO api, store it in a transient and
     * as object attribute.
     *
     * @since 2.4.7
     *
     * @param @staffIDs array of Staff IDs to return info for
     *
     *
     * @return $this->staff_result object holding staff details
     */
    public function getMboResults($staffIDs = array())
    {

        $mb = $this->instantiateMboApi();
        if (!$mb) {
            return false;
        }

        // All staff members?
        $all = (0 == count($staffIDs));
// Make specific transient for specific staff members
        $transient_string = ($all) ? 'staff' : 'staff' . implode('_', $staffIDs);
        $transient_string = $this->generate_transient_name($transient_string);
        if (false === get_transient($transient_string)) {
        // If there's not a transient already, call the API and create one

            if ($this->mbo_account !== 0) {
// If account has been specified in shortcode, update credentials
                $mb->sourceCredentials['SiteIDs'][0] = $this->mbo_account;
            }

            if ($all) {
                $this->staff_result = $mb->GetStaff();
            } else {
                $this->staff_result = $mb->GetStaff(array('StaffIDs' => $staffIDs ));
            }

            if (!empty($this->staff_result['StaffMembers'])) {
                set_transient($transient_string, serialize($this->staff_result['StaffMembers']), 60 * 60 * 12);
                $this->staff_result = $this->staff_result['StaffMembers'];
            }
        } else {
            $this->staff_result = unserialize(get_transient($transient_string));
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
     * @param $atts array of shorcode atts from calling function//
     *
     * @return array of MBO staff members, sorted by SortOrder, then LastName
     */
    public function sort_staff_by_sort_order($atts = array())
    {

        $count = 0;
// Obtain a list of columns
        foreach ($this->staff_result as $key => $row) {
// Remove any Staff members that are in the hide shortcode attribute
            if (!empty($atts['hide']) && (in_array(strtolower($row['Name']), array_map('strtolower', $atts['hide'])))) {
                unset($this->staff_result[$count]);
                $count++;
                continue;
            }
            // Remove staff members without image unless set to display them in shortcode
            if (!(isset($atts['include_imageless']) && ($atts['include_imageless'] != 0)) && (empty($this->staff_result[$count]['ImageUrl']))) {
                unset($this->staff_result[$count]);
                $count++;
                continue;
            }

            // Populate two arrays to use in array_multisort
            $important[$key] = $row['SortOrder'];
            $basic[$key] = $row['LastName'];
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
        return $this->get_staff_member_objects($atts);
    }

    /**
     * Generate an array of Staff Member objects from array of Staff Members
     */
    private function get_staff_member_objects($atts = array())
    {

        $staff_listing = $this->staff_result;
        return array_map(function ($item) use ($atts) {

            return new StaffMember($item, $atts);
        }, $staff_listing);
    }
}
