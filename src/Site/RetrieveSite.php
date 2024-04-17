<?php
/**
 * Retrieve Site
 *
 * This file contains the class that extends Retrieve, specifically
 * for fetching Mindbody Site (specific location) info.
 *
 * @package MzMindbody
 */

namespace MZoo\MzMindbody\Site;

use MZoo\MzMindbody\Core as Core;
use MZoo\MzMindbody\Common as Common;
use MZoo\MzMindbody\Libraries as Libraries;
use MZoo\MzMindbody\Schedule as Schedule;
use MZoo\MzMindbody\Common\Interfaces as Interfaces;
use MZoo\MzMindbody as NS;

/**
 * Class that holds Site Interface Methods
 *
 * Class that extends Retrieve, specifically
 * for fetching Mindbody Site (specific location)
 * info.
 */
class RetrieveSite extends Interfaces\Retrieve {



    /**
     * The Mindbody API Object
     *
     * @access private
     * @var object $mb MZoo\MzMindbody\Libraries\MboV6Api.
     */
    private $mb;

    /**
     * Format for date display, specific to MBO API Plugin.
     *
     * @since  2.5.9
     * @access public
     * @var    string $date_format WP date format option.
     */
    public $date_format;

    /**
     * Format for time display, specific to MBO API Plugin.
     *
     * @since  2.5.9
     * @access public
     * @var    string $time_format
     */
    public $time_format;

    /**
     * Short transient timer
     * @access private
     * @var integer timer in seconds
     */
    private $short_timeout;

    /**
     * Long transient timer
     * @access private
     * @var integer in seconds
     */
    private $long_timeout;

    /**
     * Class constructor
     *
     * @since 2.5.9
     */
    public function __construct() {
        $this->date_format = Core\MzMindbodyApi::$date_format;
        $this->time_format = Core\MzMindbodyApi::$time_format;
        $options = get_option( 'mz_mbo_on_demand' );
        $this->short_timeout = isset( $options['mbo_on_demand_short_timeout'] ) ? $options['mbo_on_demand_short_timeout'] : 3600;
        $this->long_timeout = isset( $options['mbo_on_demand_long_timeout'] ) ? $options['mbo_on_demand_long_timeout'] : 86400;
    }

    /**
     * Get All Site Memberships
     *
     * @since 2.5.9
     * @param boolean $dict true returns a an array of id=>name values.
     */
    public function get_site_memberships( $dict = false ) {

        if ( false === $result = get_transient( 'mz_mbo_memberships' ) ) {

            $this->get_mbo_results();

            if ( ! is_object( $this->mb ) ) {
                return 'Check your MBO connection.';
            }

            try {
                $result = $this->mb->GetMemberships();
            } catch ( \Exception $e ) {
                $result = array();
            }

            set_transient( 'mz_mbo_memberships', $result, array_key_exists( 'Memberships', $result ) ? $this->long_timeout : $this->short_timeout );

        }

        if ( true !== $dict ) {
            return $result;
        }

        $dict_array = array();
        if ( array_key_exists( 'Memberships', $result ) ) {
            foreach ( $result['Memberships'] as $element ) {
                if ( false === $element['IsActive'] ) {
                    continue;
                }
                $dict_array[ $element['MembershipId'] ] = $element['MembershipName'];
            }
        }

        return $dict_array;

    }

    /**
     * Get All Site Programs
     *
     * Each Site Program item looks like:
     *              [Id] => 1
     *              [Name] => Personal Training
     *              [ScheduleType] => Appointment|Enrollment|Arrival|Enrollment|Class
     *              [CancelOffset] => 1440
     *              [ContentFormats] => Array
     *                  (
     *                      [0] => InPerson
     *                  )
     *
     * @since 2.5.9
     * @param boolean $dict true returns a an array of id=>name values.
     * @return array dict or full array from MBO.
     */
    public function get_site_programs( $dict = false ) {

        if ( false === $result = get_transient( 'mz_mbo_programs' ) ) {

            $this->get_mbo_results();

            if ( ! is_object( $this->mb ) ) {
                return 'Check your MBO connection.';
            }

            $request_body = array();
            try {
                $result = $this->mb->GetPrograms();
            } catch ( \Exception $e ) {
                return array();
            }
            set_transient( 'mz_mbo_programs', $result, array_key_exists( 'Programs', $result ) ? $this->long_timeout : $this->short_timeout );

        }

        if ( true !== $dict ) {
            return $result;
        }

        $dict_array = array();
        foreach ( $result['Programs'] as $element ) {
            $dict_array[ $element['Id'] ] = $element['Name'];
        }
        return $dict_array;

    }

    /**
     * Get All Site Resources
     *
     * Resources are available rooms, studios, courts, etc.
     * [Resources] => Array
     *    (
     *        [0] => Array
     *            (
     *                [Id] => 1
     *                [Name] => Fitness Studio
     *            )
     *
     * @since 2.5.9
     * @param boolean $dict true returns a an array of id=>name values.
     * @return array of "resources" from MBO.
     */
    public function get_site_resources( $dict = false ) {

        if ( false === $result = get_transient( 'mz_resources_from_mbo' ) ) {

            $this->get_mbo_results();

            if ( ! is_object( $this->mb ) ) {
                return 'Check your MBO connection.';
            }

            try {
                $result = $this->mb->GetResources();
            } catch ( \Exception $e ) {
                return array();
            }
            set_transient( 'mz_resources_from_mbo', $result, array_key_exists( 'Resources', $result ) ? $this->long_timeout : $this->short_timeout );

        }

        if ( true !== $dict ) {
            return $result;
        }

        $dict_array = array();
        foreach ( $result['Resources'] as $element ) {
            $dict_array[ $element['Id'] ] = $element['Name'];
        }
        return $dict_array;
    }

    /**
     * Create API Interface Object
     *
     * @since 2.5.9
     *
     * @return array of MBO schedule data
     */
    public function get_mbo_results() {

        $this->mb = $this->instantiate_mbo_api();

        if ( ! $this->mb || 'NO_API_SERVICE' === $this->mb ) {
            return false;
        }

        return true;
    }
}
