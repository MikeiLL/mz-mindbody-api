<?php

$mz_date_display = "D F d";
$options = get_option('mz_mindbody_options');
$mz_event_calendar_duration = isset($options['mz_mindbody_eventsDuration']) ? $options['mz_mindbody_eventsDuration'] : '60';
$time_format = get_option('time_format');
$date_format = get_option('date_format');
	
function instantiate_mbo_API () {
	mz_pr("OKAY We ARE DOING IT.");
	$options = get_option( 'mz_mindbody_options','Error: Mindbody Credentials Not Set' );
	if ($options != 'Error: Mindbody Credentials Not Set') {
		$mb  = new MB_API(array(
							"SourceName" => $options['mz_source_name'],
							'Password' => $options['mz_mindbody_password'],
							'SiteIDs' => array($options['mz_mindbody_siteID'])
						)); 
		}else{
			echo '<div class="error">Mindbody Credentials Not Set</div>';
			$mb  = new MB_API(array(
							"SourceName" => '',
							'Password' => '',
							'SiteIDs' => array('')
						)); 
		}
	return $mb;
	}


?>
