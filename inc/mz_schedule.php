<?php
function mZ_mindbody_show_schedule( $atts )
{
	require_once MZ_MINDBODY_SCHEDULE_DIR .'inc/mz_mbo_init.inc';


	// optionally pass in a type parameter. Defaults to week.
	extract( shortcode_atts( array(
		'type' => 'week'
			), $atts ) );
    
	if ($type=='day')
	{
		$mz_timeframe = array('StartDateTime'=>date_i18n('Y-m-d'), 'EndDateTime'=>date_i18n('Y-m-d'), 'SchedNav'=>'');
		$mz_schedule_cache = "mz_schedule_day_cache";
	}
	else
	{
		$mz_timeframe = mz_mbo_schedule_nav($_GET);
		$mz_schedule_cache = "mz_schedule_week_cache";
	}

  // START caching
	$mz_cache_reset = isset($options['mz_mindbody_clear_cache']) ? "on" : "off";

	if ( $mz_cache_reset == "on" ){
		delete_transient( $mz_schedule_cache );
	}

	if (isset($_GET) || ( false === ( $data = get_transient( $mz_schedule_cache ) ) ) ) {
	//Send the timeframe to the GetClasses class, unless already cached
	$data = $mb->GetClasses($mz_timeframe);
	}

	//Cache the mindbody call for 24 hours
	// TODO make cache timeout configurable.
	set_transient($mz_schedule_cache, $data, 60 * 60 * 24);
	// END caching

	$return = '';

	if(!empty($data['GetClassesResult']['Classes']['Class']))
	{
		//$return .= $mb->debug();

		$mz_days = $mb->makeNumericArray($data['GetClassesResult']['Classes']['Class']);
		$mz_days = sortClassesByDate($mz_days);
		$mz_date_display = "D F d";

		$return .= '<div id="mz_mbo_schedule" class="mz_mbo_schedule">';
		$return .= $mz_timeframe['SchedNav'];
		$return .= '<table class="table table-striped">';

		foreach($mz_days as $classDate => $mz_classes)
		{
			$return .= '<tr><th>';
			$return .= date_i18n($mz_date_display, strtotime($classDate));
			$return .= '</th><th>' . __('Class Name') . '</th><th>' . __('Instructor') . '</th><th>' . __('Class Type') . '</th></tr>';

			foreach($mz_classes as $class)
			{
				if (!(($class['IsCanceled'] == 'TRUE') && ($class['HideCancel'] == 'TRUE')))
				{
					$sDate = date_i18n('m/d/Y', strtotime($class['StartDateTime']));
					$sLoc = $class['Location']['ID'];
					$sTG = $class['ClassDescription']['Program']['ID'];
					$studioid = $class['Location']['SiteID'];
					$sclassid = $class['ClassScheduleID'];
					$classDescription = $class['ClassDescription']['Description'];
					$sType = -7;
					$linkURL = "https://clients.mindbodyonline.com/ws.asp?sDate={$sDate}&sLoc={$sLoc}&sTG={$sTG}&sType={$sType}&sclassid={$sclassid}&studioid={$studioid}";
					$className = $class['ClassDescription']['Name'];
					$startDateTime = date_i18n('Y-m-d H:i:s', strtotime($class['StartDateTime']));
					$endDateTime = date_i18n('Y-m-d H:i:s', strtotime($class['EndDateTime']));
					$staffName = $class['Staff']['Name'];
					$sessionType = $class['ClassDescription']['SessionType']['Name'];
					$isAvailable = $class['IsAvailable'];

					// start building table rows
					$return .= '<tr><td>';
					$return .= date_i18n('g:i a', strtotime($startDateTime)) . ' - ' . date_i18n('g:i a', strtotime($endDateTime));
					// only show the schedule button if enabled in MBO
					$return .= $isAvailable ? '<br><a class="btn" href="' . $linkURL . '" target="_newbrowser">' . __('Sign-Up') . '</a>' : '';

					$return .= '</td><td>';

					// trigger link modal
					$return .= '<a data-toggle="modal" data-target="#mzModal" href="' . MZ_MINDBODY_SCHEDULE_URL . 'inc/modal_descriptions.php?classDescription=' . urlencode(substr($classDescription, 0, 1000)) . '&className='. urlencode(substr($className, 0, 1000)) .'">' . $className . '</a>';

					$return .= '</td><td>';
					$return .= $staffName;
					$return .= '</td><td>';
					$return .= $sessionType;

					//$return .= $classDescription;
					$return .= '</td></tr>';

				} // EOF if
			}// EOF foreach class
		}// EOF foreach day

		$return .= '</table>';

		// schedule navigation
		$return .= $mz_timeframe['SchedNav'];

		// modal-content needs to live here for dynamic loading to work
		// this still doesn't work because content is only loaded on
		// the first click.  Not sure how to force content reload each click
		$return .= '<div id="mzModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mzModalLabel" aria-hidden="true">
                 <div class="modal-content">

				</div>
		</div>';

		$return .= '</div>';
	}
	else
	{

		if(!empty($data['GetClassesResult']['Message']))
		{
			$return = $data['GetClassesResult']['Message'];
		}
		else
		{
			$return = __('Error getting classes. Try re-loading the page.') . '<br />';
			$return .= '<pre>'.print_r($data,1).'</pre>';
		}
	}//EOF If Result / Else

	return $return;

}//EOF mZ_show_schedule

function mz_mbo_schedule_nav($mz_get_variables)
{
	$sched_nav = '';
	$mz_schedule_page = get_permalink();
	//sanitize input
	//set week number based on php date or passed parameter from $_GET
	$mz_date = empty($mz_get_variables['mz_date']) ? date_i18n('Y-m-d') : mz_validate_date($mz_get_variables['mz_date']);
	//Navigate through the weeks
	$mz_start_end_date = mz_getNavDates($mz_date);
	$mz_nav_weeks_text_prev = __('Previous Week');
	$mz_nav_weeks_text_current = __('Current Week');
	$mz_nav_weeks_text_following = __('Following Week');
		$sched_nav .= ' <a href='.add_query_arg(array('mz_date' => ($mz_start_end_date[3]))).'>'.$mz_nav_weeks_text_prev.'</a>';
		$sched_nav .= ' - <a href='.$mz_schedule_page.'>'.$mz_nav_weeks_text_current.'</a> - ';
		$sched_nav .= '<a href='.add_query_arg(array('mz_date' => ($mz_start_end_date[2]))).'>'.$mz_nav_weeks_text_following.'</a>';

	$mz_timeframe = array('StartDateTime'=>$mz_start_end_date[0], 'EndDateTime'=>$mz_start_end_date[1], 'SchedNav'=>$sched_nav);

	return $mz_timeframe;
}
?>
