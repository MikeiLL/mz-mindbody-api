<?php

//set week number based on php date or passed parameter from $_GET
//sanitize input
$mz_date_display = "D F d";
$mz_event_calendar_duration = 120;

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


?>
