<?php
function mZ_mindbody_show_schedule() {
require_once MZ_MINDBODY_SCHEDULE_DIR .'/mindbody-api/MB_API.php';
$mb = new my_MB_API();
$options = get_option( 'mz_mindbody_options','Error: Mindbody Credentials Not Set' );
$sourceCredentials = array(
		"SourceName"=>$options['mz_source_name'], 
		"Password"=>$options['mz_mindbody_password'], 
		"SiteIDs"=>array($options['mz_mindbody_siteID'])
	);
$mb->setCreds($sourceCredentials);

	$mz_timeframe = mz_mbo_schedule_nav($_GET);
	//Send the timeframe to the GetClasses class
	$data = $mb->GetClasses($mz_timeframe);
	if(!empty($data['GetClassesResult']['Classes']['Class'])) {
	$mz_days = $mb->makeNumericArray($data['GetClassesResult']['Classes']['Class']);
	$mz_days = sortClassesByDate($mz_days);
	$mz_date_display = "D F d";
	?><div id="mz_mbo_schedule" class="mz_mbo_schedule"><table class="table table-striped"><?php
	foreach($mz_days as $classDate => $mz_classes) {
		?><tr><th><?php
		echo date($mz_date_display, strtotime($classDate));
		?></th><th>Class Name</th><th>Instructor</th><th>Class Type</th></tr><?php
		foreach($mz_classes as $class) {
		if (($class['IsCanceled'] == 'TRUE') && ($class['HideCancel'] == 'TRUE'))
		{
			continue; //skip this and move to next iteration
		}  
		?><tr><?php
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
			echo "<td>".date('g:i a', strtotime($startDateTime))." - ".date('g:i a', strtotime($endDateTime))."<a class='btn' href='{$linkURL}' target='_newbrowser'>Sign-Up</a></td><td>";?>
<!-- Link trigger modal -->				
<a data-toggle="modal" data-target="#myModal" href="<?php echo MZ_MINDBODY_SCHEDULE_URL ?>inc/modal_descriptions.php?classDescription=<?php echo urlencode(substr($classDescription, 0, 1000)) ?>"> <?php echo $className ?></a>

<!-- Small modal -->

<div id="myModal" class='modal fade bs-example-modal-sm' tabindex='-1' role='dialog' aria-labelledby='mySmallModalLabel' aria-hidden='true'>

</div>

</td><td><?php echo" {$staffName} </td><td>".$sessionType."</td>";
					?></tr>
			<?php
					}
				}
				?>		
	</table>    
	<?php mz_mbo_schedule_nav($_GET); ?>
	</div>


	<?php
} else {
		if(!empty($data['GetClassesResult']['Message'])) {
			echo $data['GetClassesResult']['Message'];
		} else {
			echo "__(Error getting classes. Try re-loading the page.)<br />";
			echo '<pre>'.print_r($data,1).'</pre>';
		}
	}//EOF If Result / Else

}//EOF mZ_show_schedule

function mz_mbo_schedule_nav($mz_get_variables)
{
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
			echo ' <a href='.$mz_num_weeks_back.'>'.$mz_nav_weeks_text_prev.'</a>'; 
			echo ' - <a href='.$mz_schedule_page.'>'.$mz_nav_weeks_text_current.'</a> - ';
			echo '<a href='.$mz_num_weeks_forward.'>'.$mz_nav_weeks_text_following.'</a>';
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
				echo ' <a href='.$mz_num_weeks_back.'>'.$mz_nav_weeks_text_prev.'</a>';
			}
			else 
			{
				$mz_num_weeks_back = add_query_arg(array('mz_week' => ($mz_weeknumber - 1), 'mz_next_yr' => ($mz_current_year + 1)));
				echo ' <a href='.$mz_num_weeks_back.'>'.$mz_nav_weeks_text_prev.'</a>';
			} 
			echo ' - <a href='.$mz_schedule_page.'>'.$mz_nav_weeks_text_current.'</a> - ';
			echo '<a href='.$mz_num_weeks_forward.'>'.$mz_nav_weeks_text_following.'</a> ';	
		$mz_start_end_date = getStartandEndDate($mz_weeknumber,($mz_current_year +1));
		}//EOF Following Year
		$mz_timeframe = array('StartDateTime'=>$mz_start_end_date[0], 'EndDateTime'=>$mz_start_end_date[1]);
		return $mz_timeframe;
}		
?>