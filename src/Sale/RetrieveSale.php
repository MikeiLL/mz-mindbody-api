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
	 * Get API version, create API Interface Object
	 *
	 * @since 1.0.0
	 *
	 * @param int $api_version in case call on API v5 for client features.
	 *
	 * @return array of MBO schedule data
	 */
	public function get_mbo_results( $api_version = 6 ) {

		if ( 6 === $api_version ) {
			$this->mb = $this->instantiate_mbo_api();
		} else {
			$this->mb = $this->instantiate_mbo_api( 5 );
		}

		if ( ! $this->mb || 'NO_API_SERVICE' === $this->mb ) {
			return false;
		}

		return true;
	}

	/**
	 * Get Contracts from primary location
	 *
	 * @since 1.0.0
	 *
	 * @param boolean $dict true returns a an array of id=>name values.
	 * @return MBO request GET request result
	 */
	public function get_contracts( $dict = false ) {

		$location_id = 1;

		if ( false === get_transient( 'mz_contracts_from_mbo' ) ) {

			$this->get_mbo_results();

			if ( ! is_object( $this->mb ) ) {
				return 'Check your MBO connection.';
			}

			$request_body = array(
				'LocationId' => $location_id,
			);

			try {
				$result = $this->mb->GetContracts( $request_body );
			} catch ( \Exception $e ) {
				return false;
			}

			if ( array_key_exists( 'Contracts', $result ) && ! empty( $result['Contracts'] ) ) {
				set_transient( 'mz_contracts_from_mbo', $result, 86400 );
			}
			
		} else {
		
			$result = get_transient( 'mz_contracts_from_mbo' );
		}

		if ( true === $dict ) {
			$dict_array = array();
			foreach ( $result['Contracts'] as $element ) {
				$dict_array[ $element['Id'] ] = $element['MembershipName'];
			}
			return $dict_array;
		}

		return $result;
	}

	/**
	 * Get Contracts from primary location
	 *
	 * @since 1.0.0
	 *
	 * @param boolean $dict true returns a an array of id=>name values.
	 * @return MBO request GET request result
	 */
	public function get_services( $dict = false ) {

		$location_id = 1;

		if ( false === get_transient( 'mz_services_from_mbo' ) ) {

			$this->get_mbo_results();

			if ( ! is_object( $this->mb ) ) {
				return 'Check your MBO connection.';
			}

			$request_body = array(
				'LocationId' => $location_id,
			);

			try {
				$result = $this->mb->GetServices( $request_body );
			} catch ( \Exception $e ) {
				return false;
			}

			if ( array_key_exists( 'Contracts', $result ) && ! empty( $result['Contracts'] ) ) {
				set_transient( 'mz_services_from_mbo', $result, 86400 );
			}
		} else {

			$result = get_transient( 'mz_services_from_mbo' );

			if ( true === $dict ) {
				$dict_array = array();
				foreach ( $result['Memberships'] as $element ) {
					if ( false === $element['IsActive'] ) {
						continue;
					}
					$dict_array[ $element['MembershipId'] ] = $element['MembershipName'];
				}
				return $dict_array;
			}
		}

		return $result;
	}
}
