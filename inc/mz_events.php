<?php
function mZ_mindbody_show_events ($atts, $account=0)
{
 	require_once MZ_MINDBODY_SCHEDULE_DIR .'inc/mz_mbo_init.inc';

	global $add_mz_ajax_script;
	$add_mz_ajax_script = true;

	// optionally pass in a type parameter. Defaults to week.
	extract( shortcode_atts( array(
		'account' => '0'
			), $atts ) );
			
 	// grab session type IDs for events
 	$mz_sessions = array($options['mz_mindbody_eventID']);

	$return = '';
	
    $mz_date = empty($_GET['mz_date']) ? date_i18n('Y-m-d') : mz_validate_date($_GET['mz_date']);
    
	// only make API call if we have sessions set
	if (!empty($mz_sessions) && ($mz_sessions[0] != 0))
	{
	    $mz_timeframe = array_slice(mz_getDateRange($mz_date, $mz_event_calendar_duration), 0, 1);
	    
	    //While we still need to support php 5.2 and can't use [0] on above
	    $mz_timeframe = array_pop($mz_timeframe);
	    
        $mz_timeframe = array_merge($mz_timeframe, array('SessionTypeIDs'=>$mz_sessions));

		// START caching configuration
		$mz_events_cache = "mz_events_cache";
		$mz_cache_reset = isset($options['mz_mindbody_clear_cache']) ? "on" : "off";

		if ( $mz_cache_reset == "on" )
		{
			delete_transient( $mz_events_cache );
		}

		if ( false === ( $mz_event_data = get_transient( $mz_events_cache ) ) )
		{
			if ($account == 0) {
			$mz_schedule_data = $mb->GetClasses($mz_timeframe);
		}else{
			$mb->sourceCredentials['SiteIDs'][0] = $account; 
			$mz_schedule_data = $mb->GetClasses($mz_timeframe);
		}
			$mz_event_data = $mb->GetClasses($mz_timeframe);
		}

		//Cache the mindbody call for 1 hour
		// TODO make cache timeout configurable.
		set_transient($mz_events_cache, $mz_event_data, 60 * 60);
		// END caching configuration
		// keep this here
		//$return .= $mb->debug();
        
		if(!empty($mz_event_data['GetClassesResult']['Classes']['Class']))
		{
			$number_of_events = count($mz_event_data['GetClassesResult']['Classes']['Class']);
			
			if ($number_of_events >= 1)
			{
				$return .= '<p>' .$mz_event_calendar_duration .' '. __('Day Event Calendar');
				$return .=  ' '. date_i18n($mz_date_display, strtotime($mz_timeframe['StartDateTime']));
				$return .= ' - ';
				$return .= date_i18n($mz_date_display, strtotime($mz_timeframe['EndDateTime'])).'</p>';
				//TODO Make this work - displaying number 20 with one event (correct on first page with 5 events).
				//$return .= ': ' . $number_of_events . ' '.__('event(s)').'</p>';

				$classes = $mb->makeNumericArray($mz_event_data['GetClassesResult']['Classes']['Class']);
				$classes = sortClassesByDate($classes);
                $return .= mz_mbo_schedule_nav($mz_date, "Events", $mz_event_calendar_duration);
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
							$sclassid = $class['ID'];
							// why is this hardcoded?
							$sType = -7;
							$isAvailable = $class['IsAvailable'];
							if (empty($class['ClassDescription']['ImageURL']))
							    $image = '';
							    else
							    $image = '<img class="mz_mindbody_events_img" src="' .$class['ClassDescription']['ImageURL'] . '">';
							$sTG = $class['ClassDescription']['Program']['ID'];
							$eventLinkURL = "https://clients.mindbodyonline.com/ws.asp?sDate={$sDate}&amp;sLoc={$sLoc}&amp;sTG={$sTG}&amp;sType={$sType}&amp;sclassid={$sclassid}&amp;studioid={$studioid}";
							$className = $class['ClassDescription']['Name'];
							$startDateTime = date_i18n('Y-m-d H:i:s', strtotime($class['StartDateTime']));
							$classDescription = $class['ClassDescription']['Description'];
							$endDateTime = date_i18n('Y-m-d H:i:s', strtotime($class['EndDateTime']));
							$staffName = $class['Staff']['Name'];
							$ItemType = $class['ClassDescription']['Program']['Name'];
							$enrolmentType = $class['ClassDescription']['Program']['ScheduleType'];
							$day_and_date =  date_i18n("D F d", strtotime($classDate));

							$return .= '<tr class="mz_description_holder"><td>';
							$return .= "<h3>$className</h3>";

							$clientID = isset($_SESSION['GUID']) ? $_SESSION['client']['ID'] : '';
							$add_to_class_nonce = wp_create_nonce( 'mz_MBO_add_to_class_nonce');
							if ($clientID == ''){
								 $return .= $isAvailable ? '<br/><a class="btn mz_add_to_class" href="'.home_url().'/login">Login to Sign-up</a>': '';
								  }else{
							  $return .= $isAvailable ? '<br/><a id="mz_add_to_class" class="btn mz_add_to_class"' 
								. ' data-nonce="' . $add_to_class_nonce 
								. '" data-classID="' . $sclassid  
								. '" data-clientID="' . $clientID 
								. '">' .
							  '<span class="signup">'.__('Sign-Up') . '</span><span class="count" style="display:none">0</span></a>': '';
							  }
							$return .= '<br/><div id="visitMBO" class="btn visitMBO" style="display:none">';
							return .= '<a href="'.$eventLinkURL.'" target="_blank">Manage on MindBody Site</a></div>';
							$return .= '<p class="mz_event_staff">with '. $staffName.'</p>';							

							$return .= '<h4 class="mz_event_staff">'.$day_and_date.', ' . date_i18n('g:i a', strtotime($startDateTime)).' - ';
							$return .= date_i18n('g:i a', strtotime($endDateTime)) . '</h4>';

							$return .= '<div class="mz_mindbody_event_description">';
							$return .=  $image;
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
				$return .= '<h3>' . __('No events published this period.'). '</h3>';
			}
		}
		else
		{

			if(!empty($mz_event_data['GetClassesResult']['Message']))
			{
				$return .= $mz_event_data['GetClassesResult']['Message'];
			}
			else
			{
				$return .= '<p>' .$mz_event_calendar_duration .' '. __('Day Event Calendar');
				$return .=  ' '. date_i18n($mz_date_display, strtotime($mz_timeframe['StartDateTime']));
				$return .= ' - ';
				$return .= date_i18n($mz_date_display, strtotime($mz_timeframe['EndDateTime']));
				$return .= '<h3>' . __('No events published') . '. </h3>';
				$return .= mz_mbo_schedule_nav($mz_date, "Events", $mz_event_calendar_duration);
				//$return .= '<pre>'.print_r($mz_event_data,1).'</pre>';
			}

		}//EOF If Results/Else

	}
	else // no sessions set in admin
	{
		$return .= '<h2>Error: MBO Event Type IDs must be set in Admin Panel</h2>';
	}
    $return .= mz_mbo_schedule_nav($mz_date, "Events", $mz_event_calendar_duration);
	return $return;

}//EOF mZ_mindbody_show_events

?>
