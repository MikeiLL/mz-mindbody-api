<?php
/**
 * Token Management
 *
 * This file contains the class which interfaces with MBO API
 * to get and renew tokens.
 *
 * @package MzMindbody
 */

namespace MZoo\MzMindbody\Common;

use MZoo\MzMindbody as NS;
use MZoo\MzMindbody\Core as Core;
use MZoo\MzMindbody\Common\Interfaces as Interfaces;

/**
 * Token Management
 *
 * Intermittently fetch, store and serve mbo api AccessTokens.
 *
 * @since 2.5.7
 *
 * This class exposes methods for retrieving tokens from MBO, storing them
 * locally in the DB and also returning them from the DB if new enough and
 * from MBO if not.
 *
 * The MBO V6 API serves and requires AccessTokens which expire after seven days. So
 * we store a valid AccessToken in the db options table, intermittently updating it.
 */
class TokenManagement extends Interfaces\Retrieve {

    /**
     * MBO Account
     *
     * @var string $mbo_account the numeric account of the MBO account.
     */
    protected $mbo_account;

    /**
     * Constructor
     *
     * Default $atts is locations array with location id #1.
     *
     * TODO Not sure if we need atts and/or locations here.
     *
     * @param array $atts Shortcode attributes.
     */
    public function __construct( $atts = array( 'locations' => array( 1 ) ) ) {

        parent::__construct();

        $this->atts = $atts;

        // Initialize to sandbox account.
        $this->mbo_account = '-99';

        if ( ! empty( Core\MzMindbodyApi::$basic_options['mz_mindbody_siteID'] ) ) :
            // Default to admin configured site ID.
            $this->mbo_account = Core\MzMindbodyApi::$basic_options['mz_mindbody_siteID'];
            // Unless user has overridden it in shortcode.
            if ( ! empty( $atts['account'] ) ) {
                $this->mbo_account = $atts['account'];
            }
        endif;
    }

    /**
     * Serve MBO AccessToken either from WP option or from MBO API
     *
     * @since 2.5.7
     *
     * @throws \Exception If error getting token from MBO.
     * @return AccessToken string as administered by MBO Api.
     */
    public function get_mbo_results() {
        // NS\MZMBO()->helpers->log( 'Getting token from MBO.' );

        $mb = $this->instantiate_mbo_api();

        if ( ! $mb ) {
            return false;
        }

        $response = $mb->TokenIssue();
        // NS\MZMBO()->helpers->log( print_r($response, true) );
        // @codingStandardsIgnoreStart naming conventions
        if ( ! empty( $response->AccessToken ) ) {
            return $response->AccessToken;
        }
        if ( ! empty( $response->Error->Message ) ) {
            return $response->Error->Message;
        }
        // @codingStandardsIgnoreEnd
        throw new \Exception( ' Error getting token from MBO.' );
    }




    /**
     * Serve MBO AccessToken either from WP option or from MBO API
     *
     * @since 2.5.7
     *
     * Check stored token for existence and time saved. If LT 12 hours old
     * serve, if not, get from MBO API and save via save_token.
     *
     * @return AccessToken string as administered by MBO Api.
     */
    public function serve_token() {

        $stored_token = $this->get_stored_token();

        if ( empty($stored_token['AccessToken']) || empty( $stored_token ) ) {
            return $this->get_and_save_staff_token();
        } else {
            return $stored_token['AccessToken'];
        }
    }


    /**
     * Return stored MBO token or false
     *
     * @since 2.5.7
     *
     * Test if token is saved and less than  12 hours old.
     *
     * @return false or string: stored token.
     */
    public function get_stored_token() {
        // Get the option or return an empty array with two keys.
        $stored_token = get_option(
            'mz_mbo_token',
            array(
                'stored_time' => '',
                'AccessToken' => '',
            )
        );

        if ( empty( $stored_token['AccessToken'] ) || ! ctype_alnum( $stored_token['AccessToken'] ) ) {
            return false;
        }

        // If there is a mz_mbo_token, let's see if it's less than 12 hours old.

        $current = new \DateTime();
        $current->format( 'Y-m-d H:i:s' );
        $twleve_hours_ago = clone $current;
        $twleve_hours_ago->sub( new \DateInterval( 'PT12H' ) );

        if ( is_object( $stored_token['stored_time'] ) &&
            ( $stored_token['stored_time'] > $twleve_hours_ago ) &&
                ctype_alnum( $stored_token['AccessToken'] ) ) {
            return json_decode( json_encode( $stored_token ), true );
        }

        // Must be too old.
        return false;
    }

    /**
     * Retrieve MBO AccessToken and Save to WP option
     *
     * @since 2.5.7
     *
     * Update (or create) mz_mbo_token option which holds datetime object
     * as well as AccessToken string.
     *
     * @return AccessToken string as administered by MBO Api.
     */
    public function get_and_save_staff_token() {
        // NS\MZMBO()->helpers->log( 'get_and_save_staff_token? ');

        $token = $this->get_mbo_results();
        // NS\MZMBO()->helpers->log( 'TOKEN? ' . print_r($token, true) );

        $current = new \DateTime();
        $current->format( 'Y-m-d H:i:s' );

        if ( ! ctype_alnum( $token ) ) {
            /*
             * In case we have returned something bad from the API
             * let's not save it to the option.
             */
            return $token;
        }

        update_option(
            'mz_mbo_token',
            array(
                'stored_time' => $current,
                'AccessToken' => $token,
            )
        );

        return $token;
    }

    /**
     * Save MBO AccessToken to WP option
     *
     * @since 2.5.9
     *
     * Update (or create) mz_mbo_token option which holds datetime object
     * as well as AccessToken string.
     *
     * @usedby MboV6Api::token_request()
     * @param string $token Token string returned from MBO Api.
     * @return AccessToken string as administered by MBO Api.
     */
    public function save_token_to_option( $token ) {

        $current = new \DateTime();
        $current->format( 'Y-m-d H:i:s' );

        if ( ! ctype_alnum( $token ) ) {
            /*
             * In case we have returned something bad from the API
             * let's not save it to the option.
             */
            return false;
        }

        update_option(
            'mz_mbo_token',
            array(
                'stored_time' => $current,
                'AccessToken' => $token,
            )
        );

        return $token;
    }
}
