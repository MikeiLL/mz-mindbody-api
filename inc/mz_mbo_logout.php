<?php
function mZ_mindbody_logout() {
	if (phpversion() >= 5.4) {
			if (session_status() == PHP_SESSION_NONE) {
				session_start();
				}
			}else{
			if(!session_id()) {
				session_start();
				}
			}
	foreach($_SESSION as $key => $value) {
		unset($_SESSION[$key]);
	}
	return displayConfirmation();
}

function displayConfirmation() {
	$return = "<h3>User Logged Out.</h3>";
	$return .= "<br/>";
	$return .= "<a href='".home_url()."/login' class='btn mz_add_to_class'>Log in</a>";
	return $return;
	}
?>