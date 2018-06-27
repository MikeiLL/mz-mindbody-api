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
		$basic_options = array(
			'mz_source_name' => MBOTests\Test_Options::$_MYSOURCENAME,
			'mz_mindbody_password' => MBOTests\Test_Options::$_MYPASSWORD,
			'mz_mindbody_siteID' => '-99'
		);
        add_option( 'mz_mbo_basic', $basic_options, '', 'yes' );
        $this->assertTrue(class_exists('MZ_Mindbody\Inc\Core\MZ_Mindbody_Api'));
        // Manually initialize static variable.
		MZ_Mindbody\Inc\Core\MZ_Mindbody_Api::$basic_options = get_option('mz_mbo_basic');
        $this->assertTrue(class_exists('MZ_Mindbody\Inc\Schedule\Retrieve_Schedule'));
		$schedule_object = new MZ_Mindbody\Inc\Schedule\Retrieve_Schedule;
		$time_frame = $schedule_object->time_frame();
        $this->assertTrue(count($time_frame) === 2);
	  	$response = $schedule_object->get_mbo_results();
		$this->assertTrue(is_array($response));
        $this->assertTrue($response['GetClassesResult']['ErrorCode'] == 200);
		$this->assertTrue(is_array($response['GetClassesResult']['Classes']));
		$this->assertTrue(is_array($response['GetClassesResult']['Classes']['Class']));
		$this->assertTrue($response['GetClassesResult']['Classes']['Class'][0]['Location']['SiteID'] == '-99');
		parent::tearDown();
	}

    /**
     * Confirm that horizontal schedule display method is operational
     */
    function test_sort_classes_by_date_then_time() {

        parent::setUp();

        $basic_options = array(
            'mz_source_name' => MBOTests\Test_Options::$_MYSOURCENAME,
            'mz_mindbody_password' => MBOTests\Test_Options::$_MYPASSWORD,
            'mz_mindbody_siteID' => '-99'
        );
        add_option( 'mz_mbo_basic', $basic_options, '', 'yes' );

        $this->assertTrue(class_exists('MZ_Mindbody\Inc\Schedule\Retrieve_Schedule'));

        $schedule_object = new MZ_Mindbody\Inc\Schedule\Retrieve_Schedule;

        $response = $schedule_object->get_mbo_results();

        $sequenced_classes = $schedule_object->sort_classes_by_date_then_time();

        /*
         * Test that first date is today
         */
        $key = reset($sequenced_classes);
        $first = array_shift($key);
        $first_event_date = date('Y-m-d', strtotime($first->startDateTime));
        $now = date('Y-m-d', current_time( 'timestamp' ));
        $this->assertTrue($first_event_date == $now);

        /*
         * Each subsequent date should be equal to or greater than current,
         * which is set according to first date in the matrix.
         */
        foreach ($sequenced_classes as $date => $class){
            $current = isset($current) ? $current : $date;
            $this->assertTrue( $date >= $current );
        }

        parent::tearDown();
    }

    /**
     * Confirm that grid schedule display method is operational
     */
    function test_sort_classes_by_time_then_date() {

        parent::setUp();

        $basic_options = array(
            'mz_source_name' => MBOTests\Test_Options::$_MYSOURCENAME,
            'mz_mindbody_password' => MBOTests\Test_Options::$_MYPASSWORD,
            'mz_mindbody_siteID' => '-99'
        );
        add_option( 'mz_mbo_basic', $basic_options, '', 'yes' );

        $this->assertTrue(class_exists('MZ_Mindbody\Inc\Schedule\Retrieve_Schedule'));

        $schedule_object = new MZ_Mindbody\Inc\Schedule\Retrieve_Schedule;

        $response = $schedule_object->get_mbo_results();

        $sequenced_classes = $schedule_object->sort_classes_by_time_then_date();

        /*
         * Each subsequent time slot should be equal to or greater than current,
         * which is set according to first time-like key in the matrix.
         */
        foreach ($sequenced_classes as $time_like_key => $class){
            $current = isset($current) ? $current : $time_like_key;
            $this->assertTrue( $time_like_key >= $time_like_key );
            $this->assertTrue( is_array($class['classes']) );
            // Is it an array of seven days?
            $this->assertTrue( count($class['classes']) === 7 );
        }

        parent::tearDown();
    }
}

