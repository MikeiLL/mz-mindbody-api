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

	/**
	 * Register User with Studio
	 */
	public function register_user_with_studio(){
		echo "<h2>REGISTERING YOU WITH STUDIO.</h2>";
		$contactProps = [];
		foreach($_POST as $k=>$v) {
			$contactProps[] = ['name' => $k, 'value' => $v];
		}
		echo "register_user_with_studio 1<pre>";
		var_dump($_SESSION);
		echo "</pre>";
		echo "register_user_with_studio 1<pre>";
		echo debug_print_backtrace();
		echo "</pre>";

		$request_body = array(
			'method'        		=> 'POST',
			'timeout'       		=> 55,
			'httpversion'   		=> '1.0',
			'blocking'      		=> true,
			'headers'       		=> [
				'API-Key' 				=> Core\MzMindbodyApi::$basic_options['mz_mbo_api_key'],
				'Authorization'		    => 'Bearer ' . $this->session->get( 'MBO_Public_Oauth_Token' )->AccessToken,
				'Content-Type'		    => 'application/json',
				'businessId'            => Core\MzMindbodyApi::$basic_options['mz_mindbody_siteID'],
			],
			'body'					=> json_encode([
					"userId" => $this->session->get('MBO_Universal_ID'),
					"contactProperties" => $contactProps
					]),
			'redirection' 			=> 0,
			'cookies'						=> array()
			);

			// This will create a Studio Specific Account for user based on MBO Universal Account
			$response = wp_remote_request(
				"https://api.mindbodyonline.com/platform/contacts/v1/profiles",
				$request_body
			);
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
