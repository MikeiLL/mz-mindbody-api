<?php
/**
 * MzPhpSession wrapper class.
 *
 * Wrap Eric Mann's sophisticated WP_Session class.
 *
 * @package MZMBOACCESS
 */

namespace MZoo\MzMindbody\Session;

use MZoo\MzMboAccess\Dependencies\EAMann\Sessionz;
use MZoo\MzMboAccess\Dependencies\EAMann\WPSession;

/**
 * MzPhpSession wrapper class
 *
 * @since 1.0.1
 */
class MzPhpSession {

	/**
	 * Hold the class instance.
	 *
	 * @var class instance
	 * @access private
	 * @since  1.0.1
	 */
	private static $instance = null;

	/**
	 * Holds our session data
	 *
	 * @var    array
	 * @access public
	 * @since  1.0.1
	 */
	public $session;

	/**
	 * Session index prefix
	 *
	 * @since  1.0.1
	 * @access private
	 *
	 * @var string
	 */
	private $prefix = 'mz_mbo_access_';

	/**
	 * Get things started
	 *
	 * Defines our WP_Session constants, includes the necessary libraries and
	 * retrieves the WP Session instance
	 *
	 * @since 1.0.1
	 */
	public function __construct() {

		if ( ! $this->should_start_session() ) {
			return;
		}
        // There were also issues with running PHPUNIT and getting
        // Headers already sent warnings, which broke the tests.
        // May, at some point, want to instantiate the sessions
        // Separately from main plugin instance using
        // add_action( 'template_redirect'
		if ( PHP_SESSION_DISABLED !== session_status() &&
        ( ! defined( 'WP_CLI' ) || false === WP_CLI ) &&
        ( ! defined( 'RUNNING_PHPUNIT' ) || false === RUNNING_PHPUNIT ) ) {
			add_action( 'wp_loaded', array( $this, 'wp_session_manager_initialize' ), 1, 0 );
			// If we're not in a cron, start the session.
			if ( ! defined( 'DOING_CRON' ) || false === DOING_CRON ) {
				add_action( 'wp_loaded', array( $this, 'wp_session_manager_start_session' ), 10, 0 );
			}
		}
	}


	/**
	 * Return our session instance
	 * @since 2.9.9
	 */
	public function get_session() {
		return $this->session;
	}


	/**
	 * Initialize the plugin, bootstrap autoloading, and register default hooks
	 */
	public function wp_session_manager_initialize() {

		if ( ! isset( $_SESSION ) ) {
			// Queue up the session stack.
			$wp_session_handler = Sessionz\Manager::initialize();

			// Fall back to database storage where needed.
			if ( defined( 'WP_SESSION_USE_OPTIONS' ) && WP_SESSION_USE_OPTIONS ) {
				$wp_session_handler->addHandler( new WPSession\OptionsHandler() );
			} else {
				$wp_session_handler->addHandler( new WPSession\DatabaseHandler() );

				/**
				 * The database handler can automatically clean up sessions as it goes. By default,
				 * we'll run the cleanup routine every hour to catch any stale sessions that PHP's
				 * garbage collector happens to miss. This timeout can be filtered to increase or
				 * decrease the frequency of the manual purge.
				 *
				 * @param string $timeout Interval with which to purge stale sessions
				 */
				$timeout = apply_filters( 'wp_session_gc_interval', 'hourly' );

				if ( ! wp_next_scheduled( 'wp_session_database_gc' ) ) {
					wp_schedule_event( time(), $timeout, 'wp_session_database_gc' );
				}

				add_action( 'wp_session_database_gc', array( 'EAMann\WPSession\DatabaseHandler', 'directClean' ) );
			}

			// If we have an external object cache, let's use it!
			if ( wp_using_ext_object_cache() ) {
				$wp_session_handler->addHandler( new WPSession\CacheHandler() );
			}

			if ( defined( 'WP_SESSION_ENC_KEY' ) && WP_SESSION_ENC_KEY ) {
				$wp_session_handler->addHandler( new Sessionz\Handlers\EncryptionHandler( WP_SESSION_ENC_KEY ) );
			}

			// Use an in-memory cache for the instance if we can. This will only help in rare cases.
			$wp_session_handler->addHandler( new Sessionz\Handlers\MemoryHandler() );

			$_SESSION['wp_session_manager'] = 'active';
		}

		if ( 'active' !== $_SESSION['wp_session_manager'] || ! isset( $_SESSION['wp_session_manager'] ) ) {
			add_action( 'admin_notices', array( $this, 'wp_session_manager_multiple_sessions_notice' ) );
			return;
		}

		// Create the required table.
		WPSession\DatabaseHandler::createTable();

		register_deactivation_hook(
			__FILE__,
			function () {
				wp_clear_scheduled_hook( 'wp_session_database_gc' );
			}
		);
	}

	/**
	 * Determines if we should start sessions
	 *
	 * @return bool
	 * @since  1.0.1
	 */
	public function should_start_session() {

		$start_session = true;
		if ( ! empty( $_SERVER['REQUEST_URI'] ) ) {
			$blacklist = $this->get_blacklist();
			$uri       = ltrim( $_SERVER['REQUEST_URI'], '/' );
			$uri       = untrailingslashit( $uri );
			if ( in_array( $uri, $blacklist, true ) ) {
				$start_session = false;
			}
			if ( false !== strpos( $uri, 'feed=' ) ) {
				$start_session = false;
			}
		}

		return apply_filters( 'mbo_access_sessions_start_session', $start_session );
	}

	/**
	 * Retrieve the URI blacklist
	 *
	 * These are the URIs where we never start sessions
	 *
	 * @return array
	 * @since  1.0.1
	 */
	public function get_blacklist() {
		$blacklist = apply_filters(
			'mbo_access_sessions_session_start_uri_blacklist',
			array(
				'feed',
				'feed/rss',
				'feed/rss2',
				'feed/rdf',
				'feed/atom',
				'comments/feed',
			)
		);
		// Look to see if WordPress is in a sub folder or this is a network site that uses sub folders.
		$folder = str_replace( network_home_url(), '', get_site_url() );
		if ( ! empty( $folder ) ) {
			foreach ( $blacklist as $path ) {
				$blacklist[] = $folder . '/' . $path;
			}
		}

		return $blacklist;
	}

	/**
	 * Main MzPhpSession Instance
	 *
	 * Ensures only one instance is loaded or can be loaded.
	 *
	 * @return MzPhpSession - Main instance
	 * @since  1.0.1
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * If a session hasn't already been started by some external system, start one!
	 */
	public function wp_session_manager_start_session() {
		if ( session_status() !== PHP_SESSION_ACTIVE ) {
			session_start();
		}
	}

	/**
	 * Print an admin notice if too many plugins are manipulating sessions.
	 *
	 * @global array $this->wp_session_messages
	 */
	public function wp_session_manager_multiple_sessions_notice() {
		echo '<div class="notice notice-error">';
		echo '<p>';
		esc_html_e(
			'Another plugin is attempting to start a session with WordPress. WP Session Manager will not work!',
			'wp-session-manager'
		);
		echo '</p>';
		echo '</div>';
	}


	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.1
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html_e( 'Cheatin&#8217; huh?', 'wsi' ), '2.1' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.1
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html_e( 'Cheatin&#8217; huh?', 'wsi' ), '2.1' );
	}

	/**
	 * Retrieve a session variable
	 *
	 * @access public
	 *
	 * @param string $key Session key.
	 *
	 * @return mixed Session variable
	 * @since  1.5
	 */
	public function get( $key ) {
		$key = sanitize_key( $key );

		return isset( $_SESSION[ $key ] ) ? json_decode( $_SESSION[ $key ] ) : false;
	}

	/**
	 * Set a session variable
	 *
	 * @param string           $key   Session key.
	 * @param int|string|array $value Session variable.
	 *
	 * @return mixed Session variable
	 * @since  1.0.1
	 */
	public function set( $key, $value ) {
		$key = sanitize_key( $key );

		$_SESSION[ $key ] = wp_json_encode( $value );

		return $_SESSION[ $key ];
	}
}
