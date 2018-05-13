<?php
/**
 * Class InitilizationTest
 *
 * @package Mz_Mindbody_Api
 */

/**
 * Sample test case.
 */
class InitilizationTest extends WP_UnitTestCase {

	/**
	 * Can we initialize Devin's MBO Api
	 */
	function test_instantiate_api() {
				$this->assertTrue( class_exists( 'MZ_Mindbody\Inc\Core\MBO_Init' ) );
				$mb = MZ_Mindbody\Inc\Core\MBO_Init::instantiate_mbo_API();
				$options = get_option( 'mz_mindbody_options',__('Option Not Set', 'mz-mindbody-api') );
				$this->assertTrue($options == 'Option Not Set');
	}
	
	
	/**
	 * Test connection with MBO Sandbox account
	 */
	function test_get_mbo_results() {			
			parent::setUp();	
			$options = array(
				'mz_source_name'=>MBOTests\Test_Options::$_MYSOURCENAME,
				'mz_mindbody_password'=>MBOTests\Test_Options::$_MYPASSWORD,
				'mz_mindbody_siteID'=>'-99',
				'mz_mindbody_eventID'=>MBOTests\Test_Options::$_MYEVENTIDS
			);
			add_option( 'mz_mindbody_options', $options, '', 'yes' );
			$this->assertTrue( class_exists( 'MZ_Mindbody\Inc\Schedule\Retrieve_Schedule' ));
			$schedule_object = new MZ_Mindbody\Inc\Schedule\Retrieve_Schedule;
	  		$mbo = $schedule_object->get_mbo_results();
			$this->assertTrue($mbo['GetClassesResult']['ErrorCode'] == 200);
			parent::tearDown();
	}
	
}
