<?php
namespace MZ_Mindbody\Inc\Schedule;

use MZ_Mindbody;
use MZ_Mindbody\Inc\Core as Core;
use MZ_Mindbody\Inc\Common as Common;
use MZ_Mindbody\Inc\Common\Interfaces as Interfaces;

class Retrieve_Class_Owners extends Interfaces\Retrieve_Classes {

	/**
	 * Return Time Frame for request to MBO API
	 *
	 * @since 2.4.7
	 *
	 * Default time_frame is two dates, start of current week as set in WP, and seven days from "now.
	 *
	 * @return array or start and end dates as required for MBO API
	 */
	public function time_frame($timestamp = null){
	    $timestamp = isset($timestamp) ? $timestamp : current_time( 'timestamp' );
		$current_week = $this->single_week($timestamp);
		$seven_days_from_now = $this->seven_days_later($timestamp);
		$start_time = new \Datetime( date_i18n('Y-m-d', $current_week['start']) );
		$end_time = new \Datetime( date_i18n('Y-m-d', $seven_days_from_now) );
      	$current_day_offset = new \Datetime( date_i18n('Y-m-d') );
      	$current_week_end = new \Datetime( date_i18n('Y-m-d', $current_week['end']) );

		// If we are going in future or past based on offset
		if ( !empty($this->atts['offset']) ) {
		    // Insure that we have an absolute number, because attr may be negative
		    $abs = abs($this->atts['offset']);
		    $di = new \DateInterval('P'.$abs.'W');
		    // If it's a negative number, invert the interval
            if ($this->atts['offset'] < 0) $di->invert = 1;
            $start_time->add($di);
            $end_time->add($di);;
            $current_week_end->add($di);
            $current_day_offset->add($di);
        }

        // Set current_day_offset for filtering by sort_classes_by_date_then_time().
        $this->current_day_offset = $current_day_offset;

		// Assign start_date & end_date to instance so can be accessed in grid schedule display
        $this->start_date = $start_time;
        $this->current_week_end = $current_week_end;

		return array('StartDateTime'=> $start_time->format('Y-m-d'), 'EndDateTime'=> $end_time->format('Y-m-d'));
	}

    /**
     * Populate Matrix of Regularly Scheduled Classes
     *
     * This array is used when a Class Instructor "is_substitute" to see who the regular teacher is.
     *
     * @param array $class a single "class" returned from MBO API
     */
    public function populate_regularly_scheduled_classes($class, $message='no message') {
        $class_owners = array();
        $class_count = 0;
        foreach($schedules as $schedule):
            foreach($schedule as $class):
                $class_count++;
                // Initialize array
                $day_of_class = array();
                $classStartTime = new DateTime\WpDateTime($class['StartTime'], Core\Init::$timezone);
                if (isset($class['DaySunday']) && ($class['DaySunday'] == 1)):
                    $day_of_class['Sunday'] = 1;
                endif;
                if (isset($class['DayMonday']) && ($class['DayMonday'] == 1)):
                    $day_of_class['Monday'] = 1;
                endif;
                if (isset($class['DayTuesday']) && ($class['DayTuesday'] == 1)):
                    $day_of_class['Tuesday'] = 1;
                endif;
                if (isset($class['DayWednesday']) && ($class['DayWednesday'] == 1)):
                    $day_of_class['Wednesday'] = 1;
                endif;
                if (isset($class['DayThursday']) && ($class['DayThursday'] == 1)):
                    $day_of_class['Thursday'] = 1;
                endif;
                if (isset($class['DayFriday']) && ($class['DayFriday'] == 1)):
                    $day_of_class['Friday'] = 1;
                endif;
                if (isset($class['DaySaturday']) && ($class['DaySaturday'] == 1)):
                    $day_of_class['Saturday'] = 1;
                endif;

                $class_image = isset($class['ClassDescription']['ImageURL']) ? $class['ClassDescription']['ImageURL'] : '';
                $image_path_array = explode('?imageversion=', $class_image);
                $class_description_array = explode(" ", $class['ClassDescription']['Description']);
                $class_description_substring = implode(" ", array_splice($class_description_array, 0, 5));
                $class_owners[$class['ID']] = array('class_name' => strip_tags($class['ClassDescription']['Name']),
                    'class_description' => $class_description_substring,
                    'class_owner' => strip_tags($class['Staff']['Name']),
                    'class_owner_id' => strip_tags($class['Staff']['ID']),
                    'image_url' => array_shift($image_path_array),
                    'time' => $classStartTime->format('H:i'),
                    'location' => $class['Location']['ID'],
                    'day' => $day_of_class);

            endforeach;
        endforeach;
        delete_transient('mz_class_owners');
        set_transient('mz_class_owners', $class_owners, 60 * 60 * 24 * 7);

        mz_pr(array_shift($class_owners));
        if($message == 'message'):
            return __('Classes and teachers as regularly scheduled reloaded.', 'mz-mindbody-api');
        endif;
    }



}