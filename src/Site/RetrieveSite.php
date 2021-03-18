<?php

namespace MZoo\MzMindbody\Site;

use MZoo\MzMindbody\Core as Core;
use MZoo\MzMindbody\Common as Common;
use MZoo\MzMindbody\Libraries as Libraries;
use MZoo\MzMindbody\Schedule as Schedule;
use MZoo\MzMindbody\Common\Interfaces as Interfaces;
use MZoo\MzMindbody as NS;

/*
 * Class that holds Client Interface Methods
 *
 *
 */
class RetrieveSite extends Interfaces\Retrieve
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
        $this->date_format = Core\MzMindbodyApi::$date_format;
        $this->time_format = Core\MzMindbodyApi::$time_format;
    }

    /**
     * Get All Site Memberships
     *
     * @since 2.5.9
     */
    public function getSiteMemberships()
    {

        $this->getMboResults();

        $result = $this->mb->GetMemberships();

        return $result;
    }

    /**
     * Get All Site Programs
     *
     * @since 2.5.9
     */
    public function getSitePrograms()
    {

        $this->getMboResults();

        $result = $this->mb->GetPrograms();

        return $result;
    }

    /**
     * Get All Site Resources
     *
     * @since 2.5.9
     */
    public function getSiteResources()
    {

        $this->getMboResults();

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
    public function getMboResults()
    {

        $this->mb = $this->instantiateMboApi();

        if (!$this->mb || $this->mb == 'NO_API_SERVICE') {
            return false;
        }

        return true;
    }
}
