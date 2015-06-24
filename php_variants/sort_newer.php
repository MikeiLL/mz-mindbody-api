<?php
function sortClassesByDate($mz_classes = array()) {
	$mz_classesByDate = array();
	foreach($mz_classes as $class)
	{
		/* Create a new array with a key for each date YYYY-MM-DD
		and corresponsing value an array of class details */ 
		$classDate = date("Y-m-d", strtotime($class['StartDateTime']));
		if(!empty($mz_classesByDate[$classDate])) {
			$mz_classesByDate[$classDate] = array_merge($mz_classesByDate[$classDate], array($class));
		} else {
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
			$class['day_num'] = $day_num;
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
		usort($mz_classes['classes'], function($a, $b) {
			if(date_i18n("N", strtotime($a['StartDateTime'])) == date_i18n("N", strtotime($b['StartDateTime']))) {
				return 0;
			}
			return $a['StartDateTime'] < $b['StartDateTime'] ? -1 : 1;
		}); 
		$missing = array();
		for($i=1;$i<8;$i++){
		$key = array_search($i, array_column($mz_classes['classes'], 'day_num'));
		if ($key !== false){
			$missing[$i] = $key;
			}else{
			$missing[$i] = 'x';
			}
		}
		mz_pr($missing);
	}
	
	return $mz_classesByTime;
}
?>