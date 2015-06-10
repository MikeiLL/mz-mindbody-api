<?php
function mZ_mindbody_show_schedule( $atts, $account=0 )
{
	require_once MZ_MINDBODY_SCHEDULE_DIR .'inc/mz_mbo_init.inc';
	
	global $add_mz_ajax_script;
	$add_mz_ajax_script = true;

	// optionally pass in a type parameter. Defaults to week.
	extract( shortcode_atts( array(
		'type' => 'week',
		'location' => '1',
		'account' => '0',
		'filter' => '0'
			), $atts ) );
    $mz_date = empty($_GET['mz_date']) ? date_i18n('Y-m-d') : mz_validate_date($_GET['mz_date']);

	if ($type=='day')
	{
		$mz_timeframe = array_slice(mz_getDateRange($mz_date, 1), 0, 1);
		$mz_schedule_cache = "mz_schedule_day_cache";
	}
	else
	{   
	    $mz_timeframe = array_slice(mz_getDateRange($mz_date, 7), 0, 1);
		$mz_schedule_cache = "mz_schedule_week_cache";
	}

	//While we still eed to support php 5.2 and can't use [0] on above
	$mz_timeframe = array_pop($mz_timeframe);
	
  // START caching
	$mz_cache_reset = isset($options['mz_mindbody_clear_cache']) ? "on" : "off";

	if ( $mz_cache_reset == "on" ){
		delete_transient( $mz_schedule_cache );
	}
	if (isset($_GET) || ( false === ( $mz_schedule_data = get_transient( $mz_schedule_cache ) ) ) ) {
	//Send the timeframe to the GetClasses class, unless already cached
	if ($account == 0) {
		$mz_schedule_data = $mb->GetClasses($mz_timeframe);
		}else{
		$mb->sourceCredentials['SiteIDs'][0] = $account; 
		$mz_schedule_data = $mb->GetClasses($mz_timeframe);
		}
	}
    //mz_pr($mz_schedule_data);
	//Cache the mindbody call for 24 hours
	// TODO make cache timeout configurable.
	set_transient($mz_schedule_cache, $mz_schedule_data, 60 * 60 * 24);
	// END caching

	$return = '';

	if(!empty($mz_schedule_data['GetClassesResult']['Classes']['Class']))
	{
		$mz_days = $mb->makeNumericArray($mz_schedule_data['GetClassesResult']['Classes']['Class']);
		
		$mz_days = sortClassesByDate($mz_days);

		    $return .= '<div id="mz_mbo_schedule" class="mz_mbo_schedule">';
		if ($type=='week'){
		    $return .= mz_mbo_schedule_nav($mz_date);
		}

		if ($filter == 1) {
			$return .= '<table class="mz-schedule-filter">';
			}else{
			$return .= '<table class="mz-schedule-table">';
			}

		foreach($mz_days as $classDate => $mz_classes)
		{   
			$return .= '<thead><tr><th scope="col">';
			$return .= date_i18n($mz_date_display, strtotime($classDate));
			$return .= '</th><th scope="col">' . __('Class Name') . '</th><th scope="col">' . __('Instructor') . '</th><th scope="col">' . __('Class Type') . '</th></tr></thead><tbody>';

			foreach($mz_classes as $class)
			{
				if (!(($class['IsCanceled'] == 'TRUE') && ($class['HideCancel'] == 'TRUE')) && ($class['Location']['ID'] == $location))
				{
					$sDate = date_i18n('m/d/Y', strtotime($class['StartDateTime']));
					$sLoc = $class['Location']['ID'];
					$sTG = $class['ClassDescription']['Program']['ID'];
					$studioid = $class['Location']['SiteID'];
					//$sclassid = $class['ClassScheduleID'];
					$sclassid = $class['ID'];
					$classDescription = $class['ClassDescription']['Description'];
					$sType = -7;
					$className = $class['ClassDescription']['Name'];
					$startDateTime = date_i18n('Y-m-d H:i:s', strtotime($class['StartDateTime']));
					$endDateTime = date_i18n('Y-m-d H:i:s', strtotime($class['EndDateTime']));
					$staffName = $class['Staff']['Name'];
					$sessionType = $class['ClassDescription']['SessionType']['Name'];
					$isAvailable = $class['IsAvailable'];
					$linkURL = "https://clients.mindbodyonline.com/ws.asp?sDate={$sDate}&amp;sLoc={$sLoc}&amp;sTG={$sTG}&amp;sType={$sType}&amp;sclassid={$sclassid}&amp;studioid={$studioid}";

					if (date_i18n('H', strtotime($startDateTime)) < 12) {
							$time_of_day = 'morning';
						}else if ((date_i18n('H', strtotime($startDateTime)) > 16)) {
							$time_of_day = 'evening';
						}else{
							$time_of_day = 'afternoon';
							}
					// start building table rows
					$return .= '<tr class="mz_description_holder"><td><span style="display:none">'.$time_of_day.'</span>';
					$return .= date_i18n('g:i a', strtotime($startDateTime)) . ' - ' . date_i18n('g:i a', strtotime($endDateTime));
					$return .= $isAvailable ? '<br><a class="btn" href="' . $linkURL . '" target="_blank">' . __('Sign-Up') . '</a>' : '';
					$return .= '</td><td>';

					// trigger link modal
					$return .= '<a data-toggle="modal" data-target="#mzModal" href="' . MZ_MINDBODY_SCHEDULE_URL . 'inc/modal_descriptions.php?classDescription=' . urlencode(substr($classDescription, 0, 1000)) . '&amp;className='. urlencode(substr($className, 0, 1000)) .'">' . $className . '</a>';
					$eventLinkURL = "https://clients.mindbodyonline.com/ws.asp?sDate={$sDate}&amp;sLoc={$sLoc}&amp;sTG={$sTG}&amp;sType={$sType}&amp;sclassid={$sclassid}&amp;studioid={$studioid}";
							
					$return .= '<br/><div id="visitMBO" class="btn visitMBO" style="display:none"><a href="'.$eventLinkURL.'" target="_blank">Manage on MindBody Site<a/></div>';
					$return .= '</td><td>';
					$return .= $staffName;
					$return .= '</td><td>';
					$return .= $sessionType;

					$return .= '</td></tr>';

				} // EOF if
			}// EOF foreach class
		}// EOF foreach day

		$return .= '</tbody></table>';
		if ($type=='week')
		    // schedule navigation
		    $return .= mz_mbo_schedule_nav($mz_date);
		$return .= '<div id="mzModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mzSmallModalLabel" aria-hidden="true">
                 <div class="modal-content">

				</div>
		</div>';

		$return .= '</div>';
	}
	else
	{

		if(!empty($mz_schedule_data['GetClassesResult']['Message']))
		{
			$return = $mz_schedule_data['GetClassesResult']['Message'];
		}
		else
		{
			$return = __('Error getting classes. Try re-loading the page.') . '<br />';
			$return .= '<pre>'.print_r($mz_schedule_data,1).'</pre>';
		}
	}//EOF If Result / Else

	return $return;

}//EOF mZ_show_schedule

?>
