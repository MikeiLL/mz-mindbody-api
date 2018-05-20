<?php
namespace MZ_Mindbody\Inc\Common\Interfaces;

use MZ_Mindbody\Inc\Core as Core;
use MZ_Mindbody\Inc\Libraries as Libraries;

abstract class Retrieve {

	protected $mbo_account;
	public $atts;
	
	public function __construct($atts = array()){
		$this->mbo_account = 0;
		$this->atts = $atts;
	}
	
	/*
	 * Configure the MBO API object with Credentials stored in DB
	 *
	 * @since 2.4.7
	 */
	public function instantiate_mbo_API () {

        $basic_options = get_option('mz_mbo_basic','Error: No Options');

        if ($basic_options == 'Error: No Options' || empty($basic_options)) {
            return false;
        } else {
            return new Libraries\MBO_API(array(
                "SourceName" => $basic_options['mz_source_name'],
                'Password' => $basic_options['mz_mindbody_password'],
                'SiteIDs' => array($basic_options['mz_mindbody_siteID'])
            ));
        }
	}

    /*
     * Generate a name for transient
     *
     * Name will include whatever strings are needed to
     * differentiate it from other transients storing
     * MBO results. Attr values are substringed to limit
     * length of string.
     *
     * Used by $this->get_mbo_results
     *
     * @param $attributes array stores attributes that will make
     * transient name unique.
     *
     * @since 2.4.7
     */
    protected function generate_transient_name ($shortcode = 'sc') {
        $transient_string = 'mz_mindbody_' . $shortcode .'_';
        foreach ($this->atts as $k => $attr) {
        		if (empty($attr)) continue;
            if (is_array($attr)) $attr = implode('_', $attr);
            $transient_string .= '_' . $k . '_' .substr($attr,0,4);
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
    abstract public function get_mbo_results();

}
