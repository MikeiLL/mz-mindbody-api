<?php
/**
 * Class SampleTest
 *
 * @package Mz_Mindbody_Api
 * Check https://github.com/rnagle/wordpress-unit-tests/blob/master/includes/factory.php for details on WP  unit test factory
 */

/**
 * Sample test case.
 */
class ScheduleDisplayTest extends WP_UnitTestCase {
	
	/**
	 * Test Results from MBO Sandbox account
	 */
	function test_get_classes_result() {			
		parent::setUp();	
		$options = array(
			'mz_source_name'=>MBOTests\Test_Options::$_MYSOURCENAME,
			'mz_mindbody_password'=>MBOTests\Test_Options::$_MYPASSWORD,
			'mz_mindbody_siteID'=>'-99',
			'mz_mindbody_eventID'=>''
		);
		add_option( 'mz_mindbody_options', $options, '', 'yes' );
		var_dump(MZ_Mindbody\Inc\Core\Init::$options);
		$schedule_object = new MZ_Mindbody\Inc\Schedule\Retrieve_Schedule;
		$time_frame = $schedule_object->time_frame();
        $this->assertTrue(count($time_frame) === 2);
	  	$response = $schedule_object->get_mbo_results();
		$this->assertTrue(is_array($response));
		var_dump($response);
        $this->assertTrue($response['GetClassesResult']['ErrorCode'] == 200);
		//$this->assertTrue(is_array($mbo['GetClassesResult']['Classes']));
		//$this->assertTrue(is_array($mbo['GetClassesResult']['Classes']['Class']));
		//$this->assertTrue($mbo['GetClassesResult']['Classes']['Class'][0]['Location']['SiteID'] == '-99');
		parent::tearDown();
	}
	
	/**
	 * Confirm that schedule display method is operational
	 */
	//function test_schedule_display() {			
	//	parent::setUp();	
	//	$this->assertTrue(class_exists('mZoo\MBOAPI\Schedule_Display'));
	//	$schedule_display = new mZoo\MBOAPI\Schedule_Display();
	//	$something_else = $schedule_display->mZ_mindbody_show_schedule(array('type' => 'week'));
	//	print_r($something_else);
	//	parent::tearDown();
	//}
}

