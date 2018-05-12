<?php
namespace MZ_Mindbody\Inc\Common\Interfaces;

use MZ_Mindbody\Inc\Libraries as Libraries;

abstract class Retrieve {

	public $mz_date_display = "D F d";
	public $options;
	public $mz_event_calendar_duration;
	public $time_format;
	public $date_format;
	public $start_of_week;
	public $mbo_account;
	
	public function __construct(){
		$this->options = get_option('mz_mindbody_options','Error: No Options');
		$this->mz_event_calendar_duration = isset($this->options['mz_mindbody_eventsDuration']) ? $this->options['mz_mindbody_eventsDuration'] : '60';
		$this->time_format = get_option('time_format');
		$this->date_format = get_option('date_format');
		$this->start_of_week = get_option('start_of_week');
	}
	
	/*
	 * Return current week start and end timestamps.
	 *
	 * @since 2.4.7
	 * @return array 'start', 'end' of current week in timestamps
	 */
	public function current_week(){
		return get_weekstartend(current_time( 'mysql' ), $this->start_of_week);
	}
	
	/*
	 * Return timestamp of seven days from now.
	 *
	 * @since 2.4.7
	 *
	 * @return timestamp of seven days from now
	 */
	public function seven_days_from_now(){
		return strtotime("+7 day", current_time( 'timestamp' ));
	}
	
	/*
	 * Displayable current week start and end timestamps.
	 *
	 * @since 2.4.7
	 * @return html string of start and end of current week
	 */
	public function current_week_display(){
		$time_frame = $this->current_week();
		$return = 'Week start: ' . date('M d, Y', $time_frame[start]) . '<br/>';
		$return .= 'Week end: ' . date('M d, Y', $time_frame[end]);
		return $return;
	}
	
	/*
	 * Configure the MBO API object with Credentails from DB
	 *
	 * @since 2.4.7
	 */
	public function instantiate_mbo_API () {

		if ($this->options != 'Error: No Options') {
			$mb  = new Libraries\MBO_API(array(
								"SourceName" => $this->options['mz_source_name'],
								'Password' => $this->options['mz_mindbody_password'],
								'SiteIDs' => array($this->options['mz_mindbody_siteID'])
							)); 
			} else {
				echo '<div class="error">Mindbody Credentials Not Set</div>';
				$mb  = new Libraries\MBO_API(array(
								"SourceName" => '',
								'Password' => '',
								'SiteIDs' => array('')
							)); 
			}

		return $mb;
	}
	
	/*
	 * Get results from MBO
	 *
	 * @since 2.4.7
	 */
	//abstract public function get_mbo_results();
    
    /*
     * Log via Sanbox dev plugin
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

	abstract public function time_frame($start_time);
}
