<?php
/**
 * Mini Schedule Item
 *
 * This file contains the class that should probably
 * be an extension of class schedule item, but currently
 * is a minimized copy of it's content, used to generate
 * simplified version of a schedule item for display
 * in modal by which client might view their personal
 * schedule. Currently inactive as there is not built-in
 * client verification in API v6.
 *
 * @package MzMindbody
 */

namespace MZoo\MzMindbody\Schedule;

use MZoo\MzMindbody;
use MZoo\MzMindbody\Core as Core;
use MZoo\MzMindbody\Libraries\HtmlElement;
use MZoo\MzMindbody\Libraries as Libraries;

/**
 * Simplified version of the ScheduleItem class.
 *
 * These objects hold the schedule items returned in a Client Schedule
 * returned by GetClientSchedule.
 *
 * @Used By Class_ClientPortal (was).
 * @depreciated
 */
class MiniScheduleItem extends ScheduleItem {

    /**
     * Populate attributes with data from MBO
     *
     * @since 2.4.7
     *
     * @param array $schedule_item item attributes. See class description.
     * @param array $atts from wp post shortcode.
     */
    public function __construct( $schedule_item, $atts = array() ) {
        $this->class_name     = isset( $schedule_item['Name'] ) ? $schedule_item['Name'] : '';
        $this->start_datetime = $schedule_item['StartDateTime'];
        $this->end_datetime   = $schedule_item['EndDateTime'];
        $this->staff_name     = isset( $schedule_item['Staff']['Name'] ) ? $schedule_item['Staff']['Name'] : '';
        $this->ID             = $schedule_item['Id'];
        $this->site_id        = ! empty( $atts['account'] ) ? $atts['account'] : Core\MzMindbodyApi::$basic_options['mz_mindbody_siteID'];
    }
}
