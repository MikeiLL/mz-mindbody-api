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
use MZoo\MzMindbody as NS;

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
     * @since 2.9.9
     */
    private $session;

    /**
     * Disambiguate for additional plugins.
     *
     * @var array
     * @access private
     * @since 2.9.9
     */
    private $prefix;

    /**
     * Disambiguate for additional plugins.
     *
     * @var array
     * @access private
     * @since 2.9.9
     */
    private $prefix_tail = '_main';

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
            add_action( 'plugins_loaded', array( $this, 'start_session' ), -2 );
            if (session_status() !== PHP_SESSION_ACTIVE ) {
                var_dump("session start");
                session_start();
            }
    }

    /**
     * Setup the WP_Session instance.
     *
     * @since 2.9.9
     */
    public function init() {
        $this->prefix = 'mzmbo' . $this->prefix_tail;
        $this->session = isset( $_SESSION[ $this->prefix ] ) && is_array( $_SESSION[ $this->prefix ] )
            ? $_SESSION[ $this->prefix ]
            : array();

        $_SESSION[ $this->prefix ] = $this->session;

        return $this->session;
    }

    /**
     * Set a session variable.
     *
     * @since 2.9.9
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public function set( $key, $value ) {
        $this->session[$key] = $value;
    }

    /**
     * Get a session variable.
     *
     * @since 2.9.9
     * @param string $key
     * @return mixed
     */
    public function get( $key ) {
        return $this->session[$key];
    }


    /**
     * Starts a new session if one hasn't started yet.
     *
     * @since 2.1.3
     */
    public function start_session() {
        NS\MZMBO()->helpers->log("mzmbo maybe start_session");
        if (session_status() !== PHP_SESSION_ACTIVE ) {
            NS\MZMBO()->helpers->log("mzmbo start_session");
            session_start();
        }
    }
}
