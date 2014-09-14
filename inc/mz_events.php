<?php
function mZ_mindbody_show_events (){

 	require_once MZ_MINDBODY_SCHEDULE_DIR .'inc/mz_mbo_init.inc';

	$monthnumber = empty($_GET['mz_month']) ? date("m", strtotime(date('Y-m-d'))) : $_GET['mz_month'];
	$mz_schedule_page = get_permalink();
	$start_end_date = getNextSixty($monthnumber,date("Y"));
	if ($monthnumber != date('m'))
	{
	$mz_months_previous = add_query_arg(array('mz_month' => ($monthnumber - 2)));
	echo ' <a href='.$mz_months_previous.'>Previous</a>';
	$mz_timeframe = array('StartDateTime'=>$start_end_date[0], 'EndDateTime'=>$start_end_date[1]);
	}
	else
	{
	$mz_time_now = current_time( 'Y-m-d', $gmt = 0 );
	$mz_timeframe = array('StartDateTime'=>$mz_time_now, 'EndDateTime'=>$start_end_date[1]);
	}
	$options = get_option( 'mz_mindbody_options','Option Not Set' );
	$mz_cache_reset = isset($options['mz_mindbody_clear_cache']) ? "on" : "off";
	
	$mz_events_cache = "mz_events_cache";
	
	if ( $mz_cache_reset == "on" ){
	delete_transient( $mz_events_cache );
	}
	

if ( false === ( $data = get_transient( $mz_events_cache ) ) ) {
	//Send the timeframe to the GetClasses class, unless already cached
	$data = $mb->GetClasses($mz_timeframe);
	}
	$return = '';
	pr(get_option( 'date_format' ));
	//Cache the mindbody call for 24 hours
	//set_transient($mz_events_cache, $data, 60 * 60 * 24);
	
	if(!empty($data['GetClassesResult']['Classes']['Class'])) {
	
	$classes = $mb->makeNumericArray($data['GetClassesResult']['Classes']['Class']);
	$classes = sortClassesByDate($classes);?>
	<div id="mz_mindbody_events" class="mz_mindbody_events">
	<table class="table mz_mindbody_events"><tr><?php
	$number_of_events = 0;
	foreach($classes as $classDate => $classes) {

			
		foreach($classes as $class) {
		//if (($class['IsCanceled'] == 'TRUE') && ($class['HideCancel'] == 'TRUE'))
		//{
		if (($class['ClassDescription']['Program']['Name'] != 'Events'))
		{
			continue; //skip this and move to next iteration
		}

		?><tr><?php
		    $number_of_events++;
			$sDate = date('m/d/Y', strtotime($class['StartDateTime']));
			$sLoc = $class['Location']['ID'];
			$studioid = $class['Location']['SiteID'];
			$sclassid = $class['ClassScheduleID'];
			$sType = -7;
			$image = $class['ClassDescription']['ImageURL'];
			$sTG = $class['ClassDescription']['Program']['ID'];
			$eventLinkURL = "https://clients.mindbodyonline.com/ws.asp?sDate={$sDate}&sLoc={$sLoc}&sTG={$sTG}&sType={$sType}&sclassid={$sclassid}&studioid={$studioid}";
			$className = $class['ClassDescription']['Name'];
			$startDateTime = date('Y-m-d H:i:s', strtotime($class['StartDateTime']));
			$classDescription = $class['ClassDescription']['Description'];
			$endDateTime = date('Y-m-d H:i:s', strtotime($class['EndDateTime']));
			$staffName = $class['Staff']['Name'];
			$ItemType = $class['ClassDescription']['Program']['Name'];
			$enrolmentType = $class['ClassDescription']['Program']['ScheduleType'];
			$day_and_date =  date("D F d", strtotime($classDate));
			$return .= "<h3>$className</h3>";
			$return .= "<p>with $staffName</p>";
			$return .= "<p>$day_and_date";
			$return .= " ".date('g:i a', strtotime($startDateTime))." - ".date('g:i a', strtotime($endDateTime))."</p>";
			$return .= "<div class='mz_mindbody_events_img pull-left'><img src='$image'></div>";
			$return .= "<p>$classDescription</p>";
			$return .= "<a class='btn pull-right' href='$eventLinkURL'>Sign-Up</a>";
			$return .= "</td>";
		}
	}
		if ($number_of_events < 1)
			$return .= "<h3>No events published beyond ".$start_end_date[0]."</h3>";
		else {
			$mz_months_future = add_query_arg(array('mz_month' => ($monthnumber + 2)));
			$return .= "<hr><h3><a href='.$mz_months_future.'>Future Events</a></h3>";
			}
		$return .= "</tr></table></div>";
	?>
	



	<?php
		
	
} else {
			if(!empty($data['GetClassesResult']['Message'])) {
				echo $data['GetClassesResult']['Message'];
				$return .= "</tr></table></div>";
			} else {
				echo "Error getting classes<br />";
				echo '<pre>'.print_r($data,1).'</pre>';
				$return .= "</tr></table></div>";
			}
		}//EOF If Results/Else
		
		return $return;
	}//EOF mZ_mindbody_show_events



		function getNextSixty($month, $year) {
		  // Adding leading zeros for weeks 1 - 9.
		  $date_string = $year. "/".sprintf('%02d', $month) . "/01";
			//$date_string = "now";

		  $return[0] = date('Y-m-d', strtotime($date_string));//not date('Y-n-j
		  $return[1] = date('Y-m-d', strtotime($date_string . '+2 month'));
		  return $return;
		}
		
?>