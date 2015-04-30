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
	displayConfirmation();
}

function displayConfirmation() {
	echo "<h3>User Logged Out.</h3>";
	echo "<br/>";
	echo "<a href='".home_url()."/login' class='btn mz_add_to_class'>Log in</a>";
	}
?>