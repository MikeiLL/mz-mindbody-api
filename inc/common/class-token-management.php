<?php
namespace MZ_Mindbody\Inc\Common;

use MZ_Mindbody as NS;
use MZ_Mindbody\Inc\Core as Core;
use MZ_Mindbody\Inc\Common\Interfaces as Interfaces;

class Token_Management extends Interfaces\Retrieve {
	/**
	 * Intermittently fetch, store and serve mbo api UserTokens.
	 * @since 2.5.7
	 *
	 * The MBO V6 API serves and requires UserTokens which expire after seven days. So
	 * we will store a valid UserToken in the db options table, intermittently updating it.
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
	 
	 public function get_mbo_results() {
	 	
	 	$mb = $this->instantiate_mbo_API();
	 	
        if ( !$mb ) return false;
        
        return $mb->TokenIssue();
        
	 }
}
