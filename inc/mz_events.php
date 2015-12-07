<?php 

class MZ_MBO_Events {

	private $mz_mbo_globals;
	
	public function __construct(){
		require_once(MZ_MINDBODY_SCHEDULE_DIR .'inc/mz_mbo_init.inc');
		$this->mz_mbo_globals = new MZ_Mindbody_Init();
	}
	
	public function mZ_mindbody_show_events ($atts, $account=0) {
		
		wp_enqueue_style('mZ_mindbody_schedule_bs', asset_path('styles/main.css'), false, null);
		wp_enqueue_script('modernizr', asset_path('scripts/modernizr.js'), array(), null, true);
		wp_enqueue_script('mz_mbo_bootstrap_script', asset_path('scripts/main.js'), array('jquery'), null, true);
		
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
		
		$options = get_option( 'mz_mindbody_options' );
		// grab session type IDs for events
		$mz_sessions = explode(',', $options['mz_mindbody_eventID']);

		$return = '';
	
		$mz_date = empty($_GET['mz_date']) ? date_i18n('Y-m-d') : mz_validate_date($_GET['mz_date']);
	
		// only make API call if we have sessions set
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
			
			if (isset($_GET) || ( false === ( $mz_event_data = get_transient( $mz_events_cache ) ) ) ) {
				$mb = MZ_Mindbody_Init::instantiate_mbo_API();
				if ($account == 0) {
				$mz_event_data = $mb->GetClasses($mz_timeframe);
			}else{
				$mb->sourceCredentials['SiteIDs'][0] = $account; 
				$mz_event_data = $mb->GetClasses($mz_timeframe);
			}
				
			$mz_event_data = $mb->GetClasses($mz_timeframe);

			//echo $mb->debug();

			//Cache the mindbody call for 24 hour2
			// TODO make cache timeout configurable.
			set_transient($mz_events_cache, $mz_event_data, 60 * 60 * 24);
			}
			// END caching configuration

			if(!empty($mz_event_data['GetClassesResult']['Classes']['Class']))
			{
				$classes = $this->makeNumericArray($mz_event_data['GetClassesResult']['Classes']['Class']);
				$classes = sortClassesByDate($classes, $this->mz_mbo_globals->time_format, $locations, 'DropIn');
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
					$return .= '<div class="mz_mindbody_events">';
					$return .= '<table class="table mz_mindbody_events">';
					$globals = new Global_Strings();
					$global_strings = $globals->translate_them();
					
					foreach($classes as $classDate => $classes)
					{
						foreach($classes as $class)
						{
							if (!(($class['IsCanceled'] == 'TRUE') && ($class['HideCancel'] == 'TRUE') 
											&& in_array($locations, $class['Location']['SiteID'])))
							{
								$sDate = date_i18n('m/d/Y', strtotime($class['StartDateTime']));
								$sLoc = $class['Location']['ID'];
								$studioid = $class['Location']['SiteID'];
								$sclassid = $class['ID'];
								// why is this hardcoded?
								$sType = -7;
								$isAvailable = $class['IsAvailable'];
								if (empty($class['ClassDescription']['ImageURL']))
									$image = '';
									else
									$image = '<img class="mz_mindbody_events_img" src="' .$class['ClassDescription']['ImageURL'] . '">';
								$sTG = $class['ClassDescription']['Program']['ID'];
								$eventLinkURL = "https://clients.mindbodyonline.com/ws.asp?sDate={$sDate}&amp;sLoc={$sLoc}&amp;sTG={$sTG}&amp;sType={$sType}&amp;sclassid={$sclassid}&amp;studioid={$studioid}";
								$className = $class['ClassDescription']['Name'];
								$startDateTime = date_i18n($this->mz_mbo_globals->date_format . ' ' .$this->mz_mbo_globals->time_format, strtotime($class['StartDateTime']));
								$classDescription = $class['ClassDescription']['Description'];
								$endDateTime = date_i18n($this->mz_mbo_globals->date_format . ' ' . $this->mz_mbo_globals->time_format, strtotime($class['EndDateTime']));
								$staffName = $class['Staff']['Name'];
								$ItemType = $class['ClassDescription']['Program']['Name'];
								$enrolmentType = $class['ClassDescription']['Program']['ScheduleType'];
								$day_and_date =  date_i18n("D F d", strtotime($classDate));

								$return .= '<tr class="mz_description_holder"><td>';
								$return .= "<span class='mz_event_name'>$className</span>";
								if ($advanced == 1) {
									$add_to_class_nonce = wp_create_nonce( 'mz_MBO_add_to_class_nonce');
									if ($clientID == ''){
										 $return .= $isAvailable ? '<br/><a class="btn mz_add_to_class" href="'.home_url().
										 '/login">'.__('Login to Sign-up', 'mz-mindbody-api') . '</a><br/>' : '';
										  }else{
									  $return .= $isAvailable ? '<br/><a id="mz_add_to_class" class="btn mz_add_to_class"' 
										. ' data-nonce="' . $add_to_class_nonce 
										. '" data-classID="' . $sclassid  
										. '" data-clientID="' . $clientID 
										. '">' .
									  '<span class="signup">'. $global_strings['sign_up'] . '</span><span class="count" style="display:none">0</span></a>': '';
									  $return .= '<br/><div id="visitMBO" class="btn visitMBO" style="display:none">';
									  $return .= '<a href="'.$eventLinkURL.'" target="_blank">Manage on MindBody Site</a></div>';
									  }
								}else{
									$return .= '<br/><a class="btn" href="' . $eventLinkURL . '" target="_blank">' . __('Sign-Up', 'mz-mindbody-api') . '</a><br/>';
								}
								$return .= '<span class="mz_event_staff">'.$day_and_date.', ' . date_i18n('g:i a', strtotime($startDateTime)).' - ';
								$return .= date_i18n('g:i a', strtotime($endDateTime)) . '</span>';
								$return .= '<p class="mz_event_staff_name">'.$global_strings['with'] . '&nbsp;' . $staffName.'</p>';						
							

								$return .= '<div class="mz_mindbody_event_description">';
								$return .=  $image;
								$return .= "<div class='mz_event_text'>$classDescription</div>";
								$return .= "</div>";
								$return .= '</td></tr>';
							}
						}
					}
					$return .=	'</table></div>';
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
