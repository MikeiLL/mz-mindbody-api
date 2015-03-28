<?php
function mz_mindbody_add_to_classes() {
?>
<h2>Hi</h2>
<?php
	require_once MZ_MINDBODY_SCHEDULE_DIR .'inc/mz_mbo_init.inc';
	$clientID = 2260;
    	$classID = 100015619;

	$signupData = $mb->AddClientsToClassesz(array($classID), array($clientID));
	$mb->getXMLRequest();
	$mb->getXMLResponse();
	$mb->debug();
	}

?>
