<?php
function mZ_mindbody_logout() {
session_start();
foreach($_SESSION as $key => $value) {
	unset($_SESSION[$key]);
}
header('location:index.php');
}
?>