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
    * Headers Basic
    *
    * Minimal http headers for API call.
    *
    * @access protected
    * @var array $headers_basic sent to MboV6ApiMethods
    */
   protected static $headers_basic = array(
       'User-Agent'   => '',
       'Content-Type' => 'application/json; charset=utf-8',
       'Api-Key'      => '',
       'SiteId'       => '-99',
   );

    /**
     * Basic Options
     *
     * MBO options as set in Admin Settings page.
     *
     * @access protected
     * @var array $basic_options from Admin for MBO auth.
     */
    protected static $basic_options;

    /**
     * Shortcode Attributes
     *
     * @since 2.6.7
     * @access private
     * @var array $atts shortcode attributes.
     */
    protected $atts;

    /**
     * Token Management
     *
     * Get stored tokens when good and store new ones when retrieved.
     *
     * @access protected
     * @var array $token_management MZoo\MzMindbody\Common\TokenManagement object.
     */
    protected $token_management;

    /**
     * Token Request Tries
     *
     * Limit the number of retries when token
     * request fails.
     *
     * @access private
     * @var int $token_request_tries
     */
    private $token_request_tries = 6;

    /**
     * Api Methods
     *
     * MBO Api Methods, per endpoint.
     *
     * @access protected
     * @var array $$extra_credentials sent to MboV6ApiMethods for auth.
     */
    protected static $extra_credentials = array();

    /**
     * Initialize the apiServices and api_methods arrays
     *
     * @param array $mbo_dev_credentials which are Core\MzMindbodyApi::$basic_options.
     * @param array $atts which are configured in the shortcode.
     */
    public function __construct( $mbo_dev_credentials = array(), $atts = array() ) {

        self::$basic_options = $mbo_dev_credentials;

        $this->atts = $atts;

        // set credentials into headers.
        if ( ! empty( $mbo_dev_credentials ) ) {
            /*
             * if (!empty($mbo_dev_credentials['mz_mbo_app_name'])) {
             *     $this-> headers_basic['App-Name'] = $mbo_dev_credentials['mz_mbo_app_name'];
             *     If this matches actual app name, requests fail;
             * }
             */
            if ( ! empty( $mbo_dev_credentials['mz_mbo_api_key'] ) ) {
                self::$headers_basic['Api-Key'] = $mbo_dev_credentials['mz_mbo_api_key'];
            }
            // TODO Remove following? Not used in MBO v6.
            if ( ! empty( $mbo_dev_credentials['mz_mbo_app_name'] ) ) {
                self::$headers_basic['User-Agent'] = $mbo_dev_credentials['mz_mbo_app_name'];
            }
            if ( ! empty( $mbo_dev_credentials['mz_source_name'] ) ) {
                self::$extra_credentials['SourceName'] = $mbo_dev_credentials['mz_source_name'];
            }
            if ( ! empty( $mbo_dev_credentials['mz_mindbody_password'] ) ) {
                self::$extra_credentials['Password'] = $mbo_dev_credentials['mz_mindbody_password'];
            }
            if ( ! empty( $mbo_dev_credentials['mz_mindbody_siteID'] ) ) {
                if ( is_array( $mbo_dev_credentials['mz_mindbody_siteID'] ) ) {
                    self::$headers_basic['SiteIDs'] = $mbo_dev_credentials['mz_mindbody_siteID'][0];
                } elseif ( is_numeric( $mbo_dev_credentials['mz_mindbody_siteID'] ) ) {
                    self::$headers_basic['SiteId'] = $mbo_dev_credentials['mz_mindbody_siteID'];
                }
            }
        }
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

    /**
     * Return username string formatted based on if Sourcename of Staff Name
     *
     * @since 2.5.7
     * @used  by token_request(), call_mindbody_service()
     *
     * return string of MBO API user name with our without preceding underscore
     */
    protected function format_username() {
        if (!isset(self::$basic_options['sourcename_not_staff'])) {
            return '';
        }
        if ( 'on' === self::$basic_options['sourcename_not_staff'] ) {
            return '_' . self::$extra_credentials['SourceName'];
        } else {
            return self::$extra_credentials['SourceName'];
        }
    }

    /**
     * Return the results of a Mindbody API method, specific to token
     *
     * Get a stored token if there's a good one availaible,
     * if not, request one from the API and store it to the
     * WP database. As above, but specifically for token requests.
     *
     * @since 2.5.7
     *
     * @param array $rest_method as per \MboV6ApiMethods.
     * @return array of WP option or MBO API Response with date and token string.
     */
    protected function token_request() {

        $this->token_request_tries--;

        $request_body = array(
            'Username' => $this->format_username(),
            'Password' => self::$extra_credentials['Password'],
        );

        $response = wp_remote_post(
            'https://api.mindbodyonline.com/public/v6/usertoken/issue',
            array(
                'method'      => 'POST',
                'timeout'     => 90,
                'httpversion' => '1.0',
                'blocking'    => true,
                'headers'     => self::$headers_basic,
                'body'        => wp_json_encode( $request_body ),
                'cookies'     => array(),
            )
        );

        if ( is_wp_error( $response ) ) {
            $error_message = $response->get_error_message();
            return 'Something went wrong with token request: ' . $error_message;
        } else {
            if ((int) $response['response']['code'] > 299) {
                NS\MZMBO()->helpers->log(['RESPONSE ERROR', $response['response']]);
                return 'Something went wrong with token request: ' . $response['response']['message'];
            }
            $response_body = json_decode( $response['body'] );
            // @codingStandardsIgnoreStart naming convensions 'Error'
            if ( property_exists( $response_body, 'Error' ) && strpos( $response_body->Error->Message, 'Please try again' ) ) {
            // @codingStandardsIgnoreEnd
                // OK try again after three seconds.
                sleep( 3 );
                if ( $this->token_request_tries > 1 ) {
                    return $this->token_request();
                }
                return false;
            }

            $this->token_management = new Common\TokenManagement();

            $this->token_management->save_token_to_option( $response_body );
            return $response_body;
        }
    }

}
