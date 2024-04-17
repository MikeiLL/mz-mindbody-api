<?php
/**
 * This file contains the class which withe methods to retrieve client contracts
 *
 * Methods to retrieve and manipulate user MBO contracts.
 *
 * @package MzMboOnDemand
 */

namespace MZoo\MzMindbody\Sale;

use MZoo\MzMindbody as NS;
use MZoo\MzMindbody\Common\Interfaces as Interfaces;

/**
 * Class with methods to interface with MBO Sale endpoint
 */
class RetrieveSale extends Interfaces\Retrieve {


    /**
     * The Mindbody API Object
     *
     * @access private
     * @var object $mb our mindbody API class.
     */
    private $mb;

    /**
     * Locations defined by settings
     * @access private
     * @var array location ids
     */
    private $locations;

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
     * Initialize.
     */
    public function __construct() {
        $options = get_option( 'mz_mbo_on_demand' );
        $this->short_timeout = isset( $options['mbo_on_demand_short_timeout'] ) ? $options['mbo_on_demand_short_timeout'] : 3600;
        $this->long_timeout = isset( $options['mbo_on_demand_long_timeout'] ) ? $options['mbo_on_demand_long_timeout'] : 86400;
        if ( ! isset( $options['mbo_site_location_id'] ) ) {
            $this->locations = array( 1 );
        } else if ( ! is_array( $options['mbo_site_location_id'] ) ) {
            $this->locations = array( $options['mbo_site_location_id'] );
        } else {
            $this->locations = array_keys( $options['mbo_site_location_id'] );
        }
    }

    /**
     * Get API version, create API Interface Object
     *
     * @since 1.0.0
     *
     * @param int $api_version in case call on API v5 for client features.
     *
     * @return array of MBO schedule data
     */
    public function get_mbo_results( $api_version = 6 ) {

        $this->mb = $this->instantiate_mbo_api();

        // TODO: May not need NO_API_SERVICE fallback since
        // removal of api v5
        if ( ! $this->mb || 'NO_API_SERVICE' === $this->mb ) {
            return false;
        }

        return true;
    }

    /**
     * Get Locations
     *
     * @since 2.8.8
     *
     * @param boolean $dict true returns a an array of id=>name values.
     * @return MBO request GET request result
     */
    public function get_locations( $dict = false ) {

        if ( false === $result = get_transient( 'mz_mbo_locations' ) ) {

            $this->get_mbo_results();

            if ( ! is_object( $this->mb ) ) {
                return 'Check your MBO connection.';
            }

            try {
                $result = $this->mb->GetLocations();
            } catch ( \Exception $e ) {
                $result = array();
            }

            set_transient( 'mz_mbo_locations', $result, array_key_exists( 'Locations', $result ) ? $this->long_timeout : $this->short_timeout );
        }

        if ( true !== $dict ) {
            return $result;
        }

        $dict_array = array();

        if ( array_key_exists( 'Locations', $result ) ) {
            foreach ( $result['Locations'] as $element ) {
                $dict_array[ $element['Id'] ] = $element['Name'];
            }
        }

        return $dict_array;

    }

    /**
     * Get Sites Available on this API Account
     *
     * @since 2.8.8
     *
     * @return MBO request GET request result
     */
    public function get_sites( $dict = false ) {

        if ( false === $result = get_transient( 'mz_mbo_sites' ) ) {

            $this->get_mbo_results();

            if ( ! is_object( $this->mb ) ) {
                return 'Check your MBO connection.';
            }

            try {
                $result = $this->mb->GetSites();
            } catch ( \Exception $e ) {
                $result = array();
            }

            set_transient( 'mz_mbo_sites', $result, array_key_exists( 'Sites', $result ) ? $this->long_timeout : $this->short_timeout );
        }


        if ( true !== $dict ) {
            return $result;
        }

        $dict_array = array();

        if ( array_key_exists( 'Sites', $result ) ) {
            foreach ( $result['Sites'] as $element ) {
                $dict_array[ $element['Id'] ] = $element['Name'];
            }
        }

        return $dict_array;

    }

    /**
     * Get Contracts
     *
     * @since 2.8.8
     *
     * @param boolean $dict true returns a an array of id=>name values.
     * @return MBO request GET request result
     */
    public function get_contracts( $dict = false ) {

        if ( false === $results = get_transient( 'mz_mbo_contracts' ) ) {

            $this->get_mbo_results();

            if ( ! is_object( $this->mb ) ) {
                return 'Check your MBO connection.';
            }

            $results = array();

            foreach ( $this->locations as $location_id ) {

                $request_body = array(
                    'LocationId' => $location_id,
                );

                try {
                    $result = $this->mb->GetContracts( $request_body );
                } catch ( \Exception $e ) {
                    $result = array();
                }

                if ( array_key_exists( 'Contracts', $result ) ) {
                    foreach ( $result['Contracts'] as $element ) {
                        if ( ! array_key_exists( $element['Id'], $results ) ) {
                            $results[ $element['Id'] ] = $element;
                        }
                    }
                }

            }

            set_transient( 'mz_mbo_contracts', $results, isset( $results ) ? $this->long_timeout : $this->short_timeout );

        }

        if ( true !== $dict ) {

            return $results;

        }

        $dict_array = array();

        foreach ( $results as $element ) {
            $dict_array[ $element['Id'] ] = $element['Name'];
        }

        return $dict_array;

    }

    /**
     * Get Services
     *
     * @since 2.8.8
     *
     * @param boolean $dict true returns a an array of id=>name values.
     * @return MBO request GET request result
     *
     * Note: Note that the location id argument does not filter results
     * to only services provided at the given location.
     */
    public function get_services( $dict = false ) {

        if ( false === $results = get_transient( 'mz_mbo_services' ) ) {

            $this->get_mbo_results();

            if ( ! is_object( $this->mb ) ) {
                return 'Check your MBO connection.';
            }

            $results = array();

            foreach( $this->locations as $location_id ) {
                $request_body = array(
                    'LocationId' => $location_id,
                );

                try {
                    $result = $this->mb->GetServices( $request_body );
                } catch ( \Exception $e ) {
                    $result = array();
                }

                if ( array_key_exists( 'Services', $result ) ) {
                    foreach ( $result['Services'] as $element ) {
                        if ( ! array_key_exists( $element['Id'], $results ) ) {
                            $results[ $element['Id'] ] = $element;
                        }
                    }
                }

            }

            set_transient( 'mz_mbo_services', $results, array_key_exists( 'Services', $results ) ? $this->long_timeout : $this->short_timeout );

        }

        if ( true !== $dict ) {
            return $results;
        }

        $dict_array = array();

        foreach ( $results as $element ) {
            $dict_array[ $element['Id'] ] = $element['Name'];
        }

        return $dict_array;

    }
}
