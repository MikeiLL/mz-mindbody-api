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
use MZoo\MzMindbody\Common as Common;
use MZoo\MzMindbody\Core as Core;
use Exception as Exception;

/**
 * All Interface methods for MBO v6 API, via WordPress wrapper for CURL
 */
class MboV6Api extends MboApi {

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
	 * Headers Basic
	 *
	 * Minimal http headers for API call.
	 *
	 * @access protected
	 * @var array $headers_basic sent to MboV6ApiMethods
	 */
	protected $headers_basic = array(
		'User-Agent'   => '',
		'Content-Type' => 'application/json; charset=utf-8',
		'Api-Key'      => '',
		'SiteId'       => '-99',
	);

	/**
	 * Api Methods
	 *
	 * MBO Api Methods, per endpoint.
	 *
	 * @access protected
	 * @var array $api_methods as defined in MboV6ApiMethods
	 */
	protected $api_methods = array();

	/**
	 * Api Methods
	 *
	 * MBO Api Methods, per endpoint.
	 *
	 * @access protected
	 * @var array $extra_credentials sent to MboV6ApiMethods for auth.
	 */
	protected $extra_credentials = array();

	/**
	 * Token Managemen
	 *
	 * Get stored tokens when good and store new ones when retrieved.
	 *
	 * @access protected
	 * @var array $token_management MZoo\MzMindbody\Common\TokenManagement object.
	 */
	protected $token_management;

	/**
	 * Initialize the apiServices and api_methods arrays
	 *
	 * @param array $mbo_dev_credentials which are Core\MzMindbodyApi::$basic_options.
	 * @param array $atts which are configured in the shortcode.
	 */
	public function __construct( $mbo_dev_credentials = array(), $atts = array() ) {

		$this->basic_options = $mbo_dev_credentials;

		$this->atts = $atts;

		$this->token_management = new Common\TokenManagement();

		// set credentials into headers.
		if ( ! empty( $mbo_dev_credentials ) ) {
			/*
			 * if (!empty($mbo_dev_credentials['mz_mbo_app_name'])) {
			 * 	$this-> headers_basic['App-Name'] = $mbo_dev_credentials['mz_mbo_app_name'];
			 * 	If this matches actual app name, requests fail;
			 * }
			 */
			if ( ! empty( $mbo_dev_credentials['mz_mbo_api_key'] ) ) {
				$this->headers_basic['Api-Key'] = $mbo_dev_credentials['mz_mbo_api_key'];
			}
			if ( ! empty( $mbo_dev_credentials['mz_mbo_app_name'] ) ) {
				$this->headers_basic['User-Agent'] = $mbo_dev_credentials['mz_mbo_app_name'];
			}
			if ( ! empty( $mbo_dev_credentials['mz_source_name'] ) ) {
				$this->extra_credentials['SourceName'] = $mbo_dev_credentials['mz_source_name'];
			}
			if ( ! empty( $mbo_dev_credentials['mz_mindbody_password'] ) ) {
				$this->extra_credentials['Password'] = $mbo_dev_credentials['mz_mindbody_password'];
			}
			if ( ! empty( $mbo_dev_credentials['mz_mindbody_siteID'] ) ) {
				if ( is_array( $mbo_dev_credentials['mz_mindbody_siteID'] ) ) {
					$this->headers_basic['SiteIDs'] = $mbo_dev_credentials['mz_mindbody_siteID'][0];
				} elseif ( is_numeric( $mbo_dev_credentials['mz_mindbody_siteID'] ) ) {
					$this->headers_basic['SiteId'] = $mbo_dev_credentials['mz_mindbody_siteID'];
				}
			}
		}

		/**
		 * Set apiServices array with Mindbody endpoints, which was
		 * moved to a separate file for convenience.
		 */
		$mbo_methods = new MboV6ApiMethods( $this->headers_basic );

		$this->api_methods = $mbo_methods->methods;
	}

	/**
	 * Magic method will search $this->api_methods array for $name and call the
	 * appropriate Mindbody API method if found
	 *
	 * @since 2.5.7
	 *
	 * @throws Exception When API called above configured times.
	 * @param string $name that matches method in matrix of MBO API v6 Methods.
	 * @param array  $arguments used in API request.
	 */
	public function __call( $name, $arguments ) {

		$rest_method = '';

		foreach ( $this->api_methods as $api_service_name => $api_methods ) {
			if ( array_key_exists( $name, $api_methods ) ) {
				$rest_method = $api_methods[ $name ];
			}
		}

		if ( empty( $rest_method ) ) {
			return 'Nonexistent Method';
		};

		$this->track_daily_api_calls();

		$within_api_call_limits = $this->api_call_limiter();

		if ( false === $within_api_call_limits ) {
			throw new Exception( 'Too many API Calls.' );
		}

		$this->maybe_log_daily_api_calls( $name );

		if ( empty( $arguments ) ) {
			return $this->call_mindbody_service( $rest_method );
		} else {
			switch ( count( $arguments ) ) {
				case 1:
					return $this->call_mindbody_service( $rest_method, $arguments[0] );
				case 2:
					return $this->call_mindbody_service( $rest_method, $arguments[0], $arguments[1] );
			}
		}
	}

	/**
	 * Return the results of a Mindbody API method
	 *
	 * @param array $rest_method    - as per \MboV6ApiMethods.
	 * @param array $request_data   - API Request data.
	 */
	protected function call_mindbody_service( $rest_method, $request_data = array() ) {

		if ( 'TokenIssue' === $rest_method['name'] ) {
			/*
			 * If this is a token request, make a separate call so that we
			 * don't create a loop here.
			 */
			$token = $this->token_request( $rest_method );

			return $token;
		}

		// Certain methods don't require credentials.
		// TODO: maybe add flags in methods array.
		$method_without_username = array(
			'AddClient',
			'GetMemberships',
			'GetContracts',
			'GetServices',
		);

		// Certain methods want json strings.
		$encoded_request_body = array(
			'AddClient',
			'SendPasswordResetEmail',
			'CheckoutShoppingCart',
		);

		// Certain methods don't require credentials.
		if ( ! in_array( $rest_method['name'], $method_without_username, true ) ) {
			$request_body = array_merge(
				$request_data,
				array(
					'Username' => $this->format_username(),
					'Password' => $this->extra_credentials['Password'],
					'Limit'    => 200,
				)
			);

			// Maybe there's a stored token to use.
			$token = $this->token_management->get_stored_token();

			if ( false !== $token && ctype_alnum( $token['AccessToken'] ) ) {
				$request_body['Access'] = $token['AccessToken'];
			} else {
				$request_body['Access'] = $this->token_request( $rest_method );
			}
		} else {
			$request_body = $request_data;
		}

		if ( ! empty( $this->atts['session_type_ids'] ) ) {
			$request_body['SessionTypeIds'] = $this->atts['session_type_ids'];
		}

		if ( in_array( $rest_method['name'], $encoded_request_body, true ) ) {
			$request_body = wp_json_encode( $request_body );
		}

		/*
		 * For some reason, for this call, we don't want to convert
		 * 'body' into a json string, as we do in the token request.
		 */
		$response = wp_remote_request(
			$rest_method['endpoint'],
			array(
				'method'      => $rest_method['method'],
				'timeout'     => 45,
				'httpversion' => '1.0',
				'blocking'    => true,
				'headers'     => $rest_method['headers'],
				'body'        => $request_body,
				'data_format' => 'body',
				'cookies'     => array(),
			)
		);

		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			return 'Something went wrong: ' . $error_message;
		} else {
			$response_body = json_decode( $response['body'], true );

			if ( is_array( $response_body ) && array_key_exists( 'Error', $response_body ) && strpos( $response_body['Error']['Message'], 'Please try again' ) ) {
				// OK try again.
				// sleep(3); Formerly after three seconds removed.
				if ( $this->token_request_tries > 1 ) {
					return $this->call_mindbody_service( $rest_method, $request_data );
				}
				return false;
			}
			$this->maybe_log_daily_api_calls( $response_body );

			// return the data as an array, which is what we are used to.
			return $response_body;
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
	protected function token_request( $rest_method ) {

		$this->token_request_tries--;

		$request_body = array(
			'Username' => $this->format_username(),
			'Password' => $this->extra_credentials['Password'],
		);

		$response = wp_remote_post(
			'https://api.mindbodyonline.com/public/v6/usertoken/issue',
			array(
				'method'      => 'POST',
				'timeout'     => 90,
				'httpversion' => '1.0',
				'blocking'    => true,
				'headers'     => $rest_method['headers'],
				'body'        => wp_json_encode( $request_body ),
				'cookies'     => array(),
			)
		);

		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			return 'Something went wrong with token request: ' . $error_message;
		} else {
			$response_body = json_decode( $response['body'] );
			// @codingStandardsIgnoreStart naming convensions 'Error'
			if ( property_exists( $response_body, 'Error' ) && strpos( $response_body->Error->Message, 'Please try again' ) ) {
			// @codingStandardsIgnoreEnd
				// OK try again after three seconds.
				sleep( 3 );
				if ( $this->token_request_tries > 1 ) {
					return $this->token_request( $rest_method );
				}
				return false;
			}

			$this->token_management->save_token_to_option( $response_body );
			return $response_body;
		}
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

		if ( 'on' === $this->basic_options['sourcename_not_staff'] ) {
			return '_' . $this->extra_credentials['SourceName'];
		} else {
			return $this->extra_credentials['SourceName'];
		}
	}

	/**
	 * Make basic call to classes endpoint
	 *
	 * @return DOM textarea with mbo call result in it.
	 */
	public function debug() {
		$return  = "<textarea rows='6' cols='90'>";
		$return .= print_r(
			$this->GetClasses(
				array(
					'method'   => 'GET',
					'name'     => 'GetClasses',
					'endpoint' => 'https://api.mindbodyonline.com/public/v6/class/classes',
					'headers'  => $this->headers_basic,
				)
			),
			1
		);
		$return .= '</textarea>';

		return $return;
	}
}
