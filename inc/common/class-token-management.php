<?php
namespace MZ_Mindbody\Inc\Common;

use MZ_Mindbody as NS;
use MZ_Mindbody\Inc\Core as Core;
use MZ_Mindbody\Inc\Common\Interfaces as Interfaces;

class Token_Management extends Interfaces\Retrieve {
	/**
	 * Intermittently fetch, store and serve mbo api AccessTokens.
	 * @since 2.5.7
	 *
	 * The MBO V6 API serves and requires AccessTokens which expire after seven days. So
	 * we will store a valid AccessToken in the db options table, intermittently updating it.
	 *
	 * wp_schedule_event
	 *
	 *
	 */
	
	protected $mbo_account;
	 
    public function __construct(){

        parent::__construct();

        if (!empty(Core\MZ_Mindbody_Api::$basic_options['mz_mindbody_siteID'])):
            $this->mbo_account = !empty($atts['account']) ? $atts['account'] : Core\MZ_Mindbody_Api::$basic_options['mz_mindbody_siteID'];
        else:
            $this->mbo_account = '-99';
        endif;
        
    }
	 
	 /**
     * 
     * Serve MBO AccessToken either from WP option or from MBO API
     *
     * @since 2.5.7
     *
     *
     * @return AccessToken string as administered by MBO Api
     */
	 public function get_mbo_results() {

	 	$mb = $this->instantiate_mbo_API();
	 	
        if ( !$mb ) return false;
        
        $response = $mb->TokenIssue();
                        
        return $response->AccessToken;
        
	 }
	 
	 
	 /**
     * 
     * Serve MBO AccessToken either from WP option or from MBO API
     *
     * @since 2.5.7
     *
     * Check stored token for existence and time saved. If LT 12 hours old 
     * serve, if not, get from MBO API and save via save_token.
     *
     * @return AccessToken string as administered by MBO Api
     */
	 public function serve_token() {
	 	// Get the option or return an empty array with two keys
	 	
	 	$stored_token = get_option('mz_mbo_token', ['stored_time' => '', 'AccessToken' => '']);
	 	
	 	if (empty( $stored_token['AccessToken'] )) {
	 	
	 			return $this->save_token();
	 		
	 	} else {
	 		// If there is a mz_mbo_token, let's see if it's less than 12 hours old
	 		
			$current = new \DateTime();
			$current->format('Y-m-d H:i:s');
			$twleve_hours_ago = clone $current;
			$twleve_hours_ago->sub(new \DateInterval('PT12H'));
		
			if ( is_object($stored_token['stored_time']) && ($stored_token['stored_time'] > $twleve_hours_ago) && !empty($stored_token['AccessToken']) ) {
		
				return $stored_token['AccessToken'];
			
			} else {
				// If it's old, get and save a new one:

				return $this->save_token();
				
			}
	 	
	 	}
	 }
	 
	 /**
     * 
     * Save MBO AccessToken to WP option
     *
     * @since 2.5.7
     *
     * Update (or create) mz_mbo_token option which holds datetime object 
     * as well as AccessToken string
     *
     * @return AccessToken string as administered by MBO Api
     */
	 private function save_token() {
	 
	 	$token = $this->get_mbo_results();
	 	
		$current = new \DateTime();
		$current->format('Y-m-d H:i:s');
		
	 	update_option('mz_mbo_token', [
	 		'stored_time' => $current, 
	 		'AccessToken' => $token
	 	]);
	 		 	
	 	return $token;
	 }
}
