<?php
namespace MZ_Mindbody\Inc\Common\Interfaces;

use MZ_Mindbody\Inc\Core as Core;
use MZ_Mindbody\Inc\Libraries as Libraries;

abstract class Retrieve {

	protected $mbo_account;
	
	public function __construct(){
		$this->mbo_account = 0;
	}
	
	/*
	 * Configure the MBO API object with Credentials stored in DB
	 *
	 * @since 2.4.7
	 */
	public function instantiate_mbo_API () {

        if (Core\Init::$basic_options == 'Error: No Options' || empty(Core\Init::$basic_options)) {
            return false;
        } else {
            return new Libraries\MBO_API(array(
                "SourceName" => Core\Init::$basic_options['mz_source_name'],
                'Password' => Core\Init::$basic_options['mz_mindbody_password'],
                'SiteIDs' => array(Core\Init::$basic_options['mz_mindbody_siteID'])
            ));
        }
	}

    /*
     * Generate a name for transient
     *
     * Name will include whatever strings are needed to
     * differentiate it from other transients storing
     * MBO results
     *
     * Used by $this->get_mbo_results
     *
     * @param $attributes array stores attributes that will make
     * transient name unique.
     *
     * @since 2.4.7
     */
    protected function generate_transient_name ( $attributes = array() ) {
        $transient_string = 'mz_mindbody';
        foreach ($attributes as $k => $attr) {
            $transient_string .= '_' . $attr ;
        }
        // append today's date
        $transient_string .= date('Y-m-d', current_time( 'timestamp'));
        return $transient_string;
    }
    
    /*
     * Log via Sandbox dev plugin
     * 
     * If sandbox plugin located at
     * https://github.com/MikeiLL/mz-mbo-sandbox
     * is loaded, use it to log debug info.
     */
    private function sandbox() {
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		if (is_plugin_active( 'mz-mbo-sandbox/mZ-mbo-sandbox.php' )):
				$mbo_sandbox = new MZ_MBO_Sandbox_Admin('1.0');
				$mbo_sandbox->load_sandbox();
				$mbo_sandbox->run_sandbox("MBO Instantiation via " . $_SERVER['REQUEST_URI']);
		endif;
    }


    /*
     * Get results from MBO
     *
     * @since 2.4.7
     */
    abstract public function get_mbo_results($timestamp);

}
