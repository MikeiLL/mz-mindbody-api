<?php
	
class MZ_Mindbody_Schedule_Display {

	private $mz_mbo_globals;
	private $locations_dictionary = array();
	static $time_tracker;	
	private $locations_count; // Used to know how many times we need to check when populating dict
	private $locations_dict_length;
	private $time_slot;
	
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
		$hide = $atts['hide'];
		$class_types = $atts['class_types'];
		// moved this to Class object class
		//$clientID = isset($_SESSION['GUID']) ? $_SESSION['client']['ID'] : '';
		$show_registrants = $atts['show_registrants'];
		$hide_cancelled = $atts['hide_cancelled'];
		$registrants_count = $atts['registrants_count'];
		
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
		} else {
			$locations = array_map('trim', explode(',', $atts['locations']));
		}
		$this->locations_count = count($locations);

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
			
			$return .= '<div id="mz_mbo_schedule" class="mz_mbo_schedule">';
			if ($type==__('week','mz-mindbody-api')){
				$return .= mz_mbo_schedule_nav($mz_date, __('Week', 'mz-mindbody-api'));
			}

		if ($filter == 1) {
				if ($grid == 1):
					$tbl = new HTML_Table('', 'mz-schedule-filter mz-schedule-grid');
				else:
					$tbl = new HTML_Table('', 'mz-schedule-filter');
				endif;
			}else{
				if ($grid == 1):
					$tbl = new HTML_Table('', 'mz-schedule-table mz-schedule-grid');
				else:
					$tbl = new HTML_Table('', 'mz-schedule-table');
				endif;
			}

			if ($grid == 0){
			
				$mz_days = sortClassesByDate($mz_days, $this->mz_mbo_globals->time_format, $locations, 
																						$hide_cancelled, $hide, $advanced, $show_registrants,
																						$registrants_count, 'horizontal');
																						
				foreach($mz_days as $classDate => $mz_classes) {   
			
					$tbl->addRow('header');
					// arguments: cell content, class, type (default is 'data' for td, pass 'header' for th)
					// can include associative array of optional additional attributes
		
					$tbl->addCell(date_i18n($this->mz_mbo_globals->mz_date_display, strtotime($classDate)), 'mz_date_display', 'header', array('scope'=>'header'));
					$tbl->addCell(__('Class Name', 'mz-mindbody-api'), 'mz_classDetails', 'header', array('scope'=>'header'));
					$tbl->addCell(__('Instructor', 'mz-mindbody-api'), 'mz_staffName', 'header', array('scope'=>'header'));
					$tbl->addCell(__('Class Type', 'mz-mindbody-api'), 'mz_sessionTypeName', 'header', array('scope'=>'header'));
			
					$tbl->addTSection('tbody');
					foreach($mz_classes['classes'] as $class)
						{
							// start building table rows
							$row_css_classes = 'mz_description_holder mz_schedule_table mz_location_'.$class->sLoc;
							$tbl->addRow($row_css_classes);
							$tbl->addCell($class->time_of_day, 'hidden', 'data');
							$tbl->addCell(date_i18n($this->mz_mbo_globals->time_format, strtotime($class->startDateTime)) . ' - ' . 
											date_i18n($this->mz_mbo_globals->time_format, strtotime($class->endDateTime)) .
											'<br/>' . $class->signupButton . ' ' . $class->toward_capacity , 'mz_date_display' );
				
							//class name link

							$tbl->addCell($class->class_details, "class_name_cell");

							$tbl->addCell($class->staffName, 'mz_staffName');
							$tbl->addCell($class->sessionTypeName, 'mz_sessionTypeName');
						}
					}
					
				$tbl->addTSection('tfoot');
				$tbl->addRow();
				$tbl->addCell('','','', array('colspan' => 4));
			
				$return .= $tbl->display();
				
				} else {
				// Display GRID
				$week_starting = date_i18n($this->mz_mbo_globals->date_format, strtotime($mz_date)); 
				
				$return .= '<h4 class="mz_grid_date">';
				$return .= sprintf(__('Week of %1$s', 'mz-mindbody-api'), $week_starting);
				$return .= '</h4>';
				
				// Begin building HTML Table object
				$tbl->addTSection('thead');
				$tbl->addRow();
				// arguments: cell content, class, type (default is 'data' for td, pass 'header' for th)
				// can include associative array of optional additional attributes
				$tbl->addCell('', '', 'header');
				$tbl->addCell(__('Monday', 'mz-mindbody-api'), '', 'header');
				$tbl->addCell(__('Tuesday', 'mz-mindbody-api'), '', 'header');
				$tbl->addCell(__('Wednesday', 'mz-mindbody-api'), '', 'header');
				$tbl->addCell(__('Thursday', 'mz-mindbody-api'), '', 'header');
				$tbl->addCell(__('Friday', 'mz-mindbody-api'), '', 'header');
				$tbl->addCell(__('Saturday', 'mz-mindbody-api'), '', 'header');
				$tbl->addCell(__('Sunday', 'mz-mindbody-api'), '', 'header');

				$tbl->addTSection('tbody');
				
				// Create matrix of existing class times with empty schedule slots, sequenced by day 
				// Each "class" is an instance of Single_event
				$mz_days = sortClassesByTimeThenDay($mz_days, $this->mz_mbo_globals->time_format, $locations, 
																						$hide_cancelled, $hide, $advanced, $show_registrants,
																						$registrants_count, 'grid');
																										
				foreach($mz_days as $classTime => $mz_classes) {
					if ($classTime < 12) {
							$time_of_day = __('morning', 'mz-mindbody-api');
						}else if ($classTime > 16) {
							$time_of_day = __('evening', 'mz-mindbody-api');
						}else{
							$time_of_day = __('afternoon', 'mz-mindbody-api');
						}				

					$tbl->addRow();
					$tbl->addCell($time_of_day, 'hidden mz_time_of_day', 'data');
					$tbl->addCell($mz_classes['display_time']);
					
					foreach($mz_classes['classes'] as $key => $classes)
					{
						// Set some variables to determine if we need to display an <hr/> after event
						if (empty($classes)) {
							$num_classes_min_one = 50; //Set to a number that won't match key
							}else{
							$num_classes_min_one = count($classes) - 1;
							}

						foreach($classes as $key => $class)	{
							// populate dictionary of locations with names 
							// TODO Move out of this "presentation" loop
							if ($this->locations_count > $this->locations_dict_length):
								if (!array_key_exists($class->sLoc, $this->locations_dictionary)):
									$this->locations_dictionary[$class->sLoc] = $class->locationName;
								endif;
							endif;

							$class_separator = ($key == $num_classes_min_one) ? '' : '<hr/>';
							
							$signupButton = $class->signupButton;
							
							$class_details = $class->class_details . $class_separator;
							$this->time_slot .= $class_details;
						} // foreach mz_classes
						$tbl->addCell($this->time_slot);
						$class_details = ''; // Reinitialize class details
						$this->time_slot = ''; // Reinitialize time slot
					}
				} // EOF foreach($mz_days)
				$return .= $tbl->display();
			} // EOF If Grid
			
		// Add "footer" Items
		if ($type=='week'):
				// schedule navigation
				$return .= mz_mbo_schedule_nav($mz_date, __('Week', 'mz-mindbody-api'));
		endif;
			
			$return .= '<div id="mzModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mzSmallModalLabel" aria-hidden="true"></div>';

			$return .= '</div>';
		}
		else
		{

			if(!empty($mz_schedule_data['GetClassesResult']['Message']))
			{
				$return = $mz_schedule_data['GetClassesResult']['Message'];
			}
			else
			{
				if ($type=='week'):
					$return = mz_mbo_schedule_nav($mz_date, __('Week', 'mz-mindbody-api'));
					$return .= '<br/>';
				endif;
				$return .= __('Error getting classes. Try re-loading the page.',' mz-mindbody-api') . '<br />';
				$return .= '<pre>'.print_r($mz_schedule_data,1).'</pre>';
			}
		}	// EOF if ['GetClassesResult']['Classes']['Class'] is populated
		
		if ($filter == 1):
			add_action('wp_footer', array($this, 'add_filter_table'));
			add_action('wp_footer', array($this, 'initialize_filter'));
		endif;
		
		if ($show_registrants == 1 ): 

			$return .= '<div class="modal fade" id="registrantModal" tabindex="-1" role="dialog" aria-labelledby="mzSmallModalLabel" aria-hidden="true">';

			$return .= '</div>';
		
		endif;
		
		return $return;
	}//EOF mZ_show_schedule
		
	
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
