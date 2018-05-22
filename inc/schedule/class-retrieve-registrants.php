<?php

namespace MZ_Mindbody\Inc\Schedule;

use MZ_Mindbody\Inc\Core as Core;
use MZ_Mindbody\Inc\Libraries as Libraries;
use MZ_Mindbody\Inc\Schedule as Schedule;
use MZ_Mindbody\Inc\Common\Interfaces as Interfaces;

/*
 * Class that is extended for Schedule Display Ajax Retrieve Registrants (s)
 *
 */

class Retrieve_Registrants extends Interfaces\Retrieve
{


    /**
     * Holds the registrants for a given class.
     *
     * @since    2.4.7
     * @access   public
     * @var      array $registrants Array of names of class registrants.
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
    public function get_mbo_results($classid = 0)
    {

        if (empty($classid)) return false;

        $mb = $this->instantiate_mbo_API();

        if (!$mb || $mb == 'NO_SOAP_SERVICE') return false;

        if ($this->mbo_account !== 0) {
            // If account has been specified in shortcode, update credentials
            $mb->sourceCredentials['SiteIDs'][0] = $this->mbo_account;
        }

        $this->registrants = $mb->GetClassVisits(array('ClassID' => $classid));

        return $this->registrants;
    }

    /**
     * Get Registrants called via Ajax
     *
     *
     */
    function get_registrants()
    {

        check_ajax_referer($_REQUEST['nonce'], "mz_MBO_get_registrants_nonce", false);

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
                foreach ($class_visits['GetClassVisitsResult']['Class']['Visits'] as $registrants) {
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

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $result = json_encode($result);
            echo $result;
        } else {
            header("Location: " . $_SERVER["HTTP_REFERER"]);
        }

        die();
    }
    //End Ajax Get Registrants


}
