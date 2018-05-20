<?php
namespace MZ_Mindbody\Inc\Schedule;

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
class Retrieve_Registrants extends Interfaces\Retrieve {


    /**
     * Holds the registrants for a given class.
     *
     * @since    2.4.7
     * @access   public
     * @var      array    $registrants    Array of names of class registrants.
     */
     public $registrants;


    /**
     * Get a timestamp, return data from MBO api, store it in a transient and
     * as object attribute.
     *
     * @since 2.4.7
     *
     * @param @timestamp defaults to current time
     *
     *
     * @return array of MBO schedule data
     */
    public function get_mbo_results($classid = 0){

		if ( empty( $classid ) ) return false;
		
        $mb = $this->instantiate_mbo_API();

        if ( !$mb || $mb == 'NO_SOAP_SERVICE' ) return false;

            if ($this->mbo_account !== 0) {
                // If account has been specified in shortcode, update credentials
                $mb->sourceCredentials['SiteIDs'][0] = $this->mbo_account;
            }

            $this->registrants = $mb->GetClassVisits( array( 'ClassID' => $classid ) );

        return $this->registrants;
    }

    /**
     * Get Registrants called via Ajax
     *
     *
     */

    function get_registrants() {

        check_ajax_referer( $_REQUEST['nonce'], "mz_schedule_display_nonce", false);

        $classid = $_REQUEST['classID'];
        
        $result['type'] = "success";
        $result['message'] = $classid;
        // ob_start();
        $class_visits = $this->get_mbo_results($classid);
        // var_dump($class_visits);
        if ($class_visits['GetClassVisitsResult']['Status'] != 'Success'):
            $result['type'] = "error";
            $result['message'] = __("Unable to retrieve registrants.", 'mz-mindbody-api');
        else:
            if (empty($class_visits['GetClassVisitsResult']['Class']['Visits'])) :
                $result['type'] = "success";
                $result['message'] = __("No registrants yet.", 'mz-mindbody-api');
            //mZ_write_to_file($class_visits['GetClassVisitsResult']['Class']['Visits']);
            else:
                $result['message'] = array();
                $result['type'] = "success";
                foreach($class_visits['GetClassVisitsResult']['Class']['Visits'] as $registrants) {
                    if (!isset($registrants['Client']['FirstName'])):
                        foreach ($registrants as $key => $registrant) {
                            if (isset($registrant['Client'])):
                                $result['message'][] = $registrant['Client']['FirstName'] . '_'
                                    . substr($registrant['Client']['LastName'], 0, 1);
                            endif;
                        }
                    else:
                        $result['message'][] = $registrants['Client']['FirstName'] . '_'
                            . substr($registrants['Client']['LastName'], 0, 1);
                    endif;
                }
            endif;
        endif;
		// $result['message'] = ob_get_clean();
		
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $result = json_encode($result);
            echo $result;
        }
        else {
            header("Location: ".$_SERVER["HTTP_REFERER"]);
        }

        die();
    }
    //End Ajax Get Registrants


}
