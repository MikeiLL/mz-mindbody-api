<?php
	
class MZ_Mindbody_Schedule_Display {

	private $mz_mbo_globals;
	private $locations_dictionary = array();
	
	public function __construct(){
		require_once(MZ_MINDBODY_SCHEDULE_DIR .'inc/mz_mbo_init.inc');
		$this->mz_mbo_globals = new MZ_Mindbody_Init();
	}
	
	
 	public function mbo_localize_main_js() {

		$main_js_params = array(
			'staff_preposition' => __('with', 'mz-mindbody-api')
			);
	
		wp_localize_script( 'mz_mbo_bootstrap_script', 'mz_mbo_bootstrap_script', $main_js_params);


 	}
	
	public function mZ_mindbody_show_schedule( $atts, $account=0 )
	{
		require_once(MZ_MINDBODY_SCHEDULE_DIR .'/lib/html_table.class.php');
		
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
			'hide_cancelled' => '1'
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
		
		$sign_up_text = __('Sign-Up', 'mz-mindbody-api');
		$manage_text = __('Manage on MindBody Site', 'mz-mindbody-api');
		
		//Build caache based on shortcode attributes.
		$mz_schedule_cache = 'mz_sched_che';
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
			$mz_date = empty($_GET['mz_date']) || ( $_GET['mz_date'] == date_i18n('Y-m-d',current_time('timestamp')) ) ? date_i18n('Y-m-d',strtotime('last monday')) : mz_validate_date($_GET['mz_date']);
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

		if ( $mz_cache_reset == "on" ){
			//delete_transient( $mz_schedule_cache );
			global $wpdb;
			$wpdb->query( "DELETE FROM `$wpdb->options` WHERE `option_name` LIKE ('%mz_sched_che%')" );
		}
		//Uncomment to look at transients
		//global $wpdb;
		//$all_of_us = $wpdb->get_results( "SELECT * FROM `$wpdb->options` WHERE `option_name` LIKE ('%mz_sched_che%')" );
		
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

			set_transient($mz_schedule_cache, $mz_schedule_data, 60 * 60 * 24);

		   // END caching*/
		}
		
		$mz_schedule_data = get_transient( $mz_schedule_cache );

		$return = '';

		if(!empty($mz_schedule_data['GetClassesResult']['Classes']['Class']))
		{
			$mz_days = $this->makeNumericArray($mz_schedule_data['GetClassesResult']['Classes']['Class']);
		
			if ($grid == 0){
				$mz_days = sortClassesByDate($mz_days, $this->mz_mbo_globals->time_format, $locations);
				}else{
				$mz_days = sortClassesByTimeThenDay($mz_days, $this->mz_mbo_globals->time_format, $locations);
				}

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
		if ($grid == 0) {
			foreach($mz_days as $classDate => $mz_classes)
			{   
			
				$tbl->addRow('header');
				// arguments: cell content, class, type (default is 'data' for td, pass 'header' for th)
				// can include associative array of optional additional attributes
		
				$tbl->addCell(date_i18n($this->mz_mbo_globals->mz_date_display, strtotime($classDate)), 'mz_date_display', 'header', array('scope'=>'header'));
				$tbl->addCell(__('Class Name', 'mz-mindbody-api'), 'mz_classDetails', 'header', array('scope'=>'header'));
				$tbl->addCell(__('Instructor', 'mz-mindbody-api'), 'mz_staffName', 'header', array('scope'=>'header'));
				$tbl->addCell(__('Class Type', 'mz-mindbody-api'), 'mz_sessionTypeName', 'header', array('scope'=>'header'));
			
				$tbl->addTSection('tbody');
				foreach($mz_classes as $class)
				{
						$sessionTypeName = $class['ClassDescription']['SessionType']['Name'];
						if ($class_types != '') {
							$class_types = explode(', ', $atts['class_types']);
							if (!in_array($sessionTypeName, $class_types)){
								continue;
								}
							}
						//mz_pr($class );
						$sDate = date_i18n('m/d/Y', strtotime($class['StartDateTime']));
						$sLoc = $class['Location']['ID'];
						$sTG = $class['ClassDescription']['Program']['ID'];
						$studioid = $class['Location']['SiteID'];
						$sclassid = $class['ClassScheduleID'];
						$sclassidID = $class['ID'];
						//mz_pr($sclassidID);
						$classDescription = $class['ClassDescription']['Description'];
						
						//Let's find an image if there is one and assign it to $classImage
						
						if (!isset($class['ClassDescription']['ImageURL'])) {
							$classImage = '';
							if (isset($class['ClassDescription']['AdditionalImageURLs']) && !empty($classImageArray)) {
								$classImage = pop($classImageArray);
								}
						} else {
							$classImage = $class['ClassDescription']['ImageURL'];
						}

						$sType = -7;
						$displayCancelled = ($class['IsCanceled'] == 1) ? '<div class="mz_cancelled_class">' .
										__('Cancelled', 'mz-mindbody-api') . '</div>' : '';
						$className = $class['ClassDescription']['Name'];
						//mz_pr($className);
						$startDateTime = date_i18n('Y-m-d H:i:s', strtotime($class['StartDateTime']));
						//mz_pr($startDateTime);
						//echo "<hr/>";
						$endDateTime = date_i18n('Y-m-d H:i:s', strtotime($class['EndDateTime']));
						$staffName = $class['Staff']['Name'];
						$isAvailable = $class['IsAvailable'];
						$locationName = $class['Location']['Name'];
						$staffImage = isset($class['Staff']['ImageURL']) ? $class['Staff']['ImageURL'] : '';
						mz_pr($staffImage);

						$linkURL = "https://clients.mindbodyonline.com/ws.asp?sDate={$sDate}&amp;sLoc={$sLoc}&amp;sTG={$sTG}&amp;sType={$sType}&amp;sclassid={$sclassid}&amp;studioid={$studioid}";

						if (date_i18n('H', strtotime($startDateTime)) < 12) {
								$time_of_day = __('morning', 'mz-mindbody-api');
							}else if ((date_i18n('H', strtotime($startDateTime)) > 16)) {
								$time_of_day = __('evening', 'mz-mindbody-api');
							}else{
								$time_of_day = __('afternoon', 'mz-mindbody-api');
								}
						// start building table rows
						$row_css_classes = 'mz_description_holder mz_schedule_table mz_location_'.$sLoc;
						$tbl->addRow($row_css_classes);
						$tbl->addCell($time_of_day, 'hidden', 'data');

						if (isset($isAvailable) && ($isAvailable != 0)) {
							if ($advanced == 1){
								$add_to_class_nonce = wp_create_nonce( 'mz_MBO_add_to_class_nonce');
								if ($clientID == ''){
										 $signupButton = '<a class="btn mz_add_to_class" href="'.home_url().'/login"' .
										 'title="' . __('Login to Sign-up', 'mz-mindbody-api') . '">' . 
										 __('Login to Sign-up', 'mz-mindbody-api') . '</a><br/>';
									  }else{
										  $signupButton = '<br/>' 
											. '	<a id="mz_add_to_class" class="btn mz_add_to_class"' 
											. ' data-nonce="' . $add_to_class_nonce 
											. '" data-classID="' . $sclassidID  
											. '" data-clientID="' . $clientID 
											. '">' .
											'<span class="count" style="display:none">0</span>' . 
											'<span class="signup">'. $sign_up_text .
											'</span></a>' ;
											}
									}else{
										$signupButton = '<a class="btn" href="' . $linkURL . '" target="_blank">' . $sign_up_text . '</a>';
									}
									
								$tbl->addCell(date_i18n($this->mz_mbo_globals->time_format, strtotime($startDateTime)) . ' - ' . 
								date_i18n($this->mz_mbo_globals->time_format, strtotime($endDateTime)) .
								'<br/>' . $signupButton );
							}else{ 
								$tbl->addCell(date_i18n($this->mz_mbo_globals->time_format, strtotime($startDateTime)) . ' - ' . 
									date_i18n($this->mz_mbo_globals->time_format, strtotime($endDateTime)), 'mz_date_display');
									}
									
							$class_name_link = $this->classLinkMaker($staffName, $className, $classDescription, $sclassidID, $staffImage, $show_registrants);
							
							$class_name_details = $class_name_link->build() . '<br/><div id="visitMBO" class="btn visitMBO" style="display:none">' .
							'<a class="btn" href="'.$linkURL.'" target="_blank">' .
							$manage_text . '</a></div>' .
							$displayCancelled;
							
							$tbl->addCell($class_name_details, "class_name_cell");

						$tbl->addCell($staffName, 'mz_staffName');
						$tbl->addCell($sessionTypeName, 'mz_sessionTypeName');
						// populate dictionary of locations with names 
						if (!array_key_exists($sLoc, $this->locations_dictionary))
							$this->locations_dictionary[$sLoc] = $locationName;
				}// EOF foreach class
			}// EOF foreach day

			$tbl->addTSection('tfoot');
			$tbl->addRow();
			$tbl->addCell('','','', array('colspan' => 4));
				
			$return .= $tbl->display();

		}else{
			//Display grid
			$week_starting = date_i18n($this->mz_mbo_globals->date_format, strtotime($mz_date)); //
			$return .= '<h4 class="mz_grid_date">';
			$return .= sprintf(__('Week of %1$s', 'mz-mindbody-api'), $week_starting);
			$return .= '</h4>';
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
			foreach($mz_days as $classDate => $mz_classes)
				{   
					if ($classDate < 12) {
								$time_of_day = __('morning', 'mz-mindbody-api');
							}else if ($classDate > 16) {
								$time_of_day = __('evening', 'mz-mindbody-api');
							}else{
								$time_of_day = __('afternoon', 'mz-mindbody-api');
								}					
					$tbl->addRow();
					$tbl->addCell($time_of_day, 'hidden mz_time_of_day', 'data');
					$tbl->addCell($mz_classes['display_time']);

					foreach($mz_classes['classes'] as $key => $classes)
					{
						//mz_pr($key);
						//mz_pr($classes);
						//die();
						// Set some variables to determine if we need to display an <hr/> after event
						if ((empty($classes)) || (null === $classes[0]['ClassDescription']['Name'])){
							$class_details = '';
							$num_classes_min_one = 50; //Set to a number that won't match key
							}else{
							$class_details = '';
							$num_classes_min_one = count($classes) - 1;

							foreach($classes as $key => $class){
									// populate dictionary of locations with names 
									//mz_pr($class['ClassDescription']);
									$sLoc = $class['Location']['ID'];
									$locationName = $class['Location']['Name'];
									if (!array_key_exists($sLoc, $this->locations_dictionary))
										$this->locations_dictionary[$sLoc] = $locationName;
									$sessionTypeName = $class['ClassDescription']['SessionType']['Name'];
									if ($class_types != '') {
										$class_types = explode(', ', $atts['class_types']);
										if (!in_array($sessionTypeName, $class_types)){
											continue;
											}
										}
									$className = $class['ClassDescription']['Name'];
									if(!in_array('teacher', $hide)){
										$teacher = __('with', 'mz-mindbody-api') . '&nbsp;' . $class['Staff']['Name'] .
										'<br/>';
										}else{ $teacher = '';}
									$displayCancelled = ($class['IsCanceled'] == 1) ? '<div class="mz_cancelled_class">' .
										__('Cancelled', 'mz-mindbody-api') . '</div>' : '';
									$classDescription = $class['ClassDescription']['Description'];
									if(!in_array('duration', $hide) && ($class['IsCanceled'] != 1)){
										$classStartTime = new DateTime($class['StartDateTime']);
										$classEndTime = new DateTime($class['EndDateTime']);
										if (phpversion() >= 5.3) {
											$classLength = $classEndTime->diff($classStartTime);
											$classLength = __('Duration:', 'mz-mindbody-api') . 
											'<br/>&nbsp;' . $classLength->format('%H:%I');
											}else{
											$classLength = round(($classEndTime->format('U') - $classStartTime->format('U')));
											$classLength = __('Duration:', 'mz-mindbody-api') . 
											'<br/>&nbsp;' . gmdate("H:i", $classLength);
											}
										
										}else{ $classLength = ''; }
									// Initialize $signupButton
									$signupButton = '';
									// Variables for class URL
									$sDate = date_i18n('m/d/Y', strtotime($class['StartDateTime']));
									$sTG = $class['ClassDescription']['Program']['ID'];
									$studioid = $class['Location']['SiteID'];
									$sclassid = $class['ClassScheduleID'];
									$sclassidID = $class['ID'];
									$staffName = $class['Staff']['Name'];
									
									$staffImage = isset($class['Staff']['ImageURL']) ? $class['Staff']['ImageURL'] : '';
									$sType = -7;
									$isAvailable = $class['IsAvailable'];
									if (count($locations) > 1) {
										$location_name_css = sanitize_html_class($locationName, 'mz_location_class');
										$locationAddress = $class['Location']['Address'];
										$locationNameDisplay = '<div class="'.$location_name_css.'"><a href="#" title="'. $locationAddress. '">' . 
																$locationName . '</a>';
										}else{
										$locationAddress = '';
										$locationNameDisplay = '';
										}
									$class_separator = ($key == $num_classes_min_one) ? '' : '<hr/>';
									$linkURL = "https://clients.mindbodyonline.com/ws.asp?sDate={$sDate}&amp;sLoc={$sLoc}&amp;sTG={$sTG}&amp;sType={$sType}&amp;sclassid={$sclassid}&amp;studioid={$studioid}";
									if(!in_array('signup', $hide)){
										if ($advanced == 1){
											if (isset($isAvailable) && ($isAvailable != 0)) {
												$add_to_class_nonce = wp_create_nonce( 'mz_MBO_add_to_class_nonce');
												if ($clientID == ''){
													 $signupButton = '<a class="btn mz_add_to_class fa fa-sign-in" href="'.home_url().'/login"' .
													 'title="' . __('Login to Sign-up', 'mz-mindbody-api') . '"></a><br/>';
													  }else{
													  $signupButton = '<br/><a id="mz_add_to_class" class="fa fa-sign-in mz_add_to_class"' 
														. 'title="' . $sign_up_text . '"'
														. ' data-nonce="' . $add_to_class_nonce 
														. '" data-className="' . $className 
														. '" data-classID="' . $sclassidID  
														. '" data-clientID="' . $clientID 
														. '" data-staffName="' . $staffName 
														. '"></a>' .
														'&nbsp; <span class="signup"> ' .
														'</span></a>&nbsp;' . 
														'<a id="visitMBO" class="fa fa-wrench visitMBO" href="'.$linkURL.'" target="_blank" ' . 
														'style="display:none" title="' .
														$manage_text . '"></a><br/>';
														}
												}
											}else{
												$signupButton = '&nbsp;<a href="'.$linkURL.'" target="_blank" title="'.
																__('Sign-Up', 'mz-mindbody-api'). '"><i class="fa fa-sign-in"></i></a><br/>';
													}
									}else{
										$signupButton = '';
										}
									$session_type_css = sanitize_html_class($sessionTypeName, 'mz_session_type');
									$class_name_css = sanitize_html_class($className, 'mz_class_name');
									$class_details .= '<div class="mz_schedule_table mz_description_holder mz_location_'.$sLoc.' '.'mz_' . 
									$session_type_css .' mz_'. $class_name_css .'">';
									
									$class_name_link = $this->classLinkMaker($staffName, $className, $classDescription, $sclassidID, $staffImage, $show_registrants);
							
									$class_details .= $class_name_link->build() . 
									'<br/>' .	 
									$teacher . $signupButton .
									$classLength . $displayCancelled . $locationNameDisplay . '</div>' .
									$class_separator;
								}
							}
						$tbl->addCell($class_details);
					
					}//end foreach mz_classes
				}//end foreach mz_days
			$return .= $tbl->display();
		}//End if grid
			if ($type=='week')
				// schedule navigation
				$return .= mz_mbo_schedule_nav($mz_date, __('Week', 'mz-mindbody-api'));
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
				$return = __('Error getting classes. Try re-loading the page.',' mz-mindbody-api') . '<br />';
				$return .= '<pre>'.print_r($mz_schedule_data,1).'</pre>';
			}
		}//EOF If Result / Else
		
		if ($filter == 1):
			add_action('wp_footer', array($this, 'add_filter_table'));
			add_action('wp_footer', array($this, 'initialize_filter'));
		endif;
		
		if ($show_registrants == 1 ): 

			$return .= '<div class="modal fade" id="registrantModal" tabindex="-1" role="dialog" aria-labelledby="mzSmallModalLabel" aria-hidden="true">';

			$return .= '</div>';
		
		endif;
		
		$mz_schedule_display = 'mz_schedule_display_' . mt_rand(1, 1000000);

		set_transient($mz_schedule_display, $return, 60 * 60 * 24);

		return get_transient( $mz_schedule_display );
		

				
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

/* creates an html element, like in js */
class html_element
{
	/* vars */
	var $type;
	var $attributes;
	var $self_closers;
	
	/* constructor */
	function html_element($type,$self_closers = array('input','img','hr','br','meta','link'))
	{
		$this->type = strtolower($type);
		$this->self_closers = $self_closers;
	}
	
	/* get */
	function get($attribute)
	{
		return $this->attributes[$attribute];
	}
	
	/* set -- array or key,value */
	function set($attribute,$value = '')
	{
		if(!is_array($attribute))
		{
			$this->attributes[$attribute] = $value;
		}
		else
		{
			$this->attributes = array_merge($this->attributes,$attribute);
		}
	}
	
	/* remove an attribute */
	function remove($att)
	{
		if(isset($this->attributes[$att]))
		{
			unset($this->attributes[$att]);
		}
	}
	
	/* clear */
	function clear()
	{
		$this->attributes = array();
	}
	
	/* inject */
	function inject($object)
	{
		if(@get_class($object) == __class__)
		{
			$this->attributes['text'].= $object->build();
		}
	}
	
	/* build */
	function build()
	{
		//start
		$build = '<'.$this->type;
		
		//add attributes
		if(count($this->attributes))
		{
			foreach($this->attributes as $key=>$value)
			{
				if($key != 'text') { $build.= ' '.$key.'="'.$value.'"'; }
			}
		}
		
		//closing
		if(!in_array($this->type,$this->self_closers))
		{
			$build.= '>'.$this->attributes['text'].'</'.$this->type.'>';
		}
		else
		{
			$build.= ' />';
		}
		
		//return it
		return $build;
	}
	
	/* spit it out */
	function output()
	{
		echo $this->build();
	}
}

?>
