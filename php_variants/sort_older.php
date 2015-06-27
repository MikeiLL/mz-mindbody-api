<?php
function sortClassesByDate($mz_classes = array()) {
	$mz_classesByDate = array();
	foreach($mz_classes as $class)
	{
		$classDate = date("Y-m-d", strtotime($class['StartDateTime']));
		if(!empty($mz_classesByDate[$classDate])) {
			$mz_classesByDate[$classDate] = array_merge($mz_classesByDate[$classDate], array($class));
		} else {
			$mz_classesByDate[$classDate] = array($class);
		}
	}
	ksort($mz_classesByDate);
	foreach($mz_classesByDate as $classDate => &$mz_classes)
	{
		usort($mz_classes, 'mz_uSortFunction');
	}
	return $mz_classesByDate;
}

function mz_uSortFunction($a, $b) {
			if(strtotime($a['StartDateTime']) == strtotime($b['StartDateTime'])) {
				return 0;
			}else{
			    return $a['StartDateTime'] < $b['StartDateTime'] ? -1 : 1;
			}
		}

function sortClassesByTimeThenDay($mz_classes = array()) {
	$mz_classesByTime = array();
	for($i=0;$i<count($mz_classes);$i++)
	{
		$mz_classes[$i]['day_num'] = '';
	}

	foreach($mz_classes as &$class)
	{
		/* Create a new array with a key for each time
		and corresponsing value an array of class details 
		for classes at that time. */ 
		$classTime = date_i18n("G.i", strtotime($class['StartDateTime']));
		$display_time = (date_i18n("g:i a", strtotime($class['StartDateTime'])));
		//mz_pr(date_i18n("l", strtotime($class['StartDateTime']))); full weekday name
		//mz_pr(date_i18n("N", strtotime($class['StartDateTime']))); 1 - 7 day numbers
		$classDay = date_i18n("l", strtotime($class['StartDateTime']));
		$class['day_num'] = date_i18n("N", strtotime($class['StartDateTime']));
		if(!empty($mz_classesByTime[$classTime])) {
			$mz_classesByTime[$classTime]['classes'] = array_merge($mz_classesByTime[$classTime]['classes'], array($class));
		} else {
			$mz_classesByTime[$classTime] = array('display_time' => $display_time, 
													'classes' => array($class));
		}
	}
	/* Timeslot keys in new array are not time-sequenced so do so*/
	ksort($mz_classesByTime);
	foreach($mz_classesByTime as $scheduleTime => &$mz_classes)
	{
		/*
		$mz_classes is an array of all classes for given time
		Take each of the class arrays and order it by days 1-7
		*/
		foreach($mz_classes['classes'] as $classDate => &$mz_classes)
			{
				usort($mz_classes, 'mz_uSortFunction');
			}
		//fill_empty_slots($mz_classes['classes'], 'day_num');
		$mz_classes['classes'] = week_of_timeslot($mz_classes['classes'], 'day_num');
	}
	return $mz_classesByTime;
}

function week_of_timeslot($array, $indicator){
	$seven_days = array_combine(range(1, 7), array(array(), array(), array(),
											array(), array(), array(), array()));
		foreach($seven_days as $key => $value){
			foreach ($array as $class) {
					if ($class[$indicator] == $key){
						array_push($seven_days[$key], $class);
					}
				}
			}
	return $seven_days;
	}
	
function fill_empty_slots(&$array, $needle)
		{
		$empty = array(array());
		for($i=0;$i<7;$i++){
		$j = $i + 1;
		$key = array_search($j, array_column($array, $needle));
		if ($key !== false){
			break;
			}else{
			array_splice($array, $i, 0, $empty);
			}
		}
		//mz_pr($array);
		}
		
function combine_concurrent(&$array, $needle, $test)
		{
		/*
		For each class at this time slot, if two have the same day_num
		Put them together in an array
		*/
			for($i=0;$i<count($array);$i++)
				{
					$array_of_elements = array();
					//Check if it's actually a class
					if(!empty($value)) {
					echo "Live one.<br/>";
						$array[$i] = array($i => $array[$i]);
						}
				}
		}
?>