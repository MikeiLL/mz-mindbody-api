<?php
// Ajax
 define('MZ_MBO_JS_VERSION', '1.0');

//Force page protocol to match current
$protocol = isset( $_SERVER["HTTPS"]) ? 'https://' : 'http://';
 
 function mZ_add_to_classes() {
 	wp_register_script('mZ_add_to_classes', plugins_url('/mz-mindbody-api/dist/scripts/ajax-mbo-add-to-classes.js'), array('jquery'), null, true);
 	wp_enqueue_script('mZ_add_to_classes');
 	}

 //Enqueue script in footer
 add_action('wp_footer', 'mZ_add_to_classes');
 add_action('wp_footer', 'ajax_mbo_add_to_classes_js');
 	
 function ajax_mbo_add_to_classes_js() {

 	global $add_mz_ajax_script;
 	if ( ! $add_mz_ajax_script )
 		return;

	//Force page protocol to match current
	$protocol = isset( $_SERVER["HTTPS"]) ? 'https://' : 'http://';
 
 	$params = array(
		'ajaxurl' => admin_url( 'admin-ajax.php', $protocol ),
		'registered_message' => __('Registered via MindBody', 'mz-mindbody-api'),
		'not_registered_message' => __('Problem with MindBody Registration', 'mz-mindbody-api')
		);
	
	wp_localize_script( 'mZ_add_to_classes', 'mZ_add_to_classes', $params);

 	}

// Ajax

 function mZ_get_registrants() {
 	wp_register_script('mZ_get_registrants', plugins_url('/mz-mindbody-api/dist/scripts/ajax-mbo-show-registrants.js'), array('jquery'), null, true);
 	wp_enqueue_script('mZ_get_registrants');
 	}

 //Enqueue script in footer
 add_action('wp_footer', 'mZ_get_registrants');
 add_action('wp_footer', 'ajax_mbo_get_registrants_js');

 function ajax_mbo_get_registrants_js() {

 	if ( ! $add_mz_ajax_script )
 		return;

	//Force page protocol to match current
	$protocol = isset( $_SERVER["HTTPS"]) ? 'https://' : 'http://';
 
 	$params = array(
		'ajaxurl' => admin_url( 'admin-ajax.php', $protocol ),
		'registrants_list' => __('registrant list here', 'mz-mindbody-api')
		);
	
	wp_localize_script( 'mZ_get_registrants', 'mZ_get_registrants', $params);

 	}
 

 ?>