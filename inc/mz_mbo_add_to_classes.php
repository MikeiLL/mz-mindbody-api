<?php
?>
<!--<script type='text/javascript'>
/* <![CDATA[ */
var mz_mbo_params = {"ajaxurl":"http:\/\/localhost:8888\/wp-admin\/admin-ajax.php"};
/* ]]> */
</script>-->
<?php
function mz_mindbody_add_to_classes() {
	require_once MZ_MINDBODY_SCHEDULE_DIR .'inc/mz_mbo_init.inc';
	$clientID = 2260;
    	$classID = 100015619;

	$signupData = $mb->AddClientsToClasses(array($classID), array($clientID));

	$mb->getXMLRequest();
	$mb->getXMLResponse();
	$mb->debug();
	
	}

?>
