<?php
namespace mZoo\MBOAPI;
/**
 * This file contains the class that instantiates the MBO object.
 *
 * This file contains the class that instantiates the MBO object. The inc folder
 * mostly contains files which display something in the browser.
 *
 * @since 2.1.0
 *
 * @package MZMBO
 * 
 */
 

/**
 * The MZ Mindbody Init holds variables which are shared between different view methods.
 *
 * 
 *
 * @since    2.1.0
 */
class MZ_Mindbody_Init {
	
	public $mz_date_display = "D F d";
	public $my_options;
	public $mz_event_calendar_duration;
	public $time_format;
	public $date_format;
	
	public function __construct(){
		$this->options = get_option('mz_mindbody_options');
		$this->mz_event_calendar_duration = isset($options['mz_mindbody_eventsDuration']) ? $options['mz_mindbody_eventsDuration'] : '60';
		$this->time_format = get_option('time_format');
		$this->date_format = get_option('date_format');
		// Include once in case called from dependent plugin
		$plugin_path = plugin_dir_path(dirname(__FILE__));
	}
	
	static function instantiate_mbo_API () {
		
		// If dev plugin is loaded, use it to log MBO calls
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		if (is_plugin_active( 'mz-mbo-sandbox/mZ-mbo-sandbox.php' )):
				$mbo_sandbox = new MZ_MBO_Sandbox_Admin('1.0');
				$mbo_sandbox->load_sandbox();
				$mbo_sandbox->run_sandbox("MBO Instantiation via " . $_SERVER['REQUEST_URI']);
		endif;
		//mz_pr("i ran");
		$options = get_option( 'mz_mindbody_options','Error: Mindbody Credentials Not Set' );
		if ($options != 'Error: Mindbody Credentials Not Set') {
			$mb  = new MB_API(array(
								"SourceName" => $options['mz_source_name'],
								'Password' => $options['mz_mindbody_password'],
								'SiteIDs' => array($options['mz_mindbody_siteID'])
							)); 
			}else{
				echo '<div class="error">Mindbody Credentials Not Set</div>';
				$mb  = new MB_API(array(
								"SourceName" => '',
								'Password' => '',
								'SiteIDs' => array('')
							)); 
			}
		//array_push(MZ_MBO_Instances::$instances_of_MBO, $mb);
		return $mb;
		}
		
}

class MZ_MBO_Instances {
	public static $instances_of_MBO = array();
	}

?>
