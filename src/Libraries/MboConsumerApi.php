<?php
/**
 * Mindbody Consumer API
 *
 * This file contains the class with methods to call the
 * new MBO Cobnsumer restful api.
 *
 * @package MzMindbody
 */

namespace MZoo\MzMindbody\Libraries;

use MZoo\MzMindbody as NS;
use MZoo\MzMindbody\Common as Common;
use MZoo\MzMindbody\Core as Core;
use Exception as Exception;

/**
 * All Interface methods for MBO Consumer API, via WordPress wrapper for CURL
 */
class MboConsumerApi extends MboApi {

  public static function authenticate_with_api(){
    /*
		 * For some reason, for this call, we don't want to convert
		 * 'body' into a json string, as we do in the token request.
		 */
		$response = wp_remote_request(
			"https://signin.mindbodyonline.com/connect/authorize",
			array(
				'method'        => 'GET',
				'timeout'       => 45,
				'httpversion'   => '1.0',
				'blocking'      => true,
				'headers'       => '',
				'body'          => '',
				'data_format'   => 'body',
				'cookies'       => array(),
        'response_mode' => 'form_post',
        'response_type' => 'code id_token',
        'client_id'     => 'f89a95cc-d6d5-470a-825b-93f707b88139',
        'redirect_uri'  => 'https://mostlyyoga.com',
        'scope'         => 'offline_access PG.ConsumerActivity.Api.Read',
        'nonce'         => wp_create_nonce( 'mz_mbo_authenticate_with_api' ),
			)
		);


      try {
        $response = $request->send();

        echo $response->getBody();
      } catch (HttpException $ex) {
        echo $ex;
      }

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
  } // End authenticate_with_api
}
