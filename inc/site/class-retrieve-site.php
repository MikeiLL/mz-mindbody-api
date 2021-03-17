<?php

namespace MZ_Mindbody\Inc\Site;

use MZ_Mindbody\Inc\Core as Core;
use MZ_Mindbody\Inc\Common as Common;
use MZ_Mindbody\Inc\Libraries as Libraries;
use MZ_Mindbody\Inc\Schedule as Schedule;
use MZ_Mindbody\Inc\Common\Interfaces as Interfaces;
use MZ_Mindbody as NS;

/*
 * Class that holds Client Interface Methods
 *
 *
 */
class Retrieve_Site extends Interfaces\Retrieve
{

    /**
     * The Mindbody API Object
     *
     * @access private
     */
    private $mb;

    /**
     * Format for date display, specific to MBO API Plugin.
     *
     * @since 2.5.9
     * @access   public
     * @var      string $date_format WP date format option.
     */
    public $date_format;

    /**
     * Format for time display, specific to MBO API Plugin.
     *
     * @since 2.5.9
     * @access   public
     * @var      string $time_format
     */
    public $time_format;

    /**
     * Class constructor
     *
     * @since 2.5.9
     */
    public function __construct()
    {
        $this->date_format = Core\MZ_Mindbody_Api::$date_format;
        $this->time_format = Core\MZ_Mindbody_Api::$time_format;
    }

    /**
     * Get All Site Memberships
     *
     * @since 2.5.9
     */
    public function get_site_memberships()
    {

        $this->get_mbo_results();

        $result = $this->mb->GetMemberships();

        return $result;
    }

    /**
     * Get All Site Programs
     *
     * @since 2.5.9
     */
    public function get_site_programs()
    {

        $this->get_mbo_results();

        $result = $this->mb->GetPrograms();

        return $result;
    }

    /**
     * Get All Site Resources
     *
     * @since 2.5.9
     */
    public function get_site_resources()
    {

        $this->get_mbo_results();

        $result = $this->mb->GetResources();

        return $result;
    }


    /**
     * Create API Interface Object
     *
     * @since 2.5.9
     *
     *
     *
     * @return array of MBO schedule data
     */
    public function get_mbo_results()
    {

        $this->mb = $this->instantiate_mbo_API();

        if (!$this->mb || $this->mb == 'NO_API_SERVICE') {
            return false;
        }

        return true;
    }
}
