<?php

class Tests_MBO_Api extends WP_UnitTestCase {

	public function tearDown() {
		parent::tearDown();
	}

	public function test_get_access_token() {

        parent::setUp();

        $this->assertTrue(class_exists('MZ_Mindbody\Inc\Libraries\MBO_V6_API'));
        
        $basic_options_set = array(
            'mz_source_name' => MBOTests\Test_Options::$_MYSOURCENAME,
            'mz_mindbody_password' => MBOTests\Test_Options::$_MYPASSWORD,
            'mz_mbo_app_name' => MBOTests\Test_Options::$_MYAPPNAME,
            'mz_mbo_api_key' => MBOTests\Test_Options::$_MYAPIKEY,
            'mz_mindbody_siteID' => '-99'
        );
        
        add_option( 'mz_mbo_basic', $basic_options_set, '', 'yes' );
		
        $basic_options = get_option('mz_mbo_basic');

        $mbo_api = new MZ_Mindbody\Inc\Libraries\MBO_V6_API($basic_options);
        
        $result = $mbo_api->TokenIssue();
                
        $this->assertTrue(is_object($result));
                
        $this->assertTrue($result->TokenType == 'Bearer');
        
        $this->assertTrue($result->User->Type == 'Staff');
        
        $this->assertTrue( ctype_alnum($result->AccessToken) );
        
        $bad_basic_options = $basic_options;
        
        $bad_basic_options['mz_mbo_api_key'] = '';

        $bad_mbo_api = new MZ_Mindbody\Inc\Libraries\MBO_V6_API($bad_basic_options);
        
        $result_error = $bad_mbo_api->TokenIssue();
                        
        $this->assertTrue($result_error->Error->Code == 'DeniedAccess');
        
        $this->assertTrue($result_error->Error->Message == 'Missing API key');
        
	}

}