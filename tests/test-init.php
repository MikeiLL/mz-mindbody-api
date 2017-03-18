<?php
/**
 * Class SampleTest
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
				$this->assertTrue( class_exists( 'MZ_Mindbody_Init' ) );
	}
}
