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
	$globals = new Global_Strings();
	$global_strings = $globals->translate_them();
	$login_url = $global_strings['login_url'];
	$login = $global_strings['login'];
	$return = '<h3>'.__('User Logged Out.', 'mz-mindbody-api').'</h3>';
	$return .= '<br/>';
	$return .= '<a href="'.home_url().'/'.$login_url.'" class="btn mz_add_to_class">'.$login.'</a>';
	return $return;
	}
?>