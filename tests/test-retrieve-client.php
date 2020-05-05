<?php
require_once('MZMBO_WPUnitTestCase.php');
require_once('Test_Options.php');

class Tests_Retrieve_Client extends MZMBO_WPUnitTestCase {

	public function tearDown() {
		parent::tearDown();
	}

	public function test_get_signup_form_fields() {

        parent::setUp();

        $this->assertTrue(class_exists('MZ_Mindbody\Inc\Client\Retrieve_Client'));
        		                
        $client_object = new MZ_Mindbody\Inc\Client\Retrieve_Client;
        
	  	$response = $client_object->get_mbo_results();
	  	
	  	$this->assertTrue($response);
        
        $required_fields = $client_object->get_signup_form_fields();
                
        $this->assertTrue(in_array('Email', $required_fields ));            
        $this->assertTrue(in_array('FirstName', $required_fields ));        
        $this->assertTrue(in_array('LastName', $required_fields ));
	}
	
	public function test_add_client() {

        parent::setUp();
        		                
        $client_object = new MZ_Mindbody\Inc\Client\Retrieve_Client;
        
        $required_fields = $client_object->get_signup_form_fields();
        
        $user_data = array();
        
        // Merge testa data with random data for additional required fields
        
        $length = 7; // of random string
        
		foreach ( $required_fields as $k => $v ) {
        	switch ($v) {
        		case 'FirstName':
        			$user_data['FirstName'] = Test_Options::$_FIRSTNAME;
        			break;
        		case 'LastName':
        			$user_data['LastName'] = Test_Options::$_LASTNAME;
        			break;
        		case 'Email':
        			$user_data['Email'] = Test_Options::$_CLIENTEMAIL;
        			break;
        		case 'State':
        			$user_data['State'] = Test_Options::$_CLIENTSTATE;
        			break;
        		case 'BirthDate':
        			$user_data['BirthDate'] = Test_Options::$_CLIENTBIRTHDATE;
        			break;
        		default:
        			$user_data[$v] = $v . '_' . substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1, $length);
				break;
        	}
        }
        
        $new_client = $client_object->add_client($user_data);
        
        if ( !empty($new_client['Client']['Id']) ) {
        	Test_Options::$_CLIENTID = $new_client['Client']['Id']; //100015679
        }
        
        $this->assertTrue(is_array($new_client));
        $this->assertTrue(is_string(Test_Options::$_CLIENTID));
        $this->assertTrue($new_client['Client']['Email'] == Test_Options::$_CLIENTEMAIL);
                
	}
	
	public function test_password_reset_email_request() {

        parent::setUp();
        		                
        $client_object = new MZ_Mindbody\Inc\Client\Retrieve_Client;
        
        $user_data = [
        	'UserEmail' => Test_Options::$_CLIENTEMAIL,
       		'UserFirstName' => Test_Options::$_FIRSTNAME,
       		'1UserLastName' => Test_Options::$_LASTNAME
        ];
        
        $client_reset_request = $client_object->password_reset_email_request($user_data);
        
        $this->assertTrue($client_reset_request['Error']['Code'] == 'MissingRequiredFields');
        
        if ( empty(Test_Options::$_CLIENTPASSWORD) ) {
			$user_data = [
				'UserEmail' => Test_Options::$_CLIENTEMAIL,
				'UserFirstName' => Test_Options::$_FIRSTNAME,
				'UserLastName' => Test_Options::$_LASTNAME
			];
			$client_reset_request = $client_object->password_reset_email_request($user_data);
        	$this->assertTrue(empty($client_reset_request));
        }
	}

}