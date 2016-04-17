<?php
// Ajax
 define('MZ_MBO_JS_VERSION', '1.0');

//Force page protocol to match current
$protocol = isset( $_SERVER["HTTPS"]) ? 'https://' : 'http://';

// Start Ajax Add to classes  
 function mZ_add_to_classes() {
 	wp_register_script('mZ_add_to_classes', plugins_url('/mz-mindbody-api/dist/scripts/ajax-mbo-add-to-classes.js'), array('jquery'), null, true);
 	wp_enqueue_script('mZ_add_to_classes');
 	}

 //Enqueue script in footer
 add_action('wp_footer', 'mZ_add_to_classes');
 add_action('wp_footer', 'ajax_mbo_add_to_classes_js');
 	
 function ajax_mbo_add_to_classes_js() {

	//Force page protocol to match current
	$protocol = isset( $_SERVER["HTTPS"]) ? 'https://' : 'http://';
 
 	$params = array(
		'ajaxurl' => admin_url( 'admin-ajax.php', $protocol ),
		'registered_message' => __('Registered via MindBody', 'mz-mindbody-api'),
		'not_registered_message' => __('Problem with MindBody Registration', 'mz-mindbody-api')
		);
	
	wp_localize_script( 'mZ_add_to_classes', 'mZ_add_to_classes', $params);

 	}
// End Ajax Add to classes 

// Start Ajax Get Registrants 
 function mZ_get_registrants() {
 	wp_register_script('mZ_get_registrants', plugins_url('/mz-mindbody-api/dist/scripts/ajax-mbo-show-registrants.js'), array('jquery'), null, true);
 	wp_enqueue_script('mZ_get_registrants');
 	}

 //Enqueue script in footer
 add_action('wp_footer', 'mZ_get_registrants');
 add_action('wp_footer', 'ajax_mbo_get_registrants_js');

 function ajax_mbo_get_registrants_js() {

	//Force page protocol to match current
	$protocol = isset( $_SERVER["HTTPS"]) ? 'https://' : 'http://';
 
 	$params = array(
		'ajaxurl' => admin_url( 'admin-ajax.php', $protocol ),
		'get_registrants_error' => __('Error retreiving class details.', 'mz-mindbody-api'),
		'registrants_header' => __('Registrants', 'mz-mindbody-api'),
		'staff_preposition' => __('with', 'mz-mindbody-api')
		);
	
	wp_localize_script( 'mZ_get_registrants', 'mZ_get_registrants', $params);

 	}
 // End Ajax Get Registrants 

// Start Ajax Is Logged In  
  function mZ_check_session_logged() {
 	wp_register_script('mZ_check_session_logged', plugins_url('/mz-mindbody-api/dist/scripts/ajax-mbo-check-logged.js'), array('jquery'), null, true);
 	wp_enqueue_script('mZ_check_session_logged');
 	}

 //Enqueue script in footer
 add_action('wp_footer', 'mZ_check_session_logged');
 add_action('wp_footer', 'mz_mbo_check_session_logged');

 function mz_mbo_check_session_logged() {

	//Force page protocol to match current
	$protocol = isset( $_SERVER["HTTPS"]) ? 'https://' : 'http://';
 
 	$params = array(
		'ajaxurl' => admin_url( 'admin-ajax.php', $protocol ),
		'signup' => __('Sign-up', 'mz-mindbody-api'),
		'login' => __('Login', 'mz-mindbody-api'),
		'login_title' => __('Login to Sign-up', 'mz-mindbody-api')
		);
	
	wp_localize_script( 'mZ_check_session_logged', 'mZ_check_session_logged', $params);

 	}
// End Ajax Is Logged In 

 ?>