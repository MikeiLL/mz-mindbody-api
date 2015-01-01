<?php

//set week number based on php date or passed parameter from $_GET
//sanitize input
$mz_date_display = "D F d";
$mz_event_calendar_duration = 60;

$options = get_option( 'mz_mindbody_options','Error: Mindbody Credentials Not Set' );

$mb = new MB_API(array(
 "SourceName" => $options['mz_source_name'],
   
                        'Password' => $options['mz_mindbody_password'],
   
                        'SiteIDs' => array($options['mz_mindbody_siteID'])
));
                        

?>
