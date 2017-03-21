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
				$timeframe = array_slice(mZoo\MBOAPI\Schedule_Operations::mz_getDateRange(date_i18n('Y-m-d'), 1), 0, 1);
	  		$mb = mZoo\MBOAPI\MZ_Mindbody_Init::instantiate_mbo_API();
	  		$test = $mb->GetClasses($timeframe);
				$this->assertTrue($test['GetClassesResult']['ErrorCode'] == 200);
				$this->assertTrue(is_array($test['GetClassesResult']['Classes']));
				$this->assertTrue(is_array($test['GetClassesResult']['Classes']['Class']));
				$this->assertTrue($test['GetClassesResult']['Classes']['Class'][0]['Location']['SiteID'] == '-99');
				parent::tearDown();
	}
	
	/**
	 * Confirm that schedule display method is operational
	 */
	function test_schedule_display() {
		$this->assertTrue(class_exists('mZoo\MBOAPI\Schedule_Display'));
		$schedule_display = new mZoo\MBOAPI\Schedule_Display();
		print_r($schedule_display);
	}
}
