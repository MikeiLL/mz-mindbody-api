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
		$mz_timeframe = array('StartDateTime'=>date('Y-m-d'), 'EndDateTime'=>date('Y-m-d'), 'SchedNav'=>'');
	}
	else
	{
		$mz_timeframe = mz_mbo_schedule_nav($_GET);
	}

	$options = get_option( 'mz_mindbody_options','Option Not Set' );
	$mz_schedule_cache = isset($options['mz_mindbody_clear_cache']) ? "on" : "off";

	if ( $mz_schedule_cache == "on" ){
	delete_transient( $mz_schedule_cache );
	}
if ( false === ( $data = get_transient( $mz_schedule_cache ) ) ) {

	//Send the timeframe to the GetClasses class, unless already cached
	$data = $mb->GetClasses($mz_timeframe);
	}
	$return = '';
	
	//Cache the mindbody call for 24 hours
	set_transient($mz_schedule_cache, $data, 60 * 60 * 24);
	
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
			$return .= date($mz_date_display, strtotime($classDate));
			$return .= '</th><th>Class Name</th><th>Instructor</th><th>Class Type</th></tr>';

			foreach($mz_classes as $class)
			{
				if (!(($class['IsCanceled'] == 'TRUE') && ($class['HideCancel'] == 'TRUE')))
				{
					$sDate = date('m/d/Y', strtotime($class['StartDateTime']));
					$sLoc = $class['Location']['ID'];
					$sTG = $class['ClassDescription']['Program']['ID'];
					$studioid = $class['Location']['SiteID'];
					$sclassid = $class['ClassScheduleID'];
					$classDescription = $class['ClassDescription']['Description'];
					$sType = -7;
					$linkURL = "https://clients.mindbodyonline.com/ws.asp?sDate={$sDate}&sLoc={$sLoc}&sTG={$sTG}&sType={$sType}&sclassid={$sclassid}&studioid={$studioid}";
					$className = $class['ClassDescription']['Name'];
					$startDateTime = date('Y-m-d H:i:s', strtotime($class['StartDateTime']));
					$endDateTime = date('Y-m-d H:i:s', strtotime($class['EndDateTime']));
					$staffName = $class['Staff']['Name'];
					$sessionType = $class['ClassDescription']['SessionType']['Name'];
					$isAvailable = $class['IsAvailable'];

					// start building table rows
					$return .= '<tr><td>';
					$return .= date('g:i a', strtotime($startDateTime)) . ' - ' . date('g:i a', strtotime($endDateTime));
					// only show the schedule button if enabled in MBO
					$return .= $isAvailable ? '<br><a class="btn" href="' . $linkURL . '" target="_newbrowser">Sign-Up</a>' : '';

					$return .= '</td><td>';

					// trigger link modal
					$return .= '<a data-toggle="modal" data-target="#mySmallModal" href="' . MZ_MINDBODY_SCHEDULE_URL . 'inc/modal_descriptions.php?classDescription=' . urlencode(substr($classDescription, 0, 1000)) . '">' . $className . '</a>';

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
		$return .= '<div id="mySmallModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-sm">
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
			$return = "__(Error getting classes. Try re-loading the page.)<br />";
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
	$mz_weeknumber = empty($mz_get_variables['mz_week']) ? date("W", strtotime(date('Y-m-d'))) : mz_validate_weeknum($mz_get_variables['mz_week']);
	//Navigate through the weeks
	$mz_nav_weeks_text_prev = __('Previous Week');
	$mz_nav_weeks_text_current = __('Current Week');
	$mz_nav_weeks_text_following = __('Following Week');
	$mz_current_year = date("Y");
	$num_weeks_in_year =  weeknumber($mz_current_year, 12, 31);
	if (($mz_weeknumber < $num_weeks_in_year) && empty($mz_get_variables['mz_next_yr']))
	{
		$mz_num_weeks_back = add_query_arg(array('mz_week' => ($mz_weeknumber - 1)));
		$mz_num_weeks_forward = add_query_arg(array('mz_week' => ($mz_weeknumber + 1)));
		$sched_nav .= ' <a href='.$mz_num_weeks_back.'>'.$mz_nav_weeks_text_prev.'</a>';
		$sched_nav .= ' - <a href='.$mz_schedule_page.'>'.$mz_nav_weeks_text_current.'</a> - ';
		$sched_nav .= '<a href='.$mz_num_weeks_forward.'>'.$mz_nav_weeks_text_following.'</a>';
		$mz_start_end_date = getStartandEndDate($mz_weeknumber,$mz_current_year);
	}
	else
	{   //BOF following year
		$mz_next_year = isset($mz_get_variables['mz_next_yr']) ? mz_validate_year($mz_get_variables['mz_next_yr']) : "1";
		$mz_weeknumber = ($mz_weeknumber > 40) ? $mz_weeknumber - ($num_weeks_in_year - 1) : $mz_weeknumber;
		$from_the_future_backwards = ($mz_weeknumber == 2) ? $num_weeks_in_year : ($mz_weeknumber - 1);
		$mz_num_weeks_forward = add_query_arg(array('mz_week' => ($mz_weeknumber + 1), 'mz_next_yr' => ($mz_current_year + 1)));
		if ($mz_weeknumber == 1)
		{//if we are in first week of year
			$mz_num_weeks_back = add_query_arg(array('mz_week' => ($num_weeks_in_year - 1)));
			$sched_nav .= ' <a href='.$mz_num_weeks_back.'>'.$mz_nav_weeks_text_prev.'</a>';
		}
		else
		{
			$mz_num_weeks_back = add_query_arg(array('mz_week' => ($mz_weeknumber - 1), 'mz_next_yr' => ($mz_current_year + 1)));
			$sched_nav .= ' <a href='.$mz_num_weeks_back.'>'.$mz_nav_weeks_text_prev.'</a>';
		}
		$sched_nav .= ' - <a href='.$mz_schedule_page.'>'.$mz_nav_weeks_text_current.'</a> - ';
		$sched_nav .= '<a href='.$mz_num_weeks_forward.'>'.$mz_nav_weeks_text_following.'</a> ';
		$mz_start_end_date = getStartandEndDate($mz_weeknumber,($mz_current_year +1));
	}//EOF Following Year

	$mz_timeframe = array('StartDateTime'=>$mz_start_end_date[0], 'EndDateTime'=>$mz_start_end_date[1], 'SchedNav'=>$sched_nav);

	return $mz_timeframe;
}
?>
