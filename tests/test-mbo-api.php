<?php

require_once('MZMBO_WPUnitTestCase.php');
require_once('Test_Options.php');

class Tests_MBO_Api extends MZMBO_WPUnitTestCase
{

    public function tearDown()
    {
        parent::tearDown();
    }

    public function test_get_access_token()
    {

        parent::setUp();

        $this->assertTrue(class_exists('MZoo\MzMindbody\Libraries\MboV6Api'));

        $basic_options = get_option('mz_mbo_basic');

        $mbo_api = new MZoo\MzMindbody\Libraries\MboV6Api($basic_options);

        $result = $mbo_api->TokenIssue();

        $this->assertTrue(ctype_alnum($result->AccessToken));

        $basic_options_set = array(
            'mz_source_name' => Test_Options::$_MYSOURCENAME,
            'mz_source_name' => Test_Options::$_MYSOURCENAME,
            'mz_mindbody_password' => 'sdfg',
            'mz_mbo_app_name' => 'sdfg',
            'mz_mbo_api_key' => 'wert',
            'sourcename_not_staff' => 'on',
            'mz_mindbody_siteID' => '-99'
        );

        update_option('mz_mbo_basic', $basic_options_set, '', 'yes');

        update_option('mz_mbo_token', []);

        $bad_basic_options = get_option('mz_mbo_basic');

        $bad_mbo_api = new MZoo\MzMindbody\Libraries\MboV6Api($bad_basic_options);

        $result_error = $bad_mbo_api->TokenIssue();

        $this->assertTrue($result_error->Error->Code == 'DeniedAccess');
    }
}
