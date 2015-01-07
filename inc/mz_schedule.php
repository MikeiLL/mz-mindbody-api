<?php
function mZ_mindbody_show_schedule( $atts )
{
	require_once MZ_MINDBODY_SCHEDULE_DIR .'inc/mz_mbo_init.inc';

	// optionally pass in a type parameter. Defaults to week.
	extract( shortcode_atts( array(
		'type' => 'week'
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
	$mz_schedule_data = $mb->GetClasses($mz_timeframe);
	}
    //mz_pr($mz_schedule_data);
	//Cache the mindbody call for 24 hours
	// TODO make cache timeout configurable.
	set_transient($mz_schedule_cache, $mz_schedule_data, 60 * 60 * 24);
	// END caching

	$return = '';

	if(!empty($mz_schedule_data['GetClassesResult']['Classes']['Class']))
	{
		//$return .= $mb->debug();

		$mz_days = $mb->makeNumericArray($mz_schedule_data['GetClassesResult']['Classes']['Class']);
		$mz_days = sortClassesByDate($mz_days);

		    $return .= '<div id="mz_mbo_schedule" class="mz_mbo_schedule">';
		if ($type=='week'){
		    $return .= mz_mbo_schedule_nav($mz_date);
		}
		
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
					$linkURL = "https://clients.mindbodyonline.com/ws.asp?sDate={$sDate}&amp;sLoc={$sLoc}&sTG={$sTG}&amp;sType={$sType}&amp;sclassid={$sclassid}&amp;studioid={$studioid}";
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
		if ($type=='week')
		    // schedule navigation
		    $return .= mz_mbo_schedule_nav($mz_date);

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
