<?php
/**
 * Site Portal
 *
 * This file contains the class that exposes
 * Ajax methods for interfacing with MBO data.
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
 * Class that holds Client Interface Methods for Ajax requests
 *
 * @since 2.5.9
 *
 * Not currently in use
 */
class SitePortal extends RetrieveSite {


    /**
     * Format for date display, specific to MBO API Plugin.
     *
     * @since  2.4.7
     * @access public
     * @var    string $date_format WP date format option.
     */
    public $date_format;

    /**
     * Format for time display, specific to MBO API Plugin.
     *
     * @since  2.4.7
     * @access public
     * @var    string $time_format
     */
    public $time_format;

    /**
     * Class constructor
     *
     * @since 2.4.7
     */
    public function __construct() {
        $this->date_format = Core\MzMindbodyApi::$date_format;
        $this->time_format = Core\MzMindbodyApi::$time_format;
    }
}
