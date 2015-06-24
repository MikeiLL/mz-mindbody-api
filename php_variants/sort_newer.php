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
	$mz_classesByDate = array();
	foreach($mz_classes as $class)
	{
		/* Create a new array with a key for each time
		and corresponsing value an array of class details 
		for classes at that time. */ 
		$classTime = date_i18n("g:i a", strtotime($class['StartDateTime']));
		//mz_pr(date_i18n("g:i a", strtotime($class['StartDateTime'])));
		//mz_pr(date_i18n("l", strtotime($class['StartDateTime'])));
		//mz_pr(date_i18n("N", strtotime($class['StartDateTime'])));
		$classDay = date_i18n("l", strtotime($class['StartDateTime']));
		if(!empty($mz_classesByDate[$classTime])) {
			$mz_classesByDate[$classTime] = array_merge($mz_classesByDate[$classTime], array($class));
		} else {
			$mz_classesByDate[$classTime] = array($class);
		}
	}
	/* They are not ordered by time so order them by time */
	ksort($mz_classesByDate);
	foreach($mz_classesByDate as $scheduleTime => &$mz_classes)
	{	
		/*
		$mz_classes is an array of all classes for given date
		Take each of the class arrays and order it by time
		*/
		usort($mz_classes, function($a, $b) {
			if(date_i18n("N", strtotime($a['StartDateTime'])) == date_i18n("N", strtotime($b['StartDateTime']))) {
				echo date_i18n("N", strtotime($a['StartDateTime'])) . ' and '. date_i18n("N", strtotime($b['StartDateTime'])) . '<br/>';
				return 0;
			}
			return $a['StartDateTime'] < $b['StartDateTime'] ? -1 : 1;
			echo date_i18n("N", strtotime($a['StartDateTime'])) . ' versus '. date_i18n("N", strtotime($b['StartDateTime'])). '<br/>';
		});
	}
	return $mz_classesByDate;
}
?>