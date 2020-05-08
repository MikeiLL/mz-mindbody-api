<?php
require_once('Test_Options.php');

/**
 * Class MZMBO_WPUnitTestCase
 *
 * @package Mz_Mindbody_Api
 */

/**
 * Add a logging method to WP UnitTestCase Class.
 */
abstract class MZMBO_WPUnitTestCase extends \WP_UnitTestCase {

	public function el($message){
		file_put_contents('./log_'.date("j.n.Y").'.log', $message, FILE_APPEND);
	}
	
	public static function setUpBeforeClass(){
		//global vars setup
		$basic_options_set = array(
			'mz_source_name' => Test_Options::$_MYSOURCENAME,
			'mz_mindbody_password' => Test_Options::$_MYPASSWORD,
			'mz_mbo_app_name' => Test_Options::$_MYAPPNAME,
			'mz_mbo_api_key' => Test_Options::$_MYAPIKEY,
			'sourcename_not_staff' => 'on',
			'mz_mindbody_siteID' => '-99'
		);
	
		add_option( 'mz_mbo_basic', $basic_options_set, '', 'yes' );
		
        $tm = new MZ_Mindbody\Inc\Common\Token_Management;
        
        $token = $tm->serve_token();
		
		Test_Options::$_MYACCESSTOKEN = $token;
		
		$current = new \DateTime();
		$current->format('Y-m-d H:i:s');
	
		update_option('mz_mbo_token', [
			'stored_time' => $current, 
			'AccessToken' => Test_Options::$_MYACCESSTOKEN
		]);
	}
}