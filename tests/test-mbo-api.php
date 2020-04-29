<?php

class Tests_MBO_Api extends WP_UnitTestCase {

	public function tearDown() {
		parent::tearDown();
	}

	public function test_get_access_token() {

        parent::setUp();

        $this->assertTrue(class_exists('MZ_Mindbody\Inc\Libraries\MBO_V6_API'));
        
        $basic_options_set = array(
            'SourceName' => MBOTests\Test_Options::$_MYSOURCENAME,
            'Password' => MBOTests\Test_Options::$_MYPASSWORD,
            'mz_mbo_app_name' => MBOTests\Test_Options::$_MYAPPNAME,
            'mz_mbo_api_key' => MBOTests\Test_Options::$_MYAPIKEY,
            'mz_mindbody_siteID' => '-99'
        );
        add_option( 'mz_mbo_basic', $basic_options_set, '', 'yes' );
		
        $basic_options = get_option('mz_mbo_basic');

        $mbo_api = new MZ_Mindbody\Inc\Libraries\MBO_V6_API($basic_options);
        
        $apikey = $basic_options['mz_mbo_api_key'];
        
        $this->assertTrue($apikey == MBOTests\Test_Options::$_MYAPIKEY);
        
        $headers = array();
		$headers['Content-Type'] = 'application/json';
		$headers['Api-Key'] = $apikey;
		$headers['Siteid'] = $basic_options['mz_mindbody_siteID'];
	
		$response = wp_remote_post( 'https://api.mindbodyonline.com/public/v6/usertoken/issue', array(
			'method' => 'POST',
			'timeout' => 45,
			'httpversion' => '1.0',
			'blocking' => true,
			'headers' => $headers,
			'body' => json_encode(array( 'Username' => 'Siteowner', 'Password' => 'apitest1234' )),
			'cookies' => array()
			)
		);

		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			return "Something went wrong: " . $error_message;
		} else {
			$this->assertTrue(is_array($response));
			$response_body = json_decode($response['body']);
			$this->assertTrue(is_object($response_body));
			$this->assertTrue(!empty($response_body->AccessToken));
		}
	}


}