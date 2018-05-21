<?php
namespace MZ_Mindbody\Inc\Staff;

use MZ_Mindbody\Inc\Core as Core;
use MZ_Mindbody\Inc\Libraries as Libraries;
use MZ_Mindbody\Inc\Schedule as Schedule;
use MZ_Mindbody\Inc\Common\Interfaces as Interfaces;

/*
 * Class that is extended for Schedule Display Shortcode(s)
 *
 * @param @type string $time_format Format string for php strtotime function Default: "g:i a"
 * @param @type array OR numeric $locations Single or list of MBO location numerals Default: 1
 * @param @type boolean $hide_cancelled Whether or not to display cancelled classes. Default: 0
 * @param @type array $hide Items to be removed from calendar
 * @param @type boolean $advanced Whether or not allowing online class sign-up via plugin
 * @param @type boolean $show_registrants Whether or not to display class registrants in modal popup
 * @param @type boolean $registrants_count  Whether we want to show count of registrants in a class (TODO - finish) @default: 0
 * @param @type string $calendar_format Depending on final display, we may create items in Single_event class differently.
 *																			Default: 'horizontal'
 * @param @type boolean $delink Make class name NOT a link
 * @param @type string $class_type MBO API has 'Enrollment' and 'DropIn'. 'Enrolment' is a "workdhop". Default: 'Enrollment'
 * @param @type numeric $account Which MBO account is being interfaced with.
 * @param @type boolean $this_week If true, show only week from today.
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
     * @param @timestamp defaults to current time
     *
     *
     * @return array of MBO schedule data
     */
    public function get_mbo_results(){

        $mb = $this->instantiate_mbo_API();

        if ( !$mb || $mb == 'NO_SOAP_SERVICE' ) return false;

        $transient_string = $this->generate_transient_name('staff');

        if ( false === get_transient( $transient_string ) ) {
            // If there's not a transient already, call the API and create one

            if ($this->mbo_account !== 0) {
                // If account has been specified in shortcode, update credentials
                $mb->sourceCredentials['SiteIDs'][0] = $this->mbo_account;
            }

            $this->staff_result = $mb->GetStaff();

            set_transient($transient_string, $this->staff_result, 60 * 60 * 12);

        } else {
            $this->staff_result = get_transient( $transient_string );
        }
        return $this->staff_result;
    }

    /**
     * Sort Staff array by MBO SortOrder, then by LastName
     *
     * @since 2.4.7
     *
     * @param @timestamp defaults to current time
     * @source https://stackoverflow.com/a/2875637/2223106
     *
     *
     * @return array of MBO schedule data, sorted by SortOrder, then LastName
     */
    public function sort_staff_by_sort_order(){
        // usort($this->staff_result['GetStaffResult']['StaffMembers']['Staff'], function ($a, $b){
        //     if($a['SortOrder'] == $b['SortOrder']) {
        //         return 0;
        //     }
        //     return $a['SortOrder'] < $b['SortOrder'] ? -1 : 1;
        // });

        // Obtain a list of columns
        foreach ($this->staff_result['GetStaffResult']['StaffMembers']['Staff'] as $key => $row) {
            $important[$key]  = $row['SortOrder'];
            $basic[$key] = $row['LastName'];
        }

        array_multisort($important, SORT_NUMERIC, SORT_ASC,
            $basic, SORT_REGULAR, SORT_ASC,
            $this->staff_result['GetStaffResult']['StaffMembers']['Staff']);

        return $this->staff_result;
    }

}
