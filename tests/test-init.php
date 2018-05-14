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
				$this->assertTrue( class_exists( 'MZ_Mindbody\Inc\Schedule\Retrieve_Schedule' ) );
				$mb = new MZ_Mindbody\Inc\Schedule\Retrieve_Schedule;
				$mb->instantiate_mbo_API();
				$options = get_option( 'mz_mindbody_options',__('Option Not Set', 'mz-mindbody-api') );
				$this->assertTrue($options == 'Option Not Set');
	}
	
}
