<?php

require_once 'MZMBO_WPUnitTestCase.php';
require_once 'Test_Options.php';

class Tests_TokenManagement extends MZMBO_WPUnitTestCase {


    public function tearDown() {
        parent::tearDown();
    }

    public function test_get_access_token() {

        parent::setUp();

        $this->assertTrue( class_exists( 'MZoo\MzMindbody\Common\TokenManagement' ) );

        $tm = new MZoo\MzMindbody\Common\TokenManagement();

        $token = $tm->serve_token();

        $this->assertTrue( ctype_alnum( $token ) );
    }

    public function test_stored_token_storage() {

        parent::setUp();

        $this->assertTrue( class_exists( 'MZoo\MzMindbody\Common\TokenManagement' ) );

        $tm = new MZoo\MzMindbody\Common\TokenManagement();

        $initial_token = $tm->serve_token(); // this should save and return a new token

        $tm = new MZoo\MzMindbody\Common\TokenManagement();

        $new_token = $tm->serve_token(); // this should serve saved token

        $this->assertTrue( $new_token == $initial_token );
    }
}
