<?php

namespace MZoo\MzMindbody\Schedule;

use MZoo\MzMindbody as NS;
use MZoo\MzMindbody\Core as Core;
use MZoo\MzMindbody\Libraries as Libraries;
use MZoo\MzMindbody\Schedule as Schedule;
use MZoo\MzMindbody\Common\Interfaces as Interfaces;

/*
 * Class that is extended for Schedule Display Ajax Retrieve Registrants (s)
 *
 */

class RetrieveRegistrants extends Interfaces\Retrieve {



	/**
	 * Holds the Get Class Visits Results for a given class.
	 *
	 * @since  2.4.7
	 * @access public
	 * @var    array $class_visits Array of names of class registrants.
	 */
	public $class_visits;


	/**
	 * Holds the registrants for a given class.
	 *
	 * @since  2.4.7
	 * @access public
	 * @var    array $registrants Array of names of class registrants.
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
	 * @return array of MBO schedule data
	 */
	public function getMboResults( $classid = 0 ) {

		if ( empty( $classid ) ) {
			return false;
		}

		$mb = $this->instantiateMboApi();

		if ( ! $mb || $mb == 'NO_API_SERVICE' ) {
			return false;
		}

		if ( $this->mbo_account !== 0 ) {
			// If account has been specified in shortcode, update credentials
			$mb->sourceCredentials['SiteIDs'][0] = $this->mbo_account;
		}

		$this->class_visits = $mb->GetClassVisits( array( 'ClassID' => $classid ) );

		return $this->class_visits;
	}

	/**
	 * Get Registrants called via Ajax
	 */
	function ajax_get_registrants() {
		// Generated in Schedule\ScheduleItem.
		check_ajax_referer( 'mz_mbo_get_registrants', 'nonce' );

		$classid = $_REQUEST['classID'];

		$result['type'] = 'success';

		$registrants = $this->get_registrants( $classid );

		if ( ! $registrants ) :
			$result['type'] = 'error';
		endif;

		$result['message'] = $registrants;

		if ( ! empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest' ) {
			$result = wp_json_encode( $result );
			echo $result;
		} else {
			header( 'Location: ' . $_SERVER['HTTP_REFERER'] );
		}

		die();
	}
	// End Ajax Get Registrants

	/**
	 * Get Registrants from MBO
	 *
	 * @param int $classid
	 */
	function get_registrants( $classid ) {

		$class_visits = $this->getMboResults( $classid );

		if ( ! $class_visits ) :
			return false;
	 else :
		 if ( empty( $class_visits['Class']['Visits'] ) ) :
			 return __( 'No registrants yet.', 'mz-mindbody-api' );
	  else :
		  $registrant_ids = array();

		  // Build array of registrant ids to send to GetClients
		  foreach ( $class_visits['Class']['Visits'] as $registrant ) {
			  $registrant_ids[] = $registrant['ClientId'];
		  }

		  // send list of registrants to GetRegistrants
		  $mb = $this->instantiateMboApi();

		  if ( ! $mb || $mb == 'NO_API_SERVICE' ) {
			  return false;
		  }

		  if ( $this->mbo_account !== 0 ) {
			  // If account has been specified in shortcode, update credentials
			  $mb->sourceCredentials['SiteIDs'][0] = $this->mbo_account;
		  }

		  $this->registrants = $mb->GetClients( array( 'clientIds' => $registrant_ids ) );

		  $registrant_names = array();
		  // Add first name, last initial
		  foreach ( $this->registrants['Clients'] as $registrant ) {
			  $registrant_names[] = $registrant['FirstName'] . ' ' . substr( $registrant['LastName'], 0, 1 ) . '.';
		  }
	  endif;
	 endif;

	 return $registrant_names;
	}
}
