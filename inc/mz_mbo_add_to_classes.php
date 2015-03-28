<?php
function mz_mindbody_add_to_classes() {
	require_once MZ_MINDBODY_SCHEDULE_DIR .'inc/mz_mbo_init.inc';
	$clientID = 2260;
    	$classID = 100015619;

	$signupData = $mb->AddClientsToClassesz(array($classID), array($clientID));
	print_r($mb);
	$mb->getXMLRequest();
	$mb->getXMLResponse();
	$mb->debug();
	
	echo "<h2>Hi</h2>";
	}

?>
