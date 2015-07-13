<?php

class MZ_Mindbody_Schedule_Display {

	protected $counter;
	
	public function __construct(){
		$this->counter = 0;
	}

	public function mZ_mindbody_show_schedule( $atts, $account=0 )
	{
		require_once(MZ_MINDBODY_SCHEDULE_DIR .'inc/mz_mbo_init.inc');
		require_once(MZ_MINDBODY_SCHEDULE_DIR .'/lib/html_table.class.php');

		global $add_mz_ajax_script;
		$add_mz_ajax_script = true;
		// optionally pass in a type parameter. Defaults to week.
		$atts = shortcode_atts( array(
			'type' => 'week',
			'location' => '1',
			'account' => '0',
			'filter' => '0',
			'grid' => '0',
			'hide' => ''
				), $atts );
		$type = $atts['type'];
		$location = $atts['location'];
		$account = $atts['account'];
		$filter = $atts['filter'];
		$grid = $atts['grid'];
	
		if (($grid == 1) && ($type == 'day')) {
			return '<div style="color:red"><h2>'.__('Grid Calendar Incompatible with Single Day Mode!', 'mz_mndbody_api').'</h2></div>';
		}
	
		if ($grid == 0) {
			$mz_date = empty($_GET['mz_date']) ? date_i18n('Y-m-d') : mz_validate_date($_GET['mz_date']);
			}else{
			$hide = explode(', ', $atts['hide']);
			$mz_date = empty($_GET['mz_date']) ? date_i18n('Y-m-d',strtotime('last monday')) : mz_validate_date($_GET['mz_date']);
			}

		if ($type==__('day', 'mz-mindbody-api'))
		{
			$mz_timeframe = array_slice(mz_getDateRange($mz_date, 1), 0, 1);
			$mz_schedule_cache = "mz_schedule_day_cache";
		}
		else
		{   
			$mz_timeframe = array_slice(mz_getDateRange($mz_date, 7), 0, 1);
			$mz_schedule_cache = "mz_schedule_week_cache";
		}

		//While we still need to support php 5.2 and can't use [0] on above
		$mz_timeframe = array_pop($mz_timeframe);
	
	  // START caching
		$mz_cache_reset = isset($options['mz_mindbody_clear_cache']) ? "on" : "off";

		if ( $mz_cache_reset == "on" ){
			delete_transient( $mz_schedule_cache );
		}
		if (isset($_GET) || ( false === ( $mz_schedule_data = get_transient( $mz_schedule_cache ) ) ) ) {
		//Send the timeframe to the GetClasses class, unless already cached
		if ($account == 0) {
			$mz_schedule_data = $mb->GetClasses($mz_timeframe);
			}else{
			$mb->sourceCredentials['SiteIDs'][0] = $account; 
			$mz_schedule_data = $mb->GetClasses($mz_timeframe);
			}
		}
		//mz_pr($mz_schedule_data);
		//Cache the mindbody call for 24 hours
		// TODO make cache timeout configurable.
		set_transient($mz_schedule_cache, $mz_schedule_data, 60 * 60 * 24);
		// END caching

		$return = '';

		if(!empty($mz_schedule_data['GetClassesResult']['Classes']['Class']))
		{
			$mz_days = $mb->makeNumericArray($mz_schedule_data['GetClassesResult']['Classes']['Class']);
		
			if ($grid == 0){
				$mz_days = sortClassesByDate($mz_days, $time_format, $location);
				}else{
				$mz_days = sortClassesByTimeThenDay($mz_days, $time_format, $location);
				}

				$return .= '<div id="mz_mbo_schedule" class="mz_mbo_schedule">';
			if ($type==__('week','mz-mindbody-api')){
				$return .= mz_mbo_schedule_nav($mz_date, __('Week', 'mz-mindbody-api'));
			}

		if ($filter == 1) {
				$tbl = new HTML_Table('', 'mz-schedule-filter');
			}else{
				$tbl = new HTML_Table('', 'mz-schedule-table');
			}
		if ($grid == 0) {
			foreach($mz_days as $classDate => $mz_classes)
			{   
				$tbl->addRow('header');
				// arguments: cell content, class, type (default is 'data' for td, pass 'header' for th)
				// can include associative array of optional additional attributes
		
				$tbl->addCell(date_i18n($mz_date_display, strtotime($classDate)), '', 'header', array('scope'=>'header'));
				$tbl->addCell(__('Class Name', 'mz-mindbody-api'), '', 'header', array('scope'=>'header'));
				$tbl->addCell(__('Instructor', 'mz-mindbody-api'), '', 'header', array('scope'=>'header'));
				$tbl->addCell(__('Class Type', 'mz-mindbody-api'), '', 'header', array('scope'=>'header'));
			
				$tbl->addTSection('tbody');
				foreach($mz_classes as $class)
				{
						// mz_pr($class);
						$sDate = date_i18n('m/d/Y', strtotime($class['StartDateTime']));
						$sLoc = $class['Location']['ID'];
						$sTG = $class['ClassDescription']['Program']['ID'];
						$studioid = $class['Location']['SiteID'];
						$sclassid = $class['ClassScheduleID'];
						$sclassidID = $class['ID'];
						$classDescription = $class['ClassDescription']['Description'];
						$sType = -7;
						$showCancelled = ($class['IsCanceled'] == 1) ? '<div class="mz_cancelled_class">' .
										__('Cancelled', 'mz-mindbody-api') . '</div>' : '';
						$className = $class['ClassDescription']['Name'];
						$startDateTime = date_i18n('Y-m-d H:i:s', strtotime($class['StartDateTime']));
						$endDateTime = date_i18n('Y-m-d H:i:s', strtotime($class['EndDateTime']));
						$staffName = $class['Staff']['Name'];
						$sessionType = $class['ClassDescription']['SessionType']['Name'];
						$isAvailable = $class['IsAvailable'];

						$linkURL = "https://clients.mindbodyonline.com/ws.asp?sDate={$sDate}&amp;sLoc={$sLoc}&amp;sTG={$sTG}&amp;sType={$sType}&amp;sclassid={$sclassid}&amp;studioid={$studioid}";

						if (date_i18n('H', strtotime($startDateTime)) < 12) {
								$time_of_day = __('morning', 'mz-mindbody-api');
							}else if ((date_i18n('H', strtotime($startDateTime)) > 16)) {
								$time_of_day = __('evening', 'mz-mindbody-api');
							}else{
								$time_of_day = __('afternoon', 'mz-mindbody-api');
								}
						// start building table rows
						$tbl->addRow('mz_description_holder');
						$tbl->addCell($time_of_day, 'hidden', 'data');

						if (isset($isAvailable) && ($isAvailable != 0)) {
								$tbl->addCell(date_i18n($time_format, strtotime($startDateTime)) . ' - ' . 
									date_i18n($time_format, strtotime($endDateTime)) .
									'<br/><a class="btn" href="' . $linkURL . '" target="_blank">' . __('Sign-Up', 'mz-mindbody-api') . '</a>');
							}else{ 
								$tbl->addCell(date_i18n($time_format, strtotime($startDateTime)) . ' - ' . 
									date_i18n($time_format, strtotime($endDateTime)));
									}

						$tbl->addCell(
							'<a data-toggle="modal" data-target="#mzModal" href="' . MZ_MINDBODY_SCHEDULE_URL . 
							'inc/modal_descriptions.php?classDescription=' . 
							urlencode(substr($classDescription, 0, 1000)) . 
							'&amp;className='. urlencode(substr($className, 0, 1000)) .'">' . $className . '</a>' .
						// trigger link modal
								'<br/><div id="visitMBO" class="btn visitMBO" style="display:none">' .
							'<a href="'.$linkURL.'" target="_blank">' .
							__('Manage on MindBody Site',' mz-mindbody-api') . '</a></div>' .
							$showCancelled );


						$tbl->addCell($staffName);
						$tbl->addCell($sessionType);
				}// EOF foreach class
			}// EOF foreach day

			$tbl->addTSection('tfoot');
			$tbl->addRow();
			$tbl->addCell('','','', array('colspan' => 4));
			$return .= $tbl->display();

		}else{
			//Display grid

			$week_starting = date_i18n($date_format, strtotime('last monday'));
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
					$tbl->addCell($time_of_day, 'hidden', 'data');
					$tbl->addCell($mz_classes['display_time']);
					//mz_pr($mz_classes['display_time']);
					foreach($mz_classes['classes'] as $key => $classes)
					{
						//mz_pr($key);
						//mz_pr($classes);
						//die();
						if ((empty($classes)) || (null === $classes[0]['ClassDescription']['Name'])){
							$class_details = '';
							$num_classes_min_one = 50; //Set to a number that won't match key
							}else{
							$class_details = '';
							$num_classes_min_one = count($classes) - 1;

							foreach($classes as $key => $class){	
									$className = $class['ClassDescription']['Name'];
									if(!in_array('teacher', $hide)){
										$teacher = __('with', 'mz-mindbody-api') . '&nbsp;' . $class['Staff']['Name'] .
										'<br/>';
										}else{ $teacher = '';}
									$showCancelled = ($class['IsCanceled'] == 1) ? '<div class="mz_cancelled_class">' .
										__('Cancelled') . '</div>' : '';
									$classDescription = $class['ClassDescription']['Description'];
									$sessionTypeName = $class['ClassDescription']['SessionType']['Name'];

									$showCancelled = ($class['IsCanceled'] == 1) ? '<div class="mz_cancelled_class">' .
										__('Cancelled') . '</div>' : '';
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
									$sLoc = $class['Location']['ID'];
									$sTG = $class['ClassDescription']['Program']['ID'];
									$studioid = $class['Location']['SiteID'];
									$sclassid = $class['ClassScheduleID'];
									$sclassidID = $class['ID'];
									$sType = -7;
									$isAvailable = $class['IsAvailable'];
									$class_separator = ($key == $num_classes_min_one) ? '' : '<hr/>';
									$linkURL = "https://clients.mindbodyonline.com/ws.asp?sDate={$sDate}&amp;sLoc={$sLoc}&amp;sTG={$sTG}&amp;sType={$sType}&amp;sclassid={$sclassid}&amp;studioid={$studioid}";
									if(!in_array('signup', $hide)){
									$signupButton = '&nbsp;<a href="'.$linkURL.'" target="_blank" title="'.
													__('Sign-Up', 'mz-mindbody-api'). '"><i class="fa fa-sign-in"></i></a><br/>';
										}else{$signupButton = '';}

									$class_details .= '<div class="' .'mz_' . 
									sanitize_html_class($sessionTypeName, 'mz_session_type') .'">' .
									'<a data-toggle="modal" data-target="#mzModal" href="' . MZ_MINDBODY_SCHEDULE_URL . 
									'inc/modal_descriptions.php?classDescription=' . 
									urlencode(substr($classDescription, 0, 1000)) . 
									'&amp;className='. urlencode(substr($className, 0, 1000)) .'">' . $className . '</a>' .
									'<br/>' .	 
									$teacher . $signupButton .
									$classLength . $showCancelled . '</div>' .
									$class_separator;
								}
							}
						$tbl->addCell($class_details, 'mz_description_holder');
					
					}//end foreach mz_classes
				}//end foreach mz_days
			$return .= $tbl->display();
		}//End if grid
			if ($type=='week')
				// schedule navigation
				$return .= mz_mbo_schedule_nav($mz_date, __('Week', 'mz-mindbody-api'));
			$return .= '<div id="mzModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mzSmallModalLabel" aria-hidden="true">
					 <div class="modal-content">

					</div>
			</div>';

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

		return $return;

	}//EOF mZ_show_schedule
	
}// EOF MZ_Mindbody_Schedule_Display Class

?>
