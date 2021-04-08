<?php

require_once 'MZMBO_WPUnitTestCase.php';
require_once 'Test_Options.php';

/**
 * Class InitilizationTest
 *
 * @package Mz_Mindbody_Api
 */

/**
 * Sample test case.
 */
class InitilizationTest extends MZMBO_WPUnitTestCase {


	/**
	 * Can we initialize Devin's MBO Api
	 */
	function test_instantiate_api() {
		$this->assertTrue( class_exists( 'MZoo\MzMindbody\Schedule\RetrieveSchedule' ) );
		$mb = new MZoo\MzMindbody\Schedule\RetrieveSchedule();
		$mb->instantiate_mbo_api();
		$options = get_option( 'mz_mbo_basic', 'Error: No Options' );
		$this->assertTrue( Test_Options::$_MYPASSWORD == $options['mz_mindbody_password'] );
	}
}
