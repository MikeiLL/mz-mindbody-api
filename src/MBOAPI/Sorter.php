<?php
namespace mZoo\MBOAPI;

class Sorter {		
	/**
	 * Return an array of MBO Class Objects, ordered by date.
	 *
	 * This is used in Horizontal view. It gets the filtered results from the MBO API call and  builds an array of Class Event Objects,
	 * sequenced by date and time.
	 * 
	 *
	 * @param @type array $mz_classes
	 * @param @type string $time_format Format string for php strtotime function Default: "g:i a"
	 * @param @type array OR numeric $locations Single or list of MBO location numerals Default: 1
	 * @param @type boolean $hide_cancelled Whether or not to display cancelled classes. Default: 0
	 * @param @type array $hide Items to be removed from calendar
	 * @param @type boolean $advanced Whether or not allowing online class sign-up via plugin
	 * @param @type boolean $show_registrants Whether or not to display class registrants in modal popup
	 * @param @type boolean $registrants_count  Whether we want to show count of registrants in a class (TODO - finish) @default: 0
	 * @param @type string $calendar_format Depending on final display, we may create items in Single_event class differently. 
	 *																			Default: 'horizontal'
	 * @param @type boolean $delink Make class name NOT a link
	 * @param @type string $class_type MBO API has 'Enrollment' and 'DropIn'. 'Enrolment' is a "workdhop". Default: 'Enrollment'
	 * @param @type numeric $account Which MBO account is being interfaced with.
	 * @param @type boolean $this_week If true, show only week from today.
	 *
	 * @return @type array of Objects from Single_event class, in Date (and time) sequence.
	*/
	public static function sortClassesByDate($mz_classes = array(), 
														$time_format = "g:i a", 
														$locations=1, 
														$hide_cancelled=0, 
														$hide=array(), 
														$advanced=0, 
														$show_registrants=0, 
														$registrants_count=0, 
														$calendar_format='horizontal', 
														$delink, 
														$class_type='Enrollment', 
														$account,
														$this_week=0) {

		// This is the array that will hold the classes we want to display
		$mz_classesByDate = array();

		if(!is_array($locations)):
			$locations = array($locations);
		endif;
	
		if (isset($_GET['mz_date'])):
			// If user has requested a specific start date, return dates for that period
			list($current, $current_date_string) = Schedule_Operations::Schedule_Operations::current_to_day_of_week_today();
		else:
			$current = new \DateTime(null, Schedule_Operations::Schedule_Operations::get_blog_timezone());
			$blog_timezone = new \DateTime(null, Schedule_Operations::Schedule_Operations::get_blog_timezone());
			$current_date_string = $blog_timezone->format('Y-m-d');
		endif;
	
		$end_of_week = clone $current;
		$end_of_week = $end_of_week->add(new \DateInterval('P6D'))->format('Y-m-d');

		foreach($mz_classes as $class)
		{
			//mz_pr($class);
			if ($hide_cancelled == 1):
				if ($class['IsCanceled'] == 1):
					continue;
				endif;
			endif;
		
			$classDate = date("Y-m-d", strtotime($class['StartDateTime']));
		
			if($this_week):
				// Ignore classes that are outside of seven day week starting today
				if ($classDate < $current_date_string || $classDate > $end_of_week):
					continue;
				endif;
			endif;
		
			/* Create a new array with a key for each date YYYY-MM-DD
			and corresponsing value an array of class details */ 

			$single_event = new Single_event($class, $daynum="", $hide=array(), $locations, $hide_cancelled=0, 
																				$advanced, $show_registrants, $registrants_count, 
																				$calendar_format, $delink, $account);
		
			if(!empty($mz_classesByDate[$classDate])) {
				if (
					(!in_array($class['Location']['ID'], $locations)) || 
					($class['ClassDescription']['Program']['ScheduleType'] == $class_type)
					) {
						continue;
					}
				//$mz_classesByDate[$classDate] = array_merge($mz_classesByDate[$classDate], array($class));
				array_push($mz_classesByDate[$classDate]['classes'], $single_event);
			} else {
				if (
					(!in_array($class['Location']['ID'], $locations)) || 
					($class['ClassDescription']['Program']['ScheduleType'] == $class_type)
					) {
						continue;
					}
				//$mz_classesByDate[$classDate]['classes'] = $single_event;
				$mz_classesByDate[$classDate] = array('classes' => array($single_event));
			}
		}
		/* They are not ordered by date so order them by date */
		ksort($mz_classesByDate);
		foreach($mz_classesByDate as $classDate => &$mz_classes)
		{	
			/*
			$mz_classes is an array of all classes for given date
			Take each of the class arrays and order it by time
			$mz_classesByDate sould have a length of seven, one for
			each day of the week.
			*/
			usort($mz_classes['classes'], function($a, $b) {
				if(strtotime($a->startDateTime) == strtotime($b->startDateTime)) {
					return 0;
					}
				return $a->startDateTime < $b->startDateTime ? -1 : 1;
				}); 
		}
		
		return $mz_classesByDate;
	}

	/**
	 * Return an array of MBO Class Objects, ordered by date.
	 *
	 * This is used in Grid view. It gets the filtered results from the MBO API call and builds a matrix, top level of which is
	 * seven arrays, one for each of seven days in a week (for a calendar column), each one of the Day columns contains an array
	 * of Class Event objects, sequenced by time of day, earliest to latest.
	 * 
	 *
	 * @param @type array $mz_classes
	 * @param @type string $time_format Format string for php strtotime function Default: "g:i a"
	 * @param @type array/numeric $locations Single or list of MBO location numerals Default: 1
	 * @param @type boolean $hide_cancelled Whether or not to display cancelled classes. Default: 0
	 * @param @type array $hide Items to be removed from calendar
	 * @param @type boolean $advanced Whether or not allowing online class sign-up via plugin
	 * @param @type boolean $show_registrants Whether or not to display class registrants in modal popup
	 * @param @type boolean $registrants_count  Whether we want to show count of registrants in a class (TODO - finish) @default: 0
	 * @param @type string $calendar_format Depending on final display, we may create items in Single_event class differently. 
	 * @param @type boolean $delink Make class name NOT a link
	 * @param @type string $class_type MBO API has 'Enrollment' and 'DropIn'. 'Enrolment' is a "workdhop". Default: 'Enrollment'
	 * @param @type numeric $account Which MBO account is being interfaced with.
	 *
	 * @return @type array of Objects from Single_event class, in Date (and time) sequence.
	*/
	public static function sortClassesByTimeThenDay($mz_classes = array(),
																		$time_format = "g:i a", 
																		$locations=1, 
																		$hide_cancelled=0, 
																		$hide, 
																		$advanced, 
																		$show_registrants, 
																		$registrants_count, 
																		$calendar_format, 
																		$delink, 
																		$class_type = 'Enrollment', 
																		$account) {														
		$mz_classesByTime = array();

		if(!is_array($locations)):
			$locations = array($locations);
		endif;
	
		// Note: $_GET['mz_date'] is always a Monday.
		if (isset($_GET['mz_date'])):

			$end_of_range = new \DateTime($_GET['mz_date'], Schedule_Operations::Schedule_Operations::get_blog_timezone());
			$end_of_range->add(new \DateInterval('P1W'));
		else:
			$end_of_range = new \DateTime(null, Schedule_Operations::Schedule_Operations::get_blog_timezone());
			$end_of_range->add(new \DateInterval('P1W'));
		endif;
										
		foreach($mz_classes as $class)
		{
			// Ignore classes that are not part of current week (ending Sunday)
			if (new \DateTime($class['StartDateTime'], Schedule_Operations::Schedule_Operations::get_blog_timezone()) >= $end_of_range):
				continue;
			endif;
		
			if ($hide_cancelled == 1):
				if ($class['IsCanceled'] == 1):
					continue;
				endif;
			endif;
		
			/* Create a new array with a key for each time
			and corresponsing value an array of class details 
			for classes at that time. */ 
			$classTime = date_i18n("G.i", strtotime($class['StartDateTime'])); // for numerical sorting
			// $class['day_num'] = '';
			$class['day_num'] = date_i18n("N", strtotime($class['StartDateTime'])); // Weekday num 1-7

			$single_event = new Single_event($class, 
																				$class['day_num'], 
																				$hide, 
																				$locations, 
																				$hide_cancelled, 
																				$advanced, 
																				$show_registrants, 
																				$registrants_count, 
																				$calendar_format, 
																				$delink, 
																				$account);
																			
			if(!empty($mz_classesByTime[$classTime])) {
				if (
					(!in_array($class['Location']['ID'], $locations)) || 
					($class['ClassDescription']['Program']['ScheduleType'] == 'Enrollment')
					) {
						continue;
					}
				array_push($mz_classesByTime[$classTime]['classes'], $single_event);
			} else {
				// Assign the first element ( of this time slot ?).
				if (
					(!in_array($class['Location']['ID'], $locations)) || 
					($class['ClassDescription']['Program']['ScheduleType'] == 'Enrollment')
					) {
						continue;
					}
				$display_time = (date_i18n($time_format, strtotime($class['StartDateTime']))); 
				$mz_classesByTime[$classTime] = array('display_time' => $display_time, 
														'classes' => array($single_event));
			
			}
		}

		/* Timeslot keys in new array are not time-sequenced so do so*/
		ksort($mz_classesByTime);
		foreach($mz_classesByTime as $scheduleTime => &$mz_classes)
		{
			/*
			$mz_classes is an array of all class_event objects for given time
			Take each of the class arrays and order it by days 1-7
			*/
			usort($mz_classes['classes'], function($a, $b) {
				if(date_i18n("N", strtotime($a->startDateTime)) == date_i18n("N", strtotime($b->startDateTime))) {
					return 0;
					}
					return $a->startDateTime < $b->startDateTime ? -1 : 1;
				}); 
			$mz_classes['classes'] = Schedule_Operations::Schedule_Operations::week_of_timeslot($mz_classes['classes'], 'day_num');
		}
		return $mz_classesByTime;
	}

}

?>