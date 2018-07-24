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
class StaffDisplayTest extends WP_UnitTestCase {
	
	/**
	 * Test Results from MBO Sandbox account
	 */
	function test_get_staff_result() {
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
        $this->assertTrue(class_exists('MZ_Mindbody\Inc\Staff\Retrieve_Staff'));
		$staff_object = new MZ_Mindbody\Inc\Staff\Retrieve_Staff;
	  	$response = $staff_object->get_mbo_results();
		$this->assertTrue(is_array($response));
        $this->assertTrue($response['GetStaffResult']['ErrorCode'] == 200);
		$this->assertTrue(is_array($response['GetStaffResult']['StaffMembers']));
		$this->assertTrue(is_array($response['GetStaffResult']['StaffMembers']['Staff']));
		parent::tearDown();
	}

    /**
     * Confirm that schedule display method is operational
     */
    function test_sort_staff_by_sort_order() {

        parent::setUp();

        $basic_options = array(
            'mz_source_name' => MBOTests\Test_Options::$_MYSOURCENAME,
            'mz_mindbody_password' => MBOTests\Test_Options::$_MYPASSWORD,
            'mz_mindbody_siteID' => '-99'
        );
        add_option( 'mz_mbo_basic', $basic_options, '', 'yes' );

        $this->assertTrue(class_exists('MZ_Mindbody\Inc\Staff\Retrieve_Staff'));

        $staff_object = new MZ_Mindbody\Inc\Staff\Retrieve_Staff;

        $response = $staff_object->get_mbo_results();

        $this->assertTrue(is_array($response));

        $sorted_staff = $staff_object->sort_staff_by_sort_order();

        /*
         * Each subsequent staff member should have a
         *  1. SortOrder that is ge previous
         *  2. LastName that is le previous
         */
        foreach ($sorted_staff as $key => $staff){
            // Define starting points
            if (!isset($lastSortOrder)) {
                $previousSortOrder = $staff->SortOrder;
                $previousLastName = $staff->LastName;
            }
            $this->assertTrue( $staff->SortOrder >= $previousSortOrder );
            $this->assertTrue( $staff->LastName >= $previousLastName );
            $previousSortOrder = $staff->SortOrder;
            $previousLastName = $staff->LastName;
        }

        parent::tearDown();
    }

}

