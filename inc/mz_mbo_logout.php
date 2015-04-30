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
}

function displayConfirmation() {
	echo "User Logged Out."
	echo "<br />";
	echo "<a href='login'>Log in</a>";
	}
?>