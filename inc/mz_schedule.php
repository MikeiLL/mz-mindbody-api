<?php
	
class MZ_Mindbody_Schedule_Display {

	private $mz_mbo_globals;
	private $locations_dictionary = array();
	static $time_tracker;	
	
	public function __construct(){
		require_once(MZ_MINDBODY_SCHEDULE_DIR .'inc/mz_mbo_init.inc');
		$this->mz_mbo_globals = new MZ_Mindbody_Init();
		$this::$time_tracker = date('Fd', strtotime("now"));
	}
	
	
 	public function mbo_localize_main_js() {

		$main_js_params = array(
			'staff_preposition' => __('with', 'mz-mindbody-api')
			);
	
		wp_localize_script( 'mz_mbo_bootstrap_script', 'mz_mbo_bootstrap_script', $main_js_params);


 	}
	
	public function mZ_mindbody_show_schedule( $atts, $account=0 )
	{
		wp_enqueue_style('mZ_mindbody_schedule_bs', asset_path('styles/main.css'), false, null);
		wp_enqueue_script('modernizr', asset_path('scripts/modernizr.js'), array(), null, true);
		wp_enqueue_script('mz_mbo_bootstrap_script', asset_path('scripts/main.js'), array('jquery'), null, true);
		
		add_action('wp_footer', array($this, 'mbo_localize_main_js'));

		    
		// optionally pass in a type parameter. Defaults to week.
		$atts = shortcode_atts( array(
			'type' => 'week',
			'location' => '', // stop using this eventually, in preference "int, int" format
			'locations' => '',
			'account' => '0',
			'filter' => '0',
			'grid' => '0',
			'advanced' => '0',
			'hide' => '',
			'class_types' => '',
			'show_registrants' => '0',
			'hide_cancelled' => '1',
			'registrants_count' => '0'
				), $atts );
		$type = $atts['type'];
		$location = $atts['location'];
		$locations = $atts['locations'];
		$account = $atts['account'];
		$filter = $atts['filter'];
		$grid = $atts['grid'];
		$advanced = $atts['advanced'];
		$class_types = $atts['class_types'];
		$clientID = isset($_SESSION['GUID']) ? $_SESSION['client']['ID'] : '';
		$show_registrants = $atts['show_registrants'];
		$hide_cancelled = $atts['hide_cancelled'];
		$registrants_count = $atts['registrants_count'];
		
		$sign_up_text = __('Sign-Up', 'mz-mindbody-api');
		$manage_text = __('Manage on MindBody Site', 'mz-mindbody-api');
		
		//Build caache based on shortcode attributes.
		$mz_schedule_cache = 'mz_sched_che';
		$mz_schedule_timer = 'mz_sched_tim';
		
		foreach ($atts as $key=>$value){
			if($value=='0' || $value=='') continue;
			$mz_schedule_cache .= '_'.substr($key,1,1).'_'.$value;
		}
		//Add date to cache
		if (!empty($_GET['mz_date']))
			$mz_schedule_cache .= '_'.$_GET['mz_date'];
	
		if (($grid == 1) && ($type == 'day')) {
			return '<div style="color:red"><h2>'.__('Grid Calendar Incompatible with Single Day Mode!', 'mz_mndbody_api').'</h2></div>';
		}
		
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

		if ($grid == 0) {
			$mz_date = empty($_GET['mz_date']) ? date_i18n('Y-m-d',current_time('timestamp')) : mz_validate_date($_GET['mz_date']);
			}else{
			$hide = explode(', ', $atts['hide']);
			$which_monday = (strtotime('this monday') > current_time('timestamp')) ? 'last monday' : 'this monday';
			$mz_date = empty($_GET['mz_date']) || ( $_GET['mz_date'] == date_i18n('Y-m-d',current_time('timestamp')) ) ? date_i18n('Y-m-d',strtotime($which_monday)) : mz_validate_date($_GET['mz_date']);
			}

		if ($type=='day')
		{
			$mz_timeframe = array_slice(mz_getDateRange($mz_date, 1), 0, 1);
		}
		else
		{   
			$mz_timeframe = array_slice(mz_getDateRange($mz_date, 7), 0, 1);
		}
		//While we still need to support php 5.2 and can't use [0] on above
		$mz_timeframe = array_shift($mz_timeframe);

	  // START caching

		$mz_cache_reset = isset($this->mz_mbo_globals->options['mz_mindbody_clear_cache']) ? "on" : "off";
		
		$last_look = get_transient($mz_schedule_timer);
		
		// Insure that MBO info is up to date for each new day.
		if ( $mz_cache_reset == "on" || $last_look != $this::$time_tracker){
			//delete_transient( $mz_schedule_cache );
			// Replaced above with the following so we could deal with multiple date ranges being called.
			global $wpdb;
			$wpdb->query( "DELETE FROM `$wpdb->options` WHERE `option_name` LIKE ('%mz_sched_che%')" );
		}
		//Uncomment to look at transients
		//global $wpdb;
		//$all_of_us = $wpdb->get_results( "SELECT * FROM `$wpdb->options` WHERE `option_name` LIKE ('%mz_sched_%')" );
		//mz_pr($all_of_us);
		
		if ( false === get_transient( $mz_schedule_cache ) ) {
			/* If receiving parameters in $_GET or transient deleted we need to send a new date range to reset transient
			 * uncomment line mz_pr("OKAY We ARE DOING IT."); in inc/mz_mbo_init.php
			 * to see confirmation in broser of if MBO was called with the following 
			 * line.
			*/
			$mb = MZ_Mindbody_Init::instantiate_mbo_API();
			if ($mb == 'NO_SOAP_SERVICE') {
				mz_pr($mb);
				}
				
			if ($account == 0) {
				$mz_schedule_data = $mb->GetClasses($mz_timeframe);
				}else{
				$mb->sourceCredentials['SiteIDs'][0] = $account; 
				$mz_schedule_data = $mb->GetClasses($mz_timeframe);
				}

			//Cache the mindbody call for 24 hours
			//But only if we are NOT loading for different week than current
			// TODO make cache timeout configurable.
			
			set_transient($mz_schedule_timer, $this::$time_tracker, 60 * 60 * 24);
			set_transient($mz_schedule_cache, $mz_schedule_data, 60 * 60 * 24);

		   // END caching*/
		}
		
		$mz_schedule_data = get_transient( $mz_schedule_cache );

		$return = '';

		if(!empty($mz_schedule_data['GetClassesResult']['Classes']['Class']))
		{
			$mz_days = $this->makeNumericArray($mz_schedule_data['GetClassesResult']['Classes']['Class']);
			
		//based on shortcode arguments, potentially remove array elements
			if ($class_types != ''):
				$class_types = explode(', ', $atts['class_types']);
					$i = 0;
					foreach ($mz_days as $day_of_classes) {
						$sessionTypeName = $day_of_classes['ClassDescription']['SessionType']['Name'];
						if (!in_array($sessionTypeName, $class_types)){
							unset($mz_days[$i]);
							}
						$i++;
						}
				$mz_days = array_values($mz_days);
			endif;
			
			if ($hide_cancelled == 1):
					$i = 0;
					foreach ($mz_days as $day_of_classes) {
						if ($day_of_classes['IsCanceled'] == 1){
							unset($mz_days[$i]);
							}
						$i++;
						}
					$mz_days = array_values($mz_days);
			endif;

			if ($grid == 0){
				// Order class matrix by date and time
				$mz_days = sortClassesByDate($mz_days, $this->mz_mbo_globals->time_format, $locations);
				$a_class = new Single_event($mz_days['2016-02-24'][1]);
					mz_pr($a_class);
			//mz_pr($mz_days['2016-02-24'][0]);
				}else{
				// Create matrix of existing class times with empty schedule slots, sequenced by day 
				// Each "class" is an instance of Single_event
				$mz_days = sortClassesByTimeThenDay($mz_days, $this->mz_mbo_globals->time_format, $locations);
			}
		}	// EOF if ['GetClassesResult']['Classes']['Class'] is populated
	}//EOF mZ_show_schedule
	
	private function classLinkMaker($staffName, $className, $classDescription, $sclassidID, $staffImage, $show_registrants) {
			/* Build and return an href object for each class/event*/
			
			$class_name_css = 'modal-toggle mz_get_registrants ' . sanitize_html_class($className, 'mz_class_name');
			
			$linkArray = array(
												'data-staffName'=>$staffName,
												'data-className'=>$className,
												'data-classDescription'=>rawUrlEncode($classDescription),
												'class'=> $class_name_css,
												'text'=>$className
												);
												
								if ($show_registrants == 1){
												$get_registrants_nonce = wp_create_nonce( 'mz_MBO_get_registrants_nonce');
												$linkArray['data-nonce'] = $get_registrants_nonce;
												$linkArray['data-target'] = "#registrantModal";  
												$linkArray['data-classID'] = $sclassidID;
											} else {
												$linkArray['data-target'] = "#mzModal";
											}
								if ($staffImage != ''):
									$linkArray['data-staffImage'] = $staffImage;
								endif;
						
				$class_name_link = new html_element('a');
				$class_name_link->set('href', MZ_MINDBODY_SCHEDULE_URL . 'inc/modal_descriptions.php');
				$class_name_link->set($linkArray);
				
				return $class_name_link;
	}
	
	public function makeNumericArray($data) {
		return (isset($data[0])) ? $data : array($data);
	}
	
	public function add_filter_table() {
		wp_enqueue_script('filterTable', asset_path('scripts/mz_filtertable.js'), array('jquery'), null, true);
		}

	public function initialize_filter() {
		  wp_localize_script('mz_mbo_bootstrap_script', 'mz_mindbody_api_i18n', array(
			'filter_default' => __('by teacher, class type', 'mz-mindbody-api'),
			'quick_1' => __('morning', 'mz-mindbody-api'),
			'quick_2' => __('afternoon', 'mz-mindbody-api'),
			'quick_3' => __('evening', 'mz-mindbody-api'),
			'label' => __('Filter', 'mz-mindbody-api'),
			'selector' => __('All Locations', 'mz-mindbody-api'),
			'Locations_dict' => $this->locations_dictionary
			));

		?>
		
		<!-- Start mZ_mindbody-api filterTable configuration -->
		<script type="text/javascript">
			$(document).ready(function() {
				var stripeTable = function(table) { //stripe the table (jQuery selector)
						table.find('tr').removeClass('striped').filter(':visible:even').addClass('striped');
					};

					$('table.mz-schedule-filter').filterTable({
						callback: function(term, table) { stripeTable(table); }, //call the striping after every change to the filter term
						placeholder: mz_mindbody_api_i18n.filter_default,
						highlightClass: 'alt',
						inputType: 'search',
						label: mz_mindbody_api_i18n.label,
						selector: mz_mindbody_api_i18n.selector,
						quickListClass: 'mz_quick_filter',
						quickList: [mz_mindbody_api_i18n.quick_1, mz_mindbody_api_i18n.quick_2, mz_mindbody_api_i18n.quick_3],
						locations: mz_mindbody_api_i18n.Locations_dict
					});
					stripeTable($('table.mz-schedule-filter')); //stripe the table for the first time
				});
		</script>
		<!-- End mZ_mindbody-api filterTable configuration -->		

		
		<?php
	}
	
	private function view_transients () {
		global $wpdb;
		$sql = "SELECT `option_name` AS `name`, `option_value` AS `value`
				FROM  $wpdb->options
				WHERE `option_name` LIKE '%transient_%'
				ORDER BY `option_name`";

		$results = $wpdb->get_results( $sql );
		$transients = array();

		foreach ( $results as $result )
		{

			if ( 0 !== strpos($result->name, '_transient_mz_schedule') )
				$transients['mz_plugin_transient'][ $result->name ] = maybe_unserialize( $result->value );
				
			if ( 0 !== strpos( $result->name, '_site_transient_' ) )
			{
				if ( 0 === strpos( $result->name, '_site_transient_timeout_') )
					$transients['site_transient_timeout'][ $result->name ] = $result->value;
				else
					$transients['site_transient'][ $result->name ] = maybe_unserialize( $result->value );
			}
			else
			{
				if ( 0 === strpos( $result->name, '_transient_timeout_') )
					$transients['transient_timeout'][ $result->name ] = $result->value;
				else
					$transients['transient'][ $result->name ] = maybe_unserialize( $result->value );
			}
		}
		print '<pre>$transients = ' . esc_html( var_export( $transients, TRUE ) ) . '</pre>';
		}
	
}// EOF MZ_Mindbody_Schedule_Display Class



?>
