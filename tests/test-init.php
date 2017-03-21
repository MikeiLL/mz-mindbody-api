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
				$this->assertTrue( class_exists( 'mZoo\MBOAPI\MZ_Mindbody_Init' ) );
				$mb = mZoo\MBOAPI\MZ_Mindbody_Init::instantiate_mbo_API();
				$options = get_option( 'mz_mindbody_options',__('Option Not Set', 'mz-mindbody-api') );
				$this->assertTrue($options == 'Option Not Set');
	}
	
	
	/**
	 * Test connection with MBO Sandbox account
	 */
	function test_connect_api() {			
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
				parent::tearDown();
	}
	
}
