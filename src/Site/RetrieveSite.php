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
	 * Class constructor
	 *
	 * @since 2.5.9
	 */
	public function __construct() {
		$this->date_format = Core\MzMindbodyApi::$date_format;
		$this->time_format = Core\MzMindbodyApi::$time_format;
	}

	/**
	 * Get All Site Memberships
	 *
	 * @since 2.5.9
	 */
	public function getSiteMemberships() {

		$this->get_mbo_results();

		$result = $this->mb->GetMemberships();

		return $result;
	}

	/**
	 * Get All Site Programs
	 *
	 * @since 2.5.9
	 */
	public function getSitePrograms() {

		$this->get_mbo_results();

		$result = $this->mb->GetPrograms();

		return $result;
	}

	/**
	 * Get All Site Resources
	 *
	 * @since 2.5.9
	 */
	public function getSiteResources() {

		$this->get_mbo_results();

		$result = $this->mb->GetResources();

		return $result;
	}


	/**
	 * Create API Interface Object
	 *
	 * @since 2.5.9
	 *
	 * @return array of MBO schedule data
	 */
	public function get_mbo_results() {

		$this->mb = $this->instantiateMboApi();

		if ( ! $this->mb || 'NO_API_SERVICE' === $this->mb ) {
			return false;
		}

		return true;
	}
}
