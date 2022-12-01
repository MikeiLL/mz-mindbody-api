<?php
/**
 * Mindbody V6 API
 *
 * This file contains the class with methods to call the
 * new MBO v6 restful api.
 *
 * @package MzMindbody
 */

namespace MZoo\MzMindbody\Libraries;

use MZoo\MzMindbody as NS;
use MZoo\MzMindbody\Common;
use MZoo\MzMindbody\Core;
use MZoo\MzMindbody\Session;
use Exception as Exception;

/**
 * All Interface methods for MBO v6 API, via WordPress wrapper for CURL
 */
class MboApi {

	/**
	 * Basic Options
	 *
	 * MBO options as set in Admin Settings page.
	 *
	 * @access protected
	 * @var array $basic_options from Admin for MBO auth.
	 */
	protected $basic_options;

	/**
	 * Shortcode Attributes
	 *
	 * @since 2.6.7
	 * @access private
	 * @var array $atts shortcode attributes.
	 */
	private $atts;

	public function __construct(){
		$this->check_post_requests();
		$this->got_token = false;
		// TODO: Figure out why this class gets instantiated twice.
	}

	/**
	 * Check for POST requests and farm to appropriate method.
	 *
	 * @since 2.9.9
	 */
	protected function check_post_requests() {
		if (empty($_POST)) {
			return;
		}

		// Returns from Oauth request
		if (!empty($_POST['id_token']) &&
					!empty($_POST['scope']) &&
					!empty($_POST['code']) &&
					!empty($_POST['session_state'])){

						$access_token = $this->get_token();

						if (false !== $access_token) {
							// Clear Post token so this only runs once.
							$_POST['id_token'] = "";
							echo "<h2>GOT TOKEN IS TRUE</h2>";
							$this->get_universal_id($access_token);
						}
				} else if (!empty($_POST['Email']) &&
								!empty($_POST['FirstName']) &&
								!empty($_POST['LastName']) ) {
								// Looks like we want to register this user
								$this->register_user_with_studio();
			}
	}

	/**
	 * Register User with Studio
	 */
	private function register_user_with_studio(){
		echo "<h2>REGISTERING YOU WITH STUDIO.</h2>";
		$contactProps = [];
		foreach($_POST as $k=>$v) {
			$contactProps[] = ['name' => $k, 'value' => $v];
		}

		// If we call NS\MZMBO()->session here it creates a loop.
		$session = Session\MzPhpSession::instance();

			// This will create a Studio Specific Account for user based on MBO Universal Account
			$response = wp_remote_request(
				"https://api.mindbodyonline.com/platform/contacts/v1/profiles",
				array(
					'method'        		=> 'POST',
					'timeout'       		=> 55,
					'httpversion'   		=> '1.0',
					'blocking'      		=> true,
					'headers'       		=> [
						'API-Key' 				=> Core\MzMindbodyApi::$basic_options['mz_mbo_api_key'],
						'Authorization'		=> 'Bearer ' . $session->get( 'MBO_Public_Oauth_Token' )->AccessToken,
						'Content-Type'		=> 'application/json',
						'businessId' => Core\MzMindbodyApi::$basic_options['mz_mindbody_siteID'],
					],
					'body'							=> json_encode([
							"userId" => $session->get('MBO_Universal_ID'),
							"contactProperties" => $contactProps
							]),
					'redirection' 			=> 0,
					'cookies'						=> array()
				)
			);
			echo "Response <pre>";
			print_r($response);
			echo "</pre>";
			die();
	}
	/**
	 * Get Token
	 *
	 * Get Oauth token from MBO API.
	 *
	 * @since 2.9.9
	 * @access protected
	 * @return TODO
	 *
	 */
	protected function get_token() {
		$nonce = wp_create_nonce( 'mz_mbo_authenticate_with_api' );
		$id_token = $_POST['id_token'];
		$request_body = array(
			'method'        		=> 'POST',
			'timeout'       		=> 55,
			'httpversion'   		=> '1.0',
			'blocking'      		=> true,
			'headers'       		=> '',
			'body'          		=> [
				'client_id'     => Core\MzMindbodyApi::$oauth_options['mz_mindbody_client_id'],
				'grant_type'		=> 'authorization_code',
				'scope'         => 'email profile openid offline_access Mindbody.Api.Public.v6 PG.ConsumerActivity.Api.Read',
				'client_secret'	=> Core\MzMindbodyApi::$oauth_options['mz_mindbody_client_secret'],
				'code'					=> $_POST['code'],
				'redirect_uri'	=> home_url(),
				'nonce'					=> $nonce
			],
			'redirection' 			=> 0,
			'cookies'       => array()
		);
		if (true === $this->got_token) {
			return;
		}
		$response = wp_remote_request(
			"https://signin.mindbodyonline.com/connect/token",
			$request_body
		);

		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			echo "Something went wrong: $error_message";
		} else {
			$response_body = json_decode($response['body']);
			if (empty($response_body->access_token)) {
				return false;
			} else {
				return $response_body->access_token;
			}
		}
	}

	/**
	 * Check token with MBO API
	 *
	 * Retrieve the users universal id from MBO API.
	 *
	 * @since 2.9.9
	 */
	public function get_universal_id($token) {
		$response = wp_remote_request(
			"https://api.mindbodyonline.com/platform/accounts/v1/me",
			array(
				'method'        		=> 'GET',
				'timeout'       		=> 55,
				'httpversion'   		=> '1.0',
				'blocking'      		=> true,
				'headers'       		=> [
					'API-Key' 			=> Core\MzMindbodyApi::$basic_options['mz_mbo_api_key'],
					'Authorization' => 'Bearer ' . $token
				],
				'body'          		=> '',
				'redirection' 			=> 0,
				'cookies'       => array()
			)
		);
		$response_body = json_decode($response['body']);
		if (!empty($response_body->id)){
			$this->save_id_and_token($response_body->id, $token);
			$siteID = (int)Core\MzMindbodyApi::$basic_options['mz_mindbody_siteID'];
			$has_account = false;
			foreach($response_body->businessProfiles as $studio){
				if ( $siteID === $studio->businessId ) {
					$this->session->set('MBO_USER_Site_ID', $studio->profileId);
					$has_account = true;
				}
			}
			if (true || false === $has_account) {
				// Need to register for this site.
				echo "You need to register for this site. \n";
				echo '<script>if (window.opener) window.opener.dispatchEvent(new Event("need_to_register"));</script>';
				/*
				$client = new \MZoo\MzMindbody\Client\RetrieveClient();
				$fields = $client->get_signup_form_fields();
				echo "<form method=POST>";
				echo "<ul>";
				foreach($fields as $f){
					$userField = lcfirst($f);
					echo '<li>';
					if (property_exists($response_body, $userField)){
						echo $f . ' <input name="' . $f . '" value="' . $response_body->$userField. '">';
					} else {
						echo $f . ' <input name="' . $f . '">';
					}

					echo '</li>';
				}
				echo "</ul>";
				echo '<input type=SUBMIT value="Register Now">';
				echo "</form></dialog>"; */
			} else {
				echo $this->session->get('MBO_USER_Site_ID');
				echo "Got the MBO_USER_Site_ID. Now we can do stuff.";
				echo '<script>if (window.opener) window.opener.dispatchEvent(new Event("authenticated"));</script>';
			}
			echo '<script>window.close();</script>';
		} else {
			// This should never happen, but just in case:
			echo "No Universal ID";
		}
	}

	/**
	 * Save Universal ID and Token
	 *
	 * Store Oauth token and universal id in $Session.
	 *
	 * @since 2.9.9
	 * @param string $id Universal ID from MBO API.
	 * @param string $token Oauth Token from MBO API.
	 *
	 */
	private function save_id_and_token($universal_id, $token) {
		$current = new \DateTime();
		$current->format( 'Y-m-d H:i:s' );

		$stored_token = array(
			'stored_time' => $current,
			'AccessToken' => $token,
		);
		NS\MZMBO()->session->set( 'MBO_Public_Oauth_Token', $stored_token );
		NS\MZMBO()->session->set( 'MBO_Universal_ID', $universal_id );
	}

	/**
	 * Track API requests per day
	 *
	 * There is a 1000 call limit per day on MBO, per location.
	 * Any calls above that number per location are
	 * charged at the overage rate of 1/3 cent each.
	 *
	 * @access protected
	 */
	protected function track_daily_api_calls() {
		// If not set, initiate array to track mbo calls.
		$mz_mbo_api_calls = get_option(
			'mz_mbo_api_calls',
			array(
				'calls' => 2,
				'today' => gmdate( 'Y-m-d' ),
			)
		);
		if ( $mz_mbo_api_calls['today'] < gmdate( 'Y-m-d' ) ) {
			// If it's a new day, reinitialize the matrix.
			$mz_mbo_api_calls = array(
				'today' => gmdate( 'Y-m-d' ),
				'calls' => 1,
			);
			update_option( 'mz_mbo_api_calls', $mz_mbo_api_calls );
		};
		// Otherwise increase the call count.
		$mz_mbo_api_calls['calls'] += 1;
		update_option( 'mz_mbo_api_calls', $mz_mbo_api_calls );
	}

	/**
	 * Limit the number of API requests per day
	 *
	 * There is a 1000 call limit per day on MBO, per location.
	 * Notify the admin when we get close and stop making calls
	 * when we get to about $3USD in overages.
	 *
	 * @return bool False if limit has been reached.
	 */
	protected function api_call_limiter() {

		// Don't limit if using sandbox.
		if ( ( isset( NS\MZMBO()::$basic_options['mz_mindbody_siteID'] ) ) &&
				( '-99' === NS\MZMBO()::$basic_options['mz_mindbody_siteID'] ) ) {
			return true;
		}

		// Begin alerting admin that number of daily calls nearing limit.
		if ( NS\MZMBO()::$mz_mbo_api_calls['calls'] > ( NS\MZMBO()::$advanced_options['api_call_limit'] - 500 ) ) {
			// TODO: Maybe following should be done in an action hook.
			$this->set_admin_call_excess_alert();
		};

		if ( NS\MZMBO()::$mz_mbo_api_calls['calls'] > NS\MZMBO()::$advanced_options['api_call_limit'] ) {
			return false;
		};

		// Unschedule cron alert if set because we are in the clear now.
		if ( wp_next_scheduled( 'mz_mbo_api_alert_cron' ) ) {
			wp_clear_scheduled_hook( 'mz_mbo_api_alert_cron' );
		}

		return true;
	}

	/**
	 * Set Admin Call Excess Alert Cron Job
	 *
	 * Make the admin notification via wp_mail.
	 *
	 * @return void
	 */
	public function set_admin_call_excess_alert() {
		$well = wp_next_scheduled( 'mz_mbo_api_alert_cron' );
		if ( ! wp_next_scheduled( 'mz_mbo_api_alert_cron' ) ) {
			wp_schedule_event( time(), 'hourly', 'mz_mbo_api_alert_cron' );
		}
	}

	/**
	 * Admin Call Excess Alert
	 *
	 * Make the admin notification via wp_mail.
	 *
	 * @return void
	 */
	public function admin_call_excess_alert() {
		$to      = get_option( 'admin_email' );
		$subject = __( 'Large amount of MBO API Calls', 'mz-mindbody-api' );
		$message = sprintf(
			// translators: Notify user of number of calls to api versus limit configured in WP option.
			__( 'Check your website at %3$s. There have been %1$s calls to the Mindbody API so far today. You have set a maximum of %2$s in the Admin.', 'mz-mindbody-api' ),
			NS\MZMBO()::$mz_mbo_api_calls['calls'],
			NS\MZMBO()::$advanced_options['api_call_limit'],
			site_url()
		);
		$headers = array( 'Content-Type: text/html; charset=UTF-8' );
		wp_mail( $to, $subject, $message, $headers );
	}

	/**
	 * Maybe log daily API calls
	 *
	 * Log API calls if configured to do so in admin.
	 *
	 * @param string $service_method which will be soap for v5 and rest method for v6.
	 */
	protected function maybe_log_daily_api_calls( $service_method ) {
		if ( ( isset( NS\Core\MzMindbodyApi::$advanced_options['log_api_calls'] ) ) &&
				( 'on' === NS\Core\MzMindbodyApi::$advanced_options['log_api_calls'] ) ) :
			$trace          = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 2 )[1];
			$caller_details = $trace['function'];
			if ( isset( $trace['class'] ) ) {
				$caller_details .= ' in:' . $trace['class'];
			}
			NS\MZMBO()->helpers->api_log( print_r( $service_method, true ) . ' caller:' . $caller_details );
		endif;
	}

}
