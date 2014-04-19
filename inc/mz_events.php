<?php
 function mZ_mindbody_show_events (){
$plugins_url = plugins_url('', __FILE__ );

$options = get_option( 'mz_mindbody_options','Error: Mindbody Credentials Not Set' );

$mb = new MB_API(array(
    "SourceName" => $options['mz_source_name'],
    'Password' => $options['mz_mindbody_password'],
    'SiteIDs' => array($options['mz_mindbody_siteID'])
));
		$monthnumber = empty($_GET['mz_month']) ? date("m", strtotime(date('Y-m-d'))) : $_GET['mz_month'];
		$mz_schedule_page = get_permalink();
	if ($monthnumber != date('m'))
	{
	$mz_months_previous = add_query_arg(array('mz_month' => ($monthnumber - 2)));
	echo ' <a href='.$mz_months_previous.'>__(Previous)</a>';
	}
	//echo ' <a href='.$mz_schedule_page.'>Current Events</a> ';
	$start_end_date = getNextSixty($monthnumber,date("Y"));
	$timeframe = array('StartDateTime'=>$start_end_date[0], 'EndDateTime'=>$start_end_date[1]);
	$data = $mb->GetClasses($timeframe);
	if(!empty($data['GetClassesResult']['Classes']['Class'])) {
	$classes = $mb->makeNumericArray($data['GetClassesResult']['Classes']['Class']);
	$classes = sortClassesByDate($classes);?>
<div id="mz_mindbody_events" class="mz_mindbody_events"><table class="table mz_mindbody_events"><?php
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
			?><h3><?php echo $className ?></h3>
			<p>with <?php echo $staffName ?></p>
			<p><?php echo "$day_and_date" ?>
			<?php echo date('g:i a', strtotime($startDateTime))." - ".date('g:i a', strtotime($endDateTime));?></p>
			<img src="<?php echo $image ?>" class="mz_mindbody_events">
			<p><?php echo $classDescription ?></p>
			<a class='btn' href='<?php echo $eventLinkURL ?>'>Sign-Up</a>

</td></tr>
<?php
		}
	}
		if ($number_of_events < 1)
			echo "<h3>No events published beyond ".$start_end_date[0]."</h3>";
		else {
			$mz_months_future = add_query_arg(array('mz_month' => ($monthnumber + 2)));
			echo '<hr><h3><a href='.$mz_months_future.'>__(Future Events)</a></h3>';
			}
	?>
	</table>
	</div>


	<?php
} else {
			if(!empty($data['GetClassesResult']['Message'])) {
				echo $data['GetClassesResult']['Message'];
			} else {
				echo "Error getting classes<br />";
				echo '<pre>'.print_r($data,1).'</pre>';
			}
		}//EOF If Results/Else
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
