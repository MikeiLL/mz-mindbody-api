<?php
require_once('MZMBO_WPUnitTestCase.php');
require_once('Test_Options.php');

class Tests_Session extends MZMBO_WPUnitTestCase {

    function setUp() {

        parent::setUp();

        global $wpdb;

        $collate = '';
        if ( $wpdb->has_cap( 'collation' ) ) {
            $collate = $wpdb->get_charset_collate();
        }

        $sql = "CREATE TABLE {$wpdb->prefix}sm_sessions (
		  session_id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		  session_key char(32) NOT NULL,
		  session_value LONGTEXT NOT NULL,
		  session_expiry BIGINT(20) UNSIGNED NOT NULL,
		  PRIMARY KEY  (session_key),
		  UNIQUE KEY session_id (session_id)
		) $collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        $this->el(dbDelta($sql));
        new MZ_Mindbody\Inc\Core\MZMBO_Session;

    }

	public function tearDown() {
		parent::tearDown();
	}

	public function test_set() {
		$this->assertEquals( 'bar', MZ_Mindbody\MZMBO()->session->set( 'foo', 'bar' ) );
	}

	public function test_get() {
		$this->assertEquals( 'bar', MZ_Mindbody\MZMBO()->session->get( 'foo' ) );
	}

	// public function test_use_cart_cookie() {
	// 	$this->assertTrue( MZMBO()->session->use_cart_cookie() );
	// 	define( 'MZMBO_USE_CART_COOKIE', false );
	// 	$this->assertFalse( MZMBO()->session->use_cart_cookie());
	// }

	public function test_should_start_session() {
		$blacklist = MZ_Mindbody\MZMBO()->session->get_blacklist();
		foreach( $blacklist as $uri ) {
			$this->go_to( '/' . $uri );
			$this->assertFalse( MZ_Mindbody\MZMBO()->session->should_start_session() );
		}
	}
}