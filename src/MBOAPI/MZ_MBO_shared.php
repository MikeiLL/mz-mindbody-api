<?php
namespace mZoo\MBOAPI;
/**
 * This file contains a class that holds some "global" variables used throughout the plugin.
 *
 * This file contains global variables used throughout the plugin.
 *
 * @since 2.1.0
 *
 * @package MZMBO
 * 
 */
 
/**
 * The MZ MBO Shared class holds variables which are shared between different view methods.
 *
 * @since    2.4.0
 */
 
new __NAMESPACE__ . \MZ_MBO_shared();
class MZ_MBO_shared {
	static $mz_date_display = "l, F j";
	static $mz_options;
	static $mz_event_calendar_duration;
	static $time_format;
	static $date_format;
	static $count;

	
	public function __construct(){
		/*echo "<br />in MZ MBO Shared<br />";
		timer_stop(true);
		echo "<br />".self::$count."<br />";
		self::$count++;
		$trace = debug_backtrace();
$caller = $trace[1];

echo "Called by {$caller['function']}";
if (isset($caller['class']))
    echo " in {$caller['class']}";*/
		self::$mz_options = isset(self::$mz_options) ? self::$mz_options : get_option('mz_mindbody_options');
		self::$mz_event_calendar_duration = isset(self::$mz_options['mz_mindbody_eventsDuration']) ? self::$mz_options['mz_mindbody_eventsDuration'] : '60';
		self::$time_format = (isset(self::$time_format) && (self::$time_format != '')) ? self::$time_format : get_option('time_format');
		self::$date_format = (isset(self::$date_format) && (self::$date_format != ''))  ? self::$date_format : get_option('date_format');
	}
}
 

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
		require_once(plugin_dir_path(dirname(__FILE__)) . '/mindbody-php-api/MB_API.php');
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
