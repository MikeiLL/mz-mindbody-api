<?php
function mZ_mindbody_show_events ()
{
 	require_once MZ_MINDBODY_SCHEDULE_DIR .'inc/mz_mbo_init.inc';
    
 	if ($options['mz_mindbody_eventID'] != '') {
 	 	// grab session type IDs for events
 	    $mz_sessions = array($options['mz_mindbody_eventID']);
 	    }
 	    else
 	    {
 	    return "<h2>Error: MBO Event Type IDs must be set in Admin Panel</h2>";
 	    }

 	// grab session type IDs for events
 	$mz_sessions = array($options['mz_mindbody_eventID']);

	$monthnumber = empty($_GET['mz_month']) ? date_i18n("m", strtotime(date_i18n('Y-m-d'))) : $_GET['mz_month'];
	$start_end_date = getNextSixty($monthnumber,date_i18n("Y"));
	$mz_months_future = add_query_arg(array('mz_month' => ($monthnumber + 2)));

	$return = '';

	// set time parameters and optionally add link to previous month.
	if ($monthnumber != date_i18n('m'))
	{
		$mz_months_previous = add_query_arg(array('mz_month' => ($monthnumber - 2)));
		$return .='<p><a href="'.$mz_months_previous.'">__(Previous)</a></p>';
		$mz_start_date_time = $start_end_date[0];
	}
	else
	{
		$mz_start_date_time = current_time( 'Y-m-d', $gmt = 0 );
	}

	// limit to specific session ID's if any are set.
	// or we can use this conditional to block output entirely
	if (!empty($mz_sessions) || ($mz_sessions[0] != 0))
	{
		$mz_query_param = array('StartDateTime'=>$mz_start_date_time, 'EndDateTime'=>$start_end_date[1], 'SessionTypeIDs'=>$mz_sessions);
	}
	else
	{
		$mz_query_param = array('StartDateTime'=>$mz_start_date_time, 'EndDateTime'=>$start_end_date[1]);
	}

	// START caching configuration
	$mz_events_cache = "mz_events_cache";
	$mz_cache_reset = isset($options['mz_mindbody_clear_cache']) ? "on" : "off";

	if ( $mz_cache_reset == "on" )
	{
		delete_transient( $mz_events_cache );
	}

	if ( false === ( $data = get_transient( $mz_events_cache ) ) )
	{
		$data = $mb->GetClasses($mz_query_param);
	}

	//Cache the mindbody call for 24 hours
	// TODO make cache timeout configurable.
	set_transient($mz_events_cache, $data, 60 * 60);
	// END caching configuration

	// keep this here
	//$return .= $mb->debug();

	if(!empty($data['GetClassesResult']['Classes']['Class']))
	{
		$number_of_events = count($data['GetClassesResult']['Classes']['Class']);
		if ($number_of_events >= 1)
		{
			$return .= '<p>' . __('There are') . ' ' . count($data['GetClassesResult']['Classes']['Class']) . ' ' . __('upcoming events.') . '</p>';

			$classes = $mb->makeNumericArray($data['GetClassesResult']['Classes']['Class']);
			$classes = sortClassesByDate($classes);

			$return .= '<div class="mz_mindbody_events">';
			$return .= '<table class="table mz_mindbody_events">';

			foreach($classes as $classDate => $classes)
			{
				foreach($classes as $class)
				{
					if (!(($class['IsCanceled'] == 'TRUE') && ($class['HideCancel'] == 'TRUE')))
					{
						$sDate = date_i18n('m/d/Y', strtotime($class['StartDateTime']));
						$sLoc = $class['Location']['ID'];
						$studioid = $class['Location']['SiteID'];
						$sclassid = $class['ClassScheduleID'];
						// why is this hardcoded?
						$sType = -7;
						$image = empty($class['ClassDescription']['ImageURL']) ? '' : $class['ClassDescription']['ImageURL'];
						$sTG = $class['ClassDescription']['Program']['ID'];
						$eventLinkURL = "https://clients.mindbodyonline.com/ws.asp?sDate={$sDate}&sLoc={$sLoc}&sTG={$sTG}&sType={$sType}&sclassid={$sclassid}&studioid={$studioid}";
						$className = $class['ClassDescription']['Name'];
						$startDateTime = date_i18n('Y-m-d H:i:s', strtotime($class['StartDateTime']));
						$classDescription = $class['ClassDescription']['Description'];
						$endDateTime = date_i18n('Y-m-d H:i:s', strtotime($class['EndDateTime']));
						$staffName = $class['Staff']['Name'];
						$ItemType = $class['ClassDescription']['Program']['Name'];
						$enrolmentType = $class['ClassDescription']['Program']['ScheduleType'];
						$day_and_date =  date_i18n("D F d", strtotime($classDate));

						$return .= '<tr><td>';
						$return .= '<div class="mz_mindbody_events_header clearfix">';

						$return .= '<div class="mz_mindbody_events_img"><img src="' . $image . '"></div>';

						$return .= '<div class="mz_mindbody_events_details">';
						$return .= "<h3>$className</h3>";
						$return .= "<p>with $staffName</p>";
						$return .= "<p>$day_and_date, " . date_i18n('g:i a', strtotime($startDateTime))." - ".date_i18n('g:i a', strtotime($endDateTime)) . "</p>";
						$return .= '<a class="btn btn-success" href="' . $eventLinkURL . '">' . __('Sign-Up') . '</a>';
						$return .= '</div>';

						$return .= '</div>';

						$return .= '<div class="mz_mindbody_event_description">';
						$return .= "<p>$classDescription</p>";
						$return .= "</div>";
						$return .= '</td></tr>';
					}
				}
			}
			$return .=	'</table></div>';
		}
		else
		{
			$return .= '<h3>' . __('No events published beyond') . ' ' . $start_end_date[0] . '.</h3>';
		}
		$return .= '<p><a href="' . $mz_months_future . '">' . __('Future Events') . '</a></p>';
	}
	else
	{

		if(!empty($data['GetClassesResult']['Message']))
		{
			$return .= $data['GetClassesResult']['Message'];
		}
		else
		{
			$return .= __('Error getting classes') . '<br />';
			//$return .= '<pre>'.print_r($data,1).'</pre>';
		}

	}//EOF If Results/Else

	return $return;

}//EOF mZ_mindbody_show_events


function getNextSixty($month, $year) {
  // Adding leading zeros for weeks 1 - 9.
  $date_string = $year. "/".sprintf('%02d', $month) . "/01";
	//$date_string = "now";

  $return[0] = date_i18n('Y-m-d', strtotime($date_string));//not date('Y-n-j
  $return[1] = date_i18n('Y-m-d', strtotime($date_string . '+2 month'));
  return $return;
}
?>
