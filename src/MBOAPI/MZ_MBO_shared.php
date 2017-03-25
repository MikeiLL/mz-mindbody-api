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
 
new MZ_MBO_shared();
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


?>
