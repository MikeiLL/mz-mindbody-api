<?php
/**
 * This file contains a class that holds some "global" variables and the class that instantiates the MBO object.
 *
 * This file contains global variables and the class that instantiates the MBO object. Possible it should be
 * moved into the lib directory instead of inc, as it's more of a library than an include file. The inc folder
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
	}
	
	static function instantiate_mbo_API () {
		//mz_pr("OKAY We ARE DOING IT.");
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
