<?php
	
class MZ_Mindbody_Schedule_Display {

	private $mz_mbo_object;
	private $locations_dictionary = array();
	static $time_tracker;	
	private $locations_count; // Used to know how many times we need to check when populating dict
	private $locations_dict_length = 0;
	private $time_slot;
	private $locations;
	private $mz_date_grid;
	private $initial_button_text;
	private $swap_button_text;
	private $grid_class = '';
	private $horizontal_class = '';
	private $mode_select = 0;
	private $row_css_classes;
	private $account;
	public $delink = 0;
	
	public function __construct(){
		require_once(MZ_MINDBODY_SCHEDULE_DIR .'inc/mz_mbo_init.inc');
		$this->mz_mbo_object = new MZ_Mindbody_Init();
		$this::$time_tracker = date('Fd', strtotime("today"));
	}
	
	/**
	 * Get sorted list of classes and return array of staff id's for class_title "owner"
	 * ie. not the substitute 
	 * NOT BEING USED
	 */
	private function get_non_substitute_teachers($classes) {
		$return = array();
		foreach($classes as $class):
			if($class['Substitute'] != 1):
				$return[$class['ID']] = array($class['Staff']['ID'],$class['Staff']['Name'],$class['ClassDescription']['Name']);
			else:
				//echo "Subbed out is:";
				//mz_pr(array($class['ClassScheduleID'],$class['ID'],$class['Staff']['ID'],$class['Staff']['Name'],$class['ClassDescription']['Name']));
			endif;
		endforeach;
		return $return;
	}
	
 	public function mbo_localize_main_js() {

		$main_js_params = array(
			'staff_preposition' => __('with', 'mz-mindbody-api'),
			'initial' => $this->initial_button_text,
			'mode_select' => $this->mode_select,
			'swap' => $this->swap_button_text
			);
			
		wp_localize_script( 'mz_mbo_bootstrap_script', 'mz_mbo_bootstrap_script', $main_js_params);
 	}
	
	public function mZ_mindbody_show_schedule( $atts, $account=0 )
	{
		wp_enqueue_style('mZ_mindbody_schedule_bs', asset_path('styles/main.css'), false, null);
		wp_enqueue_script('mz_mbo_bootstrap_script', asset_path('scripts/main.js'), array('jquery'), null, true);
		
		//echo '<br />time: '. current_time('l, F jS, Y \a\t g:i A'); 
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
			'registrants_count' => '0',
			'mode_select' => '0',
			'unlink' => 0
				), $atts );
		$type = $atts['type'];
		$location = $atts['location'];
		$locations = $atts['locations'];
		$this->account = $atts['account'];
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
		$mode_select = $atts['mode_select'];
		$this->delink = $atts['unlink'];
		$this->mode_select = $mode_select;

		//Build cache based on shortcode attributes.
		$mz_schedule_cache = 'mz_sched_che';
		$mz_schedule_timer = 'mz_sched_tim';
				
		foreach ($atts as $key=>$value){
			// Ensure cache object is unique to this shortcode instance based on attributes
			if($value=='0' || $value=='') continue;
			$mz_schedule_cache .= '_'.substr($key,0,4).'_'.$value;
		}

		if (($grid == 1) && ($type == 'day')) {
			return '<div style="color:red"><h2>'.__('Grid Calendar Incompatible with Single Day Mode!', 'mz_mndbody_api').'</h2></div>';
		}
		
		/*
		 * This is for backwards compatibility for previous to using an array to hold one or more locations.
		*/
		if (($locations == '') || !isset($locations)) {
			if ($location == '') {
				$this->locations = array('1');
			}else{
				$this->locations = array($location);
			}
		} else {
			$this->locations = array_map('trim', explode(',', $atts['locations']));
		}
		$this->locations_count = count($this->locations);
		
		$mz_date = empty($_GET['mz_date']) ? date_i18n('Y-m-d',current_time('timestamp')) : mz_validate_date($_GET['mz_date']);
		
		//Add date to cache
		if (!empty($_GET['mz_date'])) {
				$mz_schedule_cache .= '_' . str_replace('-','_',$mz_date);
			} else {
				$mz_schedule_cache .= '_' . str_replace('-','_',$mz_date);
			}

		$hide = explode(', ', $atts['hide']);
		$which_monday = (strtotime('this monday') > current_time('timestamp')) ? 'last monday' : 'this monday';
		$this->mz_date_grid = empty($_GET['mz_date']) || ( $_GET['mz_date'] == date_i18n('Y-m-d',current_time('timestamp')) ) ? date_i18n('Y-m-d',strtotime($which_monday)) : mz_validate_date($_GET['mz_date']);

		if ($type=='day')
			{
				$mz_timeframe = array_slice(mz_getDateRange($mz_date, 1), 0, 1);
			}
		else if (($mode_select != 0) || ($grid == 1))
			{   
				$mz_timeframe = array_slice(mz_getDateRange($this->mz_date_grid, 7), 0, 1);
			} 
		else 
			{
				$mz_timeframe = array_slice(mz_getDateRange($mz_date, 7), 0, 1);
			}
		//While we still need to support php 5.2 and can't use [0] on above
		$mz_timeframe = array_shift($mz_timeframe);

	  // START caching
		$mz_cache_reset = isset($this->mz_mbo_object->options['mz_mindbody_clear_cache']) ? "on" : "off";

		$last_look = get_transient($mz_schedule_timer);

		// Insure that MBO info is up to date for each new day.
		if ( $mz_cache_reset == "on" || $last_look != $this::$time_tracker){
			// delete_transient( $mz_schedule_cache );
			// Replaced above with the following so we could deal with multiple date ranges being called.
			global $wpdb;
			$wpdb->query( "DELETE FROM `$wpdb->options` WHERE (`option_name` LIKE '%mz_sched_che%' OR `option_name` LIKE '%mz_sched_tim%')" );
		}
		//Uncomment to look at transients
		//global $wpdb;
		//$all_of_us = $wpdb->get_results( "SELECT * FROM `$wpdb->options` WHERE (`option_name` LIKE '%mz_sched_che%' OR `option_name` LIKE '%mz_sched_tim%')" );
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
			if ($this->account == 0) {
				$mz_schedule_data = $mb->GetClasses($mz_timeframe);
				}else{
				$mb->sourceCredentials['SiteIDs'][0] = $this->account; 
				$mz_schedule_data = $mb->GetClasses($mz_timeframe);
				}

			//Cache the mindbody call for 24 hours
			//But only if we are NOT loading for different week than current
			// TODO make cache timeout configurable.
			
			set_transient($mz_schedule_timer, $this::$time_tracker, 60 * 60 * 12);
			set_transient($mz_schedule_cache, $mz_schedule_data, 60 * 60 * 12);

		   // END caching*/
		}
		
		$mz_schedule_data = get_transient( $mz_schedule_cache );

		// Start Ajax Get Staff
		 function mZ_get_staff() {
			wp_register_script('mZ_get_staff', plugins_url('/mz-mindbody-api/dist/scripts/ajax-mbo-get-staff.js'), array('jquery'), null, true);
			wp_enqueue_script('mZ_get_staff');
			}
	
		 //Enqueue script in footer
		 add_action('wp_footer', 'mZ_get_staff');
		 add_action('wp_footer', 'ajax_mbo_get_staff_js');
 
			function ajax_mbo_get_staff_js() {

			//Force page protocol to match current
			$protocol = isset( $_SERVER["HTTPS"]) ? 'https://' : 'http://';
 
			$params = array(
				'ajaxurl' => admin_url( 'admin-ajax.php', $protocol ),
				'no_bio' => __('No biography listed for this staff member.', 'mz-mindbody-api'),
				'sub_by_text' => __('Sub for', 'mz-mindbody-api')
				);
	
			wp_localize_script( 'mZ_get_staff', 'mZ_get_staff', $params);

			}
		// End Ajax Get Staff
		$return = '';
		
		if(!empty($mz_schedule_data['GetClassesResult']['Classes']['Class']))
		{
			$mz_days = $this->makeNumericArray($mz_schedule_data['GetClassesResult']['Classes']['Class']);

		// populate dictionary of locations with names 
			foreach ($mz_days as $class) {
				if (!in_array($class['Location']['ID'], $this->locations)) { continue; }
				if (!array_key_exists($class['Location']['ID'], $this->locations_dictionary)):
					$this->locations_dictionary[$class['Location']['ID']] = $class['Location']['Name'];
					$this->locations_dict_length += 1;
				endif;
				if ($this->locations_count == $this->locations_dict_length)
					break;
			}

		/* In case more locations specified than exist, print error. 
		But this would be a problem if was were not any classes in location
		during particular period.
		if ($this->locations_count > $this->locations_dict_length):
			mz_pr("You seem to have specified a Location ID that does not exist in MBO. These exist:");
			mz_pr($this->locations_dictionary);
		endif;
		*/
		if (($advanced == 1) || ($show_registrants == 1) ) {
			include_once(MZ_MINDBODY_SCHEDULE_DIR . 'lib/ajax.php');
			if ($advanced == 1){
				 add_action('wp_footer', 'mZ_check_session_logged');
				 add_action('wp_footer', 'mz_mbo_check_session_logged');
			 }
		}
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
						
			// Remove all items not in current locations collection
					$i = 0;
					foreach ($mz_days as $day_of_classes) {
						if (!in_array($day_of_classes['Location']['ID'], $this->locations)){
							unset($mz_days[$i]);
							}
						$i++;
						}
					$mz_days = array_values($mz_days);
			//endif;
			
			

			$return .= '<div id="mz_mbo_schedule" class="mz_mbo_schedule">';
			if ($type==__('week','mz-mindbody-api')){
				$return .= mz_mbo_schedule_nav($mz_date, __('Week', 'mz-mindbody-api'));
			}

		$table_class = ($filter == 1) ? 'mz-schedule-filter' : 'mz-schedule-table';
		if ($mode_select == 1):
			$this->grid_class = 'mz_hidden '.$table_class;
			$this->horizontal_class = $table_class;
			$this->initial_button_text = __('Grid View', 'mz-mindbody-api');
			$this->swap_button_text = __('Horizontal View', 'mz-mindbody-api');
		elseif ($mode_select == 2):
			$this->horizontal_class = 'mz_hidden '.$table_class;
			$this->grid_class = $table_class;
			$this->initial_button_text = __('Horizontal View', 'mz-mindbody-api');
			$this->swap_button_text = __('Grid View', 'mz-mindbody-api');
		else:
			$this->horizontal_class = $table_class;
			$this->grid_class = $table_class;
		endif;
		
		$tbl_horizontal = new HTML_Table('', $this->horizontal_class . ' ' . ' mz-schedule-horizontal mz-schedule-display');
		$tbl_grid = new HTML_Table('', $this->grid_class . ' ' . ' mz-schedule-grid mz-schedule-display');
		
		//$schedule_class_teachers = ($no_sub_link == 0 ) ? $this->get_non_substitute_teachers($mz_days) : array();

		if ($mode_select != 0) {
			// If Mode Select is enabled we will return both displays
			// Retrieve data for horizontal display

			$mz_days_horizontal = sortClassesByDate($mz_days, 
																							MZ_MBO_shared::$time_format, 
																							$this->locations, 
																							$hide_cancelled, 
																							$hide, 
																							$advanced, 
																							$show_registrants,
																							$registrants_count, 
																							'horizontal', 
																							$this->delink, 
																							'Enrollment', 
																							$this->account);
			// Display Horizontal schedule								
			$return .= $this->horizontal_schedule($mz_days_horizontal, $tbl_horizontal);
			
			// Retrieve data for grid display
			$mz_days_grid = sortClassesByTimeThenDay($mz_days, 
																								MZ_MBO_shared::$time_format, 
																								$this->locations, 
																								$hide_cancelled, 
																								$hide, 
																								$advanced, 
																								$show_registrants,
																								$registrants_count, 
																								'grid', 
																								$this->delink, 
																								'Enrollment', 
																								$this->account);
			// Display Grid schedule																					
			$return .= $this->grid_schedule($mz_days_grid, $tbl_grid, $return);
			
		} else if ($grid == 1) {	
			
			// Retrieve data for grid display
			$mz_days_grid = sortClassesByTimeThenDay($mz_days, 
																								MZ_MBO_shared::$time_format, 
																								$this->locations, 
																							  $hide_cancelled, 
																							  $hide, $advanced, 
																							  $show_registrants,
																							  $registrants_count, 
																							  'grid', 
																							  $this->delink, 
																							  'Enrollment', 
																							  $this->account);
			// Display Grid schedule																					
			$return .= $this->grid_schedule($mz_days_grid, $tbl_grid, $return);
		} else {
			// If grid is not one and mode_select not enabled, just display horizontal schedule
			// Retrieve data for horizontal display
			
			$mz_days_horizontal = sortClassesByDate($mz_days, 
																							MZ_MBO_shared::$time_format, 
																							$this->locations, 
																							$hide_cancelled, 
																							$hide, $advanced, 
																							$show_registrants,
																							$registrants_count, 
																							'horizontal', 
																							$this->delink, 
																							'Enrollment', 
																							$this->account);
			// Display Horizontal schedule								
			$return .= $this->horizontal_schedule($mz_days_horizontal, $tbl_horizontal);
			
			// We don't want to display the grid Week Of container
			$this->grid_class = 'mz_hidden';
		}

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
			add_action('wp_footer', array($this, 'add_filter_table'), 10);
			add_action('wp_footer', array($this, 'initialize_filter'));
		endif;
		
		if ($show_registrants == 1 ): 

			$return .= '<div class="modal fade" id="registrantModal" tabindex="-1" role="dialog" aria-labelledby="mzSmallModalLabel" aria-hidden="true">';

			$return .= '</div>';
		
		endif;
		
		$return .= '<div class="modal fade" id="mzStaffScheduleModal" tabindex="-1" role="dialog" aria-labelledby="mzSmallModalLabel" aria-hidden="true">';

		$return .= '</div>';
		
		return $return;
	}//EOF mZ_show_schedule
		
	private function horizontal_schedule($mz_days_horizontal, $tbl_horizontal) {

		foreach($mz_days_horizontal as $classDate => $mz_classes) {   
		
			$tbl_horizontal->addRow('header');
			// arguments: cell content, class, type (default is 'data' for td, pass 'header' for th)
			// can include associative array of optional additional attributes

			$tbl_horizontal->addCell(date_i18n(MZ_MBO_shared::$date_format, strtotime($classDate)), 'mz_date_display', 'header', array('scope'=>'header'));
			$tbl_horizontal->addCell(__('Class Name', 'mz-mindbody-api'), 'mz_classDetails', 'header', array('scope'=>'header'));
			$tbl_horizontal->addCell(__('Instructor', 'mz-mindbody-api'), 'mz_staffName', 'header', array('scope'=>'header'));
			$tbl_horizontal->addCell(__('Class Type', 'mz-mindbody-api'), 'mz_sessionTypeName', 'header', array('scope'=>'header'));
				
			$tbl_horizontal->addTSection('tbody');
			
			foreach($mz_classes['classes'] as $class)
				{
					// TODO Remove this which is URU specific
					if ($class->className == 'Admin') {continue;}
					
					// start building table rows
					$this->row_css_classes = 'mz_description_holder mz_schedule_table mz_location_'.$class->sLoc;
					$tbl_horizontal->addRow($this->row_css_classes);
					$tbl_horizontal->addCell($class->time_of_day, 'hidden', 'data');
					$tbl_horizontal->addCell(date_i18n(MZ_MBO_shared::$time_format, strtotime($class->startDateTime)) . ' - ' . 
									date_i18n(MZ_MBO_shared::$time_format, strtotime($class->endDateTime)) .
									'<br/>' . $class->signupButton[0] . ' ' . $class->toward_capacity , 'mz_date_display' );
		
					$tbl_horizontal->addCell($class->class_details, "class_name_cell");

					$tbl_horizontal->addCell($class->staffModal . ' ' . $class->sub_link, 'mz_staffName');
					if (count($this->locations) > 1):
						$tbl_horizontal->addCell($class->sessionTypeName . '<br/>' .__('at', 'mz_mbo_api') . ' ' 
						. $class->locationNameDisplay, 'mz_locationName');
					else:
						$tbl_horizontal->addCell($class->sessionTypeName, 'mz_sessionTypeName');
					endif;
					
				}
				
			}

		//$tbl_horizontal->addTSection('tfoot');
		//$tbl_horizontal->addRow();
		//$tbl_horizontal->addCell('','','', array('colspan' => '4'));
		return $tbl_horizontal->display();
	}
	
	private function grid_schedule ($mz_days_grid, $tbl_grid, &$return) {
		$week_starting = date_i18n(MZ_MBO_shared::$date_format, strtotime($this->mz_date_grid)); 
				
		$return .= '<h4 class="mz_grid_date ' . $this->grid_class . '">';
		$return .= sprintf(__('Week of %1$s', 'mz-mindbody-api'), $week_starting);
		$return .= '</h4>';
	
		// Begin building HTML Table object
		$tbl_grid->addTSection('thead');
		$tbl_grid->addRow();
		// arguments: cell content, class, type (default is 'data' for td, pass 'header' for th)
		// can include associative array of optional additional attributes
		$tbl_grid->addCell('', '', 'header');
		$tbl_grid->addCell(__('Monday', 'mz-mindbody-api'), '', 'header');
		$tbl_grid->addCell(__('Tuesday', 'mz-mindbody-api'), '', 'header');
		$tbl_grid->addCell(__('Wednesday', 'mz-mindbody-api'), '', 'header');
		$tbl_grid->addCell(__('Thursday', 'mz-mindbody-api'), '', 'header');
		$tbl_grid->addCell(__('Friday', 'mz-mindbody-api'), '', 'header');
		$tbl_grid->addCell(__('Saturday', 'mz-mindbody-api'), '', 'header');
		$tbl_grid->addCell(__('Sunday', 'mz-mindbody-api'), '', 'header');

		$tbl_grid->addTSection('tbody');
		
		foreach($mz_days_grid as $classTime => $mz_classes) {
			if ($classTime < 12) {
					$time_of_day = __('morning', 'mz-mindbody-api');
				}else if ($classTime > 16) {
					$time_of_day = __('evening', 'mz-mindbody-api');
				}else{
					$time_of_day = __('afternoon', 'mz-mindbody-api');
				}				

			$tbl_grid->addRow();
			$tbl_grid->addCell($time_of_day, 'hidden mz_time_of_day', 'data');
			$tbl_grid->addCell($mz_classes['display_time']);
			
			foreach($mz_classes['classes'] as $key => $classes)
			{
				// Set some variables to determine if we need to display an <hr/> after event
				if (empty($classes)) {
					$num_classes_min_one = 50; //Set to a number that won't match key
					}else{
					$num_classes_min_one = count($classes) - 1;
					}

				foreach($classes as $key => $class)	{
					//TODO remove this which is URU specific
					if ($class->className == 'Admin') {continue;}

					$class_separator = ($key == $num_classes_min_one) ? '' : '<hr/>';
					//What is this doing?
					$signupButton = '';//$class->signupButton;
					
					$class_details = $class->class_details . $class_separator;
					$this->time_slot .= $class_details;
				} // foreach mz_classes
				$tbl_grid->addCell($this->time_slot);
				$class_details = ''; // Reinitialize class details
				$this->time_slot = ''; // Reinitialize time slot
			}
		} // EOF foreach($mz_days)
		return $tbl_grid->display();
	}
			
	private function makeNumericArray($data) {
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
			jQuery(document).ready(function($){
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
