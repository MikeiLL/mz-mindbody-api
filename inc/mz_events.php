<?php 

class MZ_MBO_Events {

	private $mz_mbo_globals;
	
	public function __construct(){
		require_once(MZ_MINDBODY_SCHEDULE_DIR .'inc/mz_mbo_init.inc');
		$this->mz_mbo_globals = new MZ_Mindbody_Init();
	}
	 	
	public function mbo_localize_main_js() {

		$main_js_params = array(
			'staff_preposition' => __('with', 'mz-mindbody-api'),
			'initial' => $this->initial_button_text,
			'mode_select' => 0,
			'is_current_week' => 0,
			'swap' => 0,
			'today' => date_i18n($this->mz_mbo_globals->mz_date_display, strtotime('today'))
			);

		wp_localize_script( 'mz_mbo_bootstrap_script', 'mz_mbo_bootstrap_script', $main_js_params);
 	}
 	
	public function mZ_mindbody_show_events ($atts, $account=0) {
		
		wp_enqueue_style('mZ_mindbody_schedule_bs', asset_path('styles/main.css'), false, null);
		wp_enqueue_script('modernizr', asset_path('scripts/modernizr.js'), array(), null, true);
		wp_enqueue_script('mz_mbo_bootstrap_script', asset_path('scripts/main.js'), array('jquery'), null, true);
		
		add_action('wp_footer', array($this, 'mbo_localize_main_js'));
		
		// optionally pass in a type parameter. Defaults to week.
		$atts = shortcode_atts( array(
			'location' => '1',
			'locations' => '',
			'account' => '0',
			'advanced' => '1',
				), $atts );
		$location = $atts['location'];
		$locations = $atts['locations'];
		$account = $atts['account'];
		$advanced = $atts['advanced'];
		$clientID = isset($_SESSION['GUID']) ? $_SESSION['client']['ID'] : '';
		
		/*
		 * This is for backwards compatibility for previous to using an array to hold one or more locations.
		*/
		if (($locations == '') || !isset($locations)) {
			if ($location == '') {
				$locations = array('1');
			}else{
				$locations = array($location);
			}
		}else{
			$locations = explode(', ', $atts['locations']);
			}
		
		if ($advanced == 1) {
			include_once(MZ_MINDBODY_SCHEDULE_DIR . 'lib/ajax.php');
		}
		
		$options = get_option( 'mz_mindbody_options' );
		// grab session type IDs for events
		$mz_sessions = explode(',', $options['mz_mindbody_eventID']);

		$return = '';
	
		$mz_date = empty($_GET['mz_date']) ? date_i18n('Y-m-d') : mz_validate_date($_GET['mz_date']);
	
		// only make API call if we have array session types set in Admin
		if (!empty($mz_sessions) && ($mz_sessions[0] != 0))
		{
			$mz_timeframe = array_slice(mz_getDateRange($mz_date, $this->mz_mbo_globals->mz_event_calendar_duration), 0, 1);
			
			//While we still need to support php 5.2 and can't use [0] on above
			$mz_timeframe = array_pop($mz_timeframe);
			
			$mz_timeframe = array_merge($mz_timeframe, array('SessionTypeIDs'=>$mz_sessions));
			
			// START caching configuration
			$mz_events_cache = "mz_events_cache";

			$mz_cache_reset = isset($this->mz_mbo_globals->options['mz_mindbody_clear_cache']) ? "on" : "off";

			if ( $mz_cache_reset == "on" )
			{
				delete_transient( $mz_events_cache );
			}
			
			if ( false === ( $mz_event_data = get_transient( $mz_events_cache ) ) || isset($_GET['mz_date']) ) {
				// Since event pagination won't happen often we can run the API when it's used.
				$mb = MZ_Mindbody_Init::instantiate_mbo_API();
				if ($account == 0) {
				$mz_event_data = $mb->GetClasses($mz_timeframe);
			}else{
				$mb->sourceCredentials['SiteIDs'][0] = $account; 
				$mz_event_data = $mb->GetClasses($mz_timeframe);
			}
				
			//echo $mb->debug();

			//Cache the mindbody call for 24 hours
			// TODO make cache timeout configurable.
			if( !isset($_GET['mz_date']) ) // Don't populate transient with future or past data
				set_transient($mz_events_cache, $mz_event_data, 60 * 60 * 24);
			}
			// END caching configuration
				//mz_pr($mz_event_data['GetClassesResult']['Classes']['Class']);

			if(!empty($mz_event_data['GetClassesResult']['Classes']['Class']))
			{
				$classes = $this->makeNumericArray($mz_event_data['GetClassesResult']['Classes']['Class']);
				$classes = sortClassesByDate($classes, $this->mz_mbo_globals->time_format, $locations, 0, array(), 
																	$advanced, 0, 0, 'events','DropIn');
				$number_of_events = count($classes);

				$return .= '<p>' .$this->mz_mbo_globals->mz_event_calendar_duration .' '. __('Day Event Calendar');
				$return .=  ' '. date_i18n($this->mz_mbo_globals->date_format, strtotime($mz_timeframe['StartDateTime']));
				$return .= ' - ';
				$return .= date_i18n($this->mz_mbo_globals->date_format, strtotime($mz_timeframe['EndDateTime'])).'</p>';
				if ($number_of_events >= 1)
				{
					//TODO Make this work - displaying number 20 with one event (correct on first page with 5 events).
					//$return .= ': ' . $number_of_events . ' '.__('event(s)').'</p>';

					$return .= mz_mbo_schedule_nav($mz_date, "Events", $this->mz_mbo_globals->mz_event_calendar_duration);
					$event_container = new html_element('div');
					$return .= '<div class="mz_mindbody_events">';
					$globals = new Global_Strings();
					$global_strings = $globals->translate_them();
					
					foreach($classes as $classDate => $classes)
					{
						foreach($classes['classes'] as $class)
						{
						  
							$event_container->set('text', $class->class_details );
							
							$return .= $event_container->build();
						}
					}
					$return .=	'<hr />';
				}
				else
				{
					$return .= '<h3>' . __('No events published this period.', 'mz-mindbody-api'). '</h3>';
				}
			}
			else
			{

				if(!empty($mz_event_data['GetClassesResult']['Message']))
				{
					$return .= $mz_event_data['GetClassesResult']['Message'];
				}
				else
				{
					$return .= '<p>' . sprintf(_n('%1$s Day Event Calendar', '%1$s Day Event Calendar', 'mz-mindbody-api'), $this->mz_mbo_globals->mz_event_calendar_duration);
					$return .=  ' '. date_i18n($this->mz_mbo_globals->mz_date_display, strtotime($mz_timeframe['StartDateTime']));
					$return .= ' - ';
					$return .= date_i18n($this->mz_mbo_globals->mz_date_display, strtotime($mz_timeframe['EndDateTime']));
					$return .= '<h3>' . __('No events published', 'mz-mindbody-api') . '. </h3>';
					//$return .= '<pre>'.print_r($mz_event_data,1).'</pre>';
				}

			}//EOF If Results/Else

		}
		else // no sessions set in admin
		{
			$return .= '<h2>'.__('Error: MBO Event Type IDs must be set in Admin Panel', 'mz-mindbody-api').'</h2>';
		}
		$return .= mz_mbo_schedule_nav($mz_date, _n("Event", "Events", 'mz-mindbody-api'), $this->mz_mbo_globals->mz_event_calendar_duration);
		return $return;

	}//EOF mZ_mindbody_show_events
	
	public function makeNumericArray($data) {
		return (isset($data[0])) ? $data : array($data);
	}

}//EOF MZ_MBO_Events
?>
