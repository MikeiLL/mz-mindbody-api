<?php

namespace MzMindbody\Schedule;

use MzMindbody;
use MzMindbody\Core as Core;
use MzMindbody\Common as Common;
use MzMindbody\Common\Interfaces as Interfaces;

class RetrieveSchedule extends Interfaces\RetrieveClasses
{

    /**
     * Return Time Frame for request to MBO API
     *
     * @since 2.4.7
     *
     * Default timeFrame is two dates, start of current week as set in WP, and seven days from "now.
     *
     * @throws \Exception
     *
     * @return array of start and end dates as required for MBO API
     */

    public function timeFrame($timestamp = null)
    {

        $timestamp = isset($timestamp) ? $timestamp : current_time('timestamp');
        // override timestamp here for testing
        // $timestamp = '2020-5-1';

        $current_week = $this->singleWeek($timestamp);
        $sevenDaysLater = $this->sevenDaysLater($timestamp);
        if ((!empty($this->atts['type']) && ($this->atts['type'] === 'day'))) :
            $today = current_time('timestamp');
            $start_time = new \Datetime(date_i18n('Y-m-d', $today));
            $end_time = new \Datetime(date_i18n('Y-m-d', $today));
            // test with $end_time = new \DateTime('tomorrow');
        else :
            $start_time = new \Datetime(date_i18n('Y-m-d', $current_week['start']));
            $end_time = new \Datetime(date_i18n('Y-m-d', $sevenDaysLater));
        endif;
        $current_day_offset = new \Datetime(date_i18n('Y-m-d'));
        $current_week_end = new \Datetime(date_i18n('Y-m-d', $current_week['end']));
        //print_r("OFFSET: ");
        //print_r($this->atts['offset']);
        // If we are going in future or past based on offset
        if (!empty($this->atts['offset'])) {
            // Insure that we have an absolute number, because attr may be negative
            $abs = abs($this->atts['offset']);
            if ((!empty($this->atts['type']) && ($this->atts['type'] === 'day'))) :
                $di = new \DateInterval('P' . $abs . 'D');
            else :
                $di = new \DateInterval('P' . $abs . 'W');
            endif;

            // If it's a negative number, invert the interval
            if ($this->atts['offset'] < 0) {
                $di->invert = 1;
            }
            $start_time->add($di);
            $end_time->add($di);
            $current_week_end->add($di);
            $current_day_offset->add($di);
        }

        // Set current_day_offset for filtering by sortClassesByDateThenTime().
        $this->current_day_offset = $current_day_offset;

        // Assign start_date & end_date to instance so can be accessed in grid schedule display
        $this->start_date = $start_time;
        $this->current_week_end = $current_week_end;

        return array('StartDateTime' => $start_time->format('Y-m-d'), 'EndDateTime' => $end_time->format('Y-m-d'));
    }
}
