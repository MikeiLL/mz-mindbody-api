<?php
function sortClassesByDate($mz_classes = array(), $time_format = "g:i a", $locations = array(1), $type = 'Enrollment') {
	$mz_classesByDate = array();
	foreach($mz_classes as $class)
	{
		/* Create a new array with a key for each date YYYY-MM-DD
		and corresponsing value an array of class details */ 
		$classDate = date("Y-m-d", strtotime($class['StartDateTime']));
		if(!empty($mz_classesByDate[$classDate])) {
			if (
				(!in_array($class['Location']['ID'], $locations)) || 
				(($class['IsCanceled'] == 1) && ($class['HideCancel'] == 1)) ||
				($class['ClassDescription']['Program']['ScheduleType'] == $type)
				) {
					continue;
				}
			$mz_classesByDate[$classDate] = array_merge($mz_classesByDate[$classDate], array($class));
		} else {
			if (
				(!in_array($class['Location']['ID'], $locations)) || 
				(($class['IsCanceled'] == 1) && ($class['HideCancel'] == 1)) ||
				($class['ClassDescription']['Program']['ScheduleType'] == $type)
				) {
					continue;
				}
			$mz_classesByDate[$classDate] = array($class);
		}
	}
	/* They are not ordered by date so order them by date */
	ksort($mz_classesByDate);
	foreach($mz_classesByDate as $classDate => &$mz_classes)
	{	
		/*
		$mz_classes is an array of all classes for given date
		Take each of the class arrays and order it by time
		*/
		usort($mz_classes, function($a, $b) {
			if(strtotime($a['StartDateTime']) == strtotime($b['StartDateTime'])) {
				return 0;
			}
			return $a['StartDateTime'] < $b['StartDateTime'] ? -1 : 1;
		});
	}
	return $mz_classesByDate;
}

function sortClassesByTimeThenDay($mz_classes = array(), $time_format = "g:i a", $locations = array(1)) {
	$mz_classesByTime = array();
	foreach($mz_classes as $class)
	{
		
		/* Create a new array with a key for each time
		and corresponsing value an array of class details 
		for classes at that time. */ 
		$classTime = date_i18n("G.i", strtotime($class['StartDateTime'])); // for numerical sorting
		$class['day_num'] = date_i18n("N", strtotime($class['StartDateTime'])); // Weekday num 1-7
		if(!empty($mz_classesByTime[$classTime])) {
			if (
				(!in_array($class['Location']['ID'], $locations)) || 
				(($class['IsCanceled'] == 1) && ($class['HideCancel'] == 1)) ||
				($class['ClassDescription']['Program']['ScheduleType'] == 'Enrollment')
				) {
					continue;
				}
			$single_event = new Single_event($class, $class['day_num']);
			array_push($mz_classesByTime[$classTime]['classes'], $single_event);
		} else {
			// Assign the first element ( of this time slot ?).
			if (
				(!in_array($class['Location']['ID'], $locations)) || 
				(($class['IsCanceled'] == 1) && ($class['HideCancel'] == 1)) ||
				($class['ClassDescription']['Program']['ScheduleType'] == 'Enrollment')
				) {
					continue;
				}
			$single_event = new Single_event($class, $class['day_num']);
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
		$mz_classes['classes'] = week_of_timeslot($mz_classes['classes'], 'day_num');
	}
	return $mz_classesByTime;
}

function week_of_timeslot($array, $indicator){
	/*
	Make a clean array with seven corresponding slots and populate 
	based on indicator (day) for each class. There may be more than
	one even for each day and empty arrays will represent empty time slots.
	*/
	$seven_days = array_combine(range(1, 7), array(array(), array(), array(),
											array(), array(), array(), array()));
		foreach($seven_days as $key => $value){
			foreach ($array as $class) {
					if ($class->$indicator == $key){
						array_push($seven_days[$key], $class);
					}
				}
			}
	return $seven_days;
	}
?>