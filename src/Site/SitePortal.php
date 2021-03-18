<?php

namespace MzMindbody\Site;

use MzMindbody\Core as Core;
use MzMindbody\Common as Common;
use MzMindbody\Libraries as Libraries;
use MzMindbody\Schedule as Schedule;
use MzMindbody\Common\Interfaces as Interfaces;
use MzMindbody as NS;

/*
 * Class that holds Client Interface Methods for Ajax requests
 *
 * @since 2.5.9
 *
 * Not currently in use
 */
class SitePortal extends RetrieveSite
{

    /**
     * The Mindbody API Object
     *
     * @access private
     */
    private $mb;

    /**
     * Template Date for sending to template partials
     *
     * @access private
     */
    private $template_data;

    /**
     * Client ID
     *
     * The MBO ID of the Current User/Client
     *
     * @access private
     */
    private $clientID;

    /**
     * Format for date display, specific to MBO API Plugin.
     *
     * @since    2.4.7
     * @access   public
     * @var      string $date_format WP date format option.
     */
    public $date_format;

    /**
     * Format for time display, specific to MBO API Plugin.
     *
     * @since    2.4.7
     * @access   public
     * @var      string $time_format
     */
    public $time_format;

    /**
     * Class constructor
     *
     * Since 2.4.7
     */
    public function __construct()
    {
        $this->date_format = Core\MzMindbodyApi::$date_format;
        $this->time_format = Core\MzMindbodyApi::$time_format;
    }
}
