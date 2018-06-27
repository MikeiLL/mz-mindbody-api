<?php
/**
 * @group edd_session
 */
class Tests_Session extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();
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