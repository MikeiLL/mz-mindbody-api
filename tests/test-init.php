<?php
require_once('MZMBO_WPUnitTestCase.php');
require_once('Test_Options.php');

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
    function test_instantiate_api()
    {
        $this->assertTrue(class_exists('MZ_Mindbody\Inc\Schedule\Retrieve_Schedule'));
        $mb = new MZ_Mindbody\Inc\Schedule\Retrieve_Schedule;
        $mb->instantiate_mbo_API();
        $options = get_option('mz_mbo_basic', 'Error: No Options');
        $this->assertTrue(Test_Options::$_MYPASSWORD == $options['mz_mindbody_password']);
    }
	
}
