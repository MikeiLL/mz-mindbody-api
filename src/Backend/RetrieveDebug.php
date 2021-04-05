<?php
/**
 * Retrieve Debug
 *
 * This file contains the class with methods for
 * testing the API within the WordPress Admin.
 *
 * @package MzMindbody
 */

namespace MZoo\MzMindbody\Backend;

use MZoo\MzMindbody as NS;
use MZoo\MzMindbody\Common\Interfaces as Interfaces;

class RetrieveDebug extends Interfaces\Retrieve {





	public $property_test = 'thing';

	/**
	 * Return Time Frame for request to MBO API
	 *
	 * @since 2.4.7
	 *
	 * @throws \Exception
	 *
	 * Default timeFrame is two dates, start of current week as set in WP, and seven days from "now.
	 *
	 * @return array or start and end dates as required for MBO API
	 */
	public function timeFrame( $timestamp = null ) {
		$time = new \Datetime( date( 'Y-m-d', time() ) );
		return array(
			'StartDateTime' => $time->format( 'Y-m-d' ),
			'EndDateTime'   => $time->format( 'Y-m-d' ),
		);
	}


	/**
	 * Return data from MBO api
	 *
	 * @since 2.4.7
	 *
	 * @param @timestamp defaults to current time
	 *
	 * @throws \Exception
	 *
	 * @return array of MBO schedule data
	 */
	public function getMboResults( $timestamp = null, $version_five = false ) {

		$mb = ( $version_five === false ) ? $this->instantiateMboApi() : $this->instantiateMboApi( 5 );

		if ( ! $mb ) {
			return false;
		}

		if ( ! is_object( $mb ) && is_string( $mb ) && strpos( $mb, 'NO_API_SERVICE' ) ) {
			return $mb;
		}

		if ( $version_five !== false ) {
			$this->classes = $mb->GetClasses( $this->timeFrame() );
		}

		return $mb->debug();
	}
}
