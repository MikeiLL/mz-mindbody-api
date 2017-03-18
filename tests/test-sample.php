<?php
/**
 * Class SampleTest
 *
 * @package Mz_Mindbody_Api
 */

/**
 * Sample test case.
 */
class SampleTest extends WP_UnitTestCase {

	/**
	 * A single example test.
	 */
	function test_sample() {
		// Replace this with some actual testing code.
		$this->assertTrue( true );
	}
	/**
	 * A single example test.
	 */
	function test_sample_two() {
		// Replace this with some actual testing code.
		$bla = new MZ_Mindbody_API_Loader();
		$shit = "shit";
		$this->assertEquals( "shit", $shit );
	}
}
