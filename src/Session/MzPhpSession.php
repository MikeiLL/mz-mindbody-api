<?php
/**
 * EDD Session
 *
 * This is a wrapper class for WP_Session / PHP $_SESSION and handles the storage of cart items, purchase sessions, etc
 *
 *
 * @package MZMBOACCESS
 */

namespace MZoo\MzMindbody\Session;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * MZMBO_Session Class
 *
 * @since 1.5
 */
class MzPhpSession {

	/**
	 * Holds our session data.
	 *
	 * @var array
	 * @access private
	 * @since 1.5
	 */
	private $session;

	/**
	 * Constructor.
	 *
	 * Defines our WP_Session constants, includes the necessary libraries and
	 * retrieves the WP Session instance.
	 *
	 * @since 1.5
	 */
	public function __construct() {
			// Use PHP SESSION (must be enabled via the MZMBO_USE_PHP_SESSIONS constant)
			add_action( 'init', array( $this, 'start_session' ), -2 );
	}

	/**
	 * Setup the WP_Session instance.
	 *
	 * @since 1.5
	 */
	public function init() {
        $key           = 'mzmbo_';
        $this->session = isset( $_SESSION[ $key ] ) && is_array( $_SESSION[ $key ] )
            ? $_SESSION[ $key ]
            : array();

        $_SESSION[ $key ] = $this->session;

		return $this->session;
	}


	/**
	 * Starts a new session if one hasn't started yet.
	 *
	 * @since 2.1.3
	 */
	public function start_session() {
        if (session_status() !== PHP_SESSION_ACTIVE ) {
            session_start();
        }
	}
}
