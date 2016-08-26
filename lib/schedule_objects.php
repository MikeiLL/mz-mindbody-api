<?php

class Single_event {

	private static $subbed_classes = array();
	private static $regular_title_staff = array();

	private $mz_mbo_globals;
	
	public $sDate;
	public $sLoc;
	public $sTG;
	public $studioid;
	public $class_instance_ID;
	public $class_title_ID;
	public $sessionTypeName;
	public $classDescription;
	public $classImage = '';
	public $classImageArray;
	public $displayCancelled;
	public $className;
	public $startDateTime;
	public $endDateTime;
	public $staffName;
	public $isAvailable;
	public $locationName;
	public $staffImage;
	public $day_num; //for use in grid schedule display
	public $teacher = '';
	public $classLength = '';
	public $signupButton = '';
	public $locationAddress = '';
	public $locationAddress2 = '';
	public $locationNameDisplay = '';
	public $sign_up_title;
	public $sign_up_text = '';
	public $manage_text;
	public $class_details;
	public $toward_capacity = '';
	public $time_of_day;
	public $non_specified_class_times = array();
	public $scheduleType;
	public $mbo_url;
	public $event_start_and_end;
	public $level; // accessing from another plugin
	public $startTimeStamp;
	public $endTimeStamp;
	public $sub_link = '';
	public $staffModal;
	
	private $pluginoptions;
	private $classStartTime;
	private $classEndTime;
	private $sType = -7;
	private $session_type_css;
	private $class_name_css; 
	private $show_registrants;
	private $totalBooked; 
	private $maxCapacity;
	private $registrants_count;
	private $advanced;
	private $calendar_format;
	private $add_to_class_nonce = '';
	private $clientID;
	private $signUpButtonID;
	private $signup_button_class;
	private $event_start;
	private $event_end;
	private $is_substitute;
	private $staffID;
	private $siteID;
	private $delink;
	
	public function __construct($class, $day_num='', $hide=array(), $locations, $hide_cancelled=0, $advanced, 
															$show_registrants, $registrants_count, $calendar_format='horizontal', 
															$delink){

		require_once(MZ_MINDBODY_SCHEDULE_DIR .'inc/mz_mbo_init.inc');
		$this->mz_mbo_globals = new MZ_Mindbody_Init();
		$this->pluginoptions = get_option( 'mz_mindbody_options','Option Not Set' );
		$this->sign_up_title = __('Sign-Up', 'mz-mindbody-api');
		$this->manage_text = __('Manage on MindBody Site', 'mz-mindbody-api');
		$this->sDate = date_i18n('m/d/Y', strtotime($class['StartDateTime']));
		$this->sLoc = $class['Location']['ID'];
		$this->sTG = $class['ClassDescription']['Program']['ID'];
		$this->studioid = $class['Location']['SiteID'];
		$this->class_instance_ID = $class['ClassScheduleID'];
		$this->class_title_ID = $class['ID'];
		$this->sessionTypeName = $class['ClassDescription']['SessionType']['Name'];
								//mz_pr($class_title_ID);
		$this->classDescription = $class['ClassDescription']['Description'];
		$this->displayCancelled = ($class['IsCanceled'] == 1) ? '<div class="mz_cancelled_class">' .
												__('Cancelled', 'mz-mindbody-api') . '</div>' : '';
		//htmlspecialchars ( string $string [, int $flags = ENT_COMPAT | ENT_HTML401 [, string $encoding = ini_get("default_charset") [, bool $double_encode = true ]]] )
		$this->className = htmlspecialchars( $class['ClassDescription']['Name'] );
		$this->startDateTime = date_i18n('Y-m-d H:i:s', strtotime($class['StartDateTime']));
		$this->startTime = date_i18n('H:i:s', strtotime($class['StartDateTime']));
		$this->startTimeStamp = strtotime($class['StartDateTime']);
		$this->endTimeStamp = strtotime($class['EndDateTime']);
								//mz_pr($startDateTime);
								//echo "<hr/>";
		$this->endDateTime = date_i18n('Y-m-d H:i:s', strtotime($class['EndDateTime']));
		$this->is_substitute = $class['Substitute'];
		$this->staffName = $class['Staff']['Name'];
		$this->staffID = $class['Staff']['ID'];
		$this->isAvailable = $class['IsAvailable'];
		$this->locationName = $class['Location']['Name'];
		$this->staffImage = isset($class['Staff']['ImageURL']) ? $class['Staff']['ImageURL'] : '';
		$this->day_num = $day_num;
		
		$this->session_type_css = sanitize_html_class($this->sessionTypeName, 'mz_session_type');
		$this->class_name_css = sanitize_html_class($this->className, 'mz_class_name');
		$this->class_details .= '<div class="mz_schedule_table mz_description_holder mz_location_'.$this->sLoc.' '.'mz_' . 
		$this->session_type_css .' mz_'. $this->class_name_css .'">';
		$this->totalBooked = $class['TotalBooked'];
		$this->maxCapacity = $class['MaxCapacity'];
		$this->scheduleType = $class['ClassDescription']['Program']['ScheduleType'];
		
		$this->advanced = $advanced;
		$this->calendar_format = $calendar_format;
		$this->time_of_day = $this->time_of_day_maker($this->startTime);
		$this->show_registrants = $show_registrants;
		$this->event_start = date_i18n($this->mz_mbo_globals->date_format . ' ' .$this->mz_mbo_globals->time_format, strtotime($this->startDateTime));
		$this->event_end = date_i18n($this->mz_mbo_globals->time_format, strtotime($this->endDateTime));
		
		$this->clientID = isset($_SESSION['GUID']) ? $_SESSION['client']['ID'] : '';
		$this->level = isset($class['ClassDescription']['Level']['Name']) ? $class['ClassDescription']['Level']['Name'] : '';
		$teacherLink = $this->teacherLinkMaker($this->staffID,$this->staffName);
		$this->staffModal = array_shift($teacherLink)->build();
		$this->event_start_and_end = $this->event_start . ' - ' . $this->event_end;
		$this->delink = $delink;
		
		//if ($this->is_substitute):
			//$this->staffModal = $this->teacherLinkMaker($class_owners[$this->class_title_ID][0], $class_owners[$this->class_title_ID][1])->build();
		//endif;

		// Create a non-specific schedule time for use in mz-mbo-pages
		$non_specific_class_time = date_i18n('l g:i a', strtotime($class['StartDateTime'])). ' - ' .
															 date_i18n('g:i a', strtotime($class['EndDateTime'])) . '&nbsp;' .
																'<span class="schedule_location">(' . $this->locationName . ')</span>';
																
		array_push($this->non_specified_class_times, $non_specific_class_time);
		
		
		if (($this->registrants_count == 1) && ($this->maxCapacity != ''))
			$this->toward_capacity = $this->totalBooked . '/' . $this->maxCapacity;
						
		//Let's find an image if there is one and assign it to $classImage

		if (!isset($class['ClassDescription']['ImageURL'])) {
			if (isset($class['ClassDescription']['AdditionalImageURLs']) && !empty($classImageArray)) {
				$this->classImage = array_pop($classImageArray);
			}
		} else {
			$this->classImage = $class['ClassDescription']['ImageURL'];
		}

		if(!in_array('teacher', $hide)){
			$this->teacher = ' ' . __('with', 'mz-mindbody-api') . ' ' . $this->staffModal;

			if ($this->is_substitute == 1):
				if ( $class_owners = get_transient( 'mz_class_owners' ) ):
					foreach($class_owners as $id => $details):
					
				$class_description_array = explode(" ", $this->classDescription);
				$class_description_substring = implode(" ", array_splice($class_description_array, 0, 5));
						if(($details['class_name'] == $this->className) && 
								($details['class_description'] == $class_description_substring) &&
							  ($this->classImage == $details['image_url'])):
							
							$class_owner = $details;
							$substitute_button_object = $this->teacherLinkMaker($this->staffID,'s', $class_owner);
							$this->sub_link = array_pop($substitute_button_object)->build();
							break;
						endif;
							// or else if name not found 
							$class_owner = '';
							$substitute_button_object = $this->teacherLinkMaker($this->staffID,'', $class_owner);
							$this->sub_link = '<span class="mz-substitute">s</span>';
					endforeach;
				else:
					$class_owner = '';
					$substitute_button_object = $this->teacherLinkMaker($this->staffID,'', $class_owner);
					$this->sub_link =  '<span class="mz-substitute">s</span>';
				endif;
				
				
				$this->teacher .= ' ' . $this->sub_link;
			endif;
			$this->teacher .= '<br />';
			}
			
		if(!in_array('duration', $hide) && ($class['IsCanceled'] != 1)){
			$this->classStartTime = new DateTime($class['StartDateTime']);
			$this->classEndTime = new DateTime($class['EndDateTime']);
			if (phpversion() >= 5.3) {
					$this->classLength = $this->classEndTime->diff($this->classStartTime);
					$this->classLength = __('Duration:', 'mz-mindbody-api') . 
					'<br/>&nbsp;' . $this->classLength->format('%H:%I');
				}else{
					$this->classLength = round(($this->classEndTime->format('U') - $this->classStartTime->format('U')));
					$this->classLength = __('Duration:', 'mz-mindbody-api') . 
					'<br/>&nbsp;' . gmdate("H:i", $this->classLength);
				}
		}
		
		if (count($locations) > 1) {
				// TODO Let's not do this loop every time. Ouch.
				$this->location_name_css = sanitize_html_class($this->locationName, 'mz_location_class');
				$this->locationAddress = $class['Location']['Address'];
				$this->locationAddress2 = $class['Location']['Address2'];
				$this->url_encoded_address = urlencode($this->locationAddress.$this->locationAddress2);
				$this->locationNameDisplay = '<span class="location_name '.$this->location_name_css.'"><a href="http://maps.google.com/maps?q='.$this->url_encoded_address.'" target="_blank" title="'. $this->locationAddress. '">' . 
										$this->locationName . '</a>';
			}

		$this->mbo_url = $this->mbo_url($this->sDate, $this->sLoc, $this->sTG, $this->sType, $this->class_instance_ID, $this->studioid);
				
		
		if(!in_array('signup', $hide)){
				$this->signupButton = $this->makeSignupButton($this->advanced, $this->calendar_format);
			}
		
		
		$this->class_name_link = $this->classLinkMaker($this->staffName, $this->className, 
																							$this->classDescription, $this->class_title_ID, 
																							$this->staffImage, $this->show_registrants);
								
		if (isset($this->isAvailable) && ($this->isAvailable == 1)):		
			$sign_up_manage_links = $this->signupButton[0] . ' ' . $this->signupButton[1]	;
		else:
			$sign_up_manage_links = '';
		endif;			
		
		if ($this->calendar_format == 'grid'):			
			if (($this->delink != 1) && ($this->delink != 3)):																		
				$this->class_details .= $this->class_name_link->build() . '<br/>';
			else:
				$this->class_details .= '<span class="mz_class_name">' . $this->className . '</span>';
			endif; 
			$this->class_details .= $this->teacher . $sign_up_manage_links .
			'<br/>' . $this->classLength . 
			$this->displayCancelled . '<br/>' . $this->locationNameDisplay . '</div>';
		elseif ($this->calendar_format == 'events' || $this->calendar_format == 'overview'):
			$image = new html_element('img');
			$image->set('class', 'mz_event_image');
			if (isset($this->classImage) && $this->classImage != '') {
				$image->set('src', $this->classImage);
			}
			else if (isset($this->staffImage) && $this->staffImage != '') {
				$image->set('src', $this->staffImage);
			}
			else {
			$image = '';
			$display_image = '';
			}
			if ($image != '') {
				$image_container = new html_element('div');
				$image_container->set('class', 'wp-caption mz_event_image_container');
				$image_caption = new html_element('p');
				$image_caption->set('class', 'wp-caption-text');
				$image_caption->set('text', $this->className);
				$image_container->set('text', $image->build() . $image_caption->build());
				$display_image = $image_container->build();
			}
			
			$title = new html_element('h2');
			$title->set('text', $this->className);
			$title->set('class', 'event_title ' . $this->class_name_css);
			$teacher = new html_element('h3');
			$teacher->set('text', $this->teacher);
			$times = new html_element('h4');
			$times->set('text', $this->event_start . ' - ' . $this->event_end);
			
			if (isset($this->locationNameDisplay) && $this->locationNameDisplay != ''):
				$location = new html_element('h4');
				$the_word_at = __('at', 'mz-mindbody-api');
				$location->set('text', $the_word_at . ' ' . $this->locationNameDisplay . ' ');
				$location_display = $location->build();
			else: 
				$location_display = '';
			endif;
			
			$description = new html_element('p');
			$description->set('text', $display_image . $this->classDescription);
			$event_details = $title->build();
			$event_details_array[0] = $teacher->build();
			$event_details_array[1] = $location_display;
			$event_details_array[2] = $times->build();
			$event_details_array[3] = $description->build();
			if ($this->calendar_format !== 'overview'):
				// Using "overview" for access by mz-mbo-pages plugin.
				foreach($event_details_array as $detail){
						$event_details .= $detail;
					}
			$this->displayCancelled . '<hr class="class-event-divider" style="clear:both" />';
			else:
				$event_details = $event_details_array[1] . $event_details_array[3];
				$this->displayCancelled = '';
			endif;
			$this->class_details .= $event_details . '<br />' .
			$this->displayCancelled;
		else: // This is just the title for the horizontal calendar.
			if (($this->delink != 1) && ($this->delink != 3)):																		
				$this->class_details .= $this->class_name_link->build();
			else:
				$this->class_details .= '<span class="mz_class_name">' . $this->className . '</span>';
			endif; 
			$this->class_details .= '<br/><div id="visitMBO" class="btn visitMBO" style="display:none">' .
			'<a class="btn" href="'.$this->mbo_url.'" target="_blank">' .
			$this->manage_text . '</a></div>' .
			$this->displayCancelled;
		endif;
		
		if ($this->calendar_format == 'events')
			$this->class_details .= $sign_up_manage_links . '<hr class="class-event-divider" style="clear:both" />';

	} // Construct
	
	private function classLinkMaker($staffName, $className, $classDescription, $class_title_ID, $staffImage, $show_registrants) {
			/* Build and return an href object for each class/event
			to use in creating popup modal */
			
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
												$linkArray['data-classID'] = $this->class_title_ID;
											} else {
												$linkArray['data-target'] = "#mzModal";
											}
								if ($staffImage != ''):
									$linkArray['data-staffImage'] = $this->staffImage;
								endif;
						
				$class_name_link = new html_element('a');
				$class_name_link->set('href', MZ_MINDBODY_SCHEDULE_URL . 'inc/modal_descriptions.php');
				$class_name_link->set($linkArray);
				return $class_name_link;
	}
	
	private function teacherLinkMaker($staffID, $staffName, $class_owner='') {
			/* Build and return an href object for each class/event
			to use in creating popup modal */
			$staff_name_css = 'modal-toggle mz_get_staff mz_staff_name ';
			if ($staffName == 's'):
				$substituted_alert = __('Substitute', 'mz-mindbody-api');
				$staff_name_css .= 'mz-substitute ';
				$linkArray = array(
											'data-staffName'=>$class_owner['class_owner'],
											'data-siteID'=>$this->pluginoptions['mz_mindbody_siteID'],
											'data-staffID'=>$class_owner['class_owner_id'],
											'data-sub'=>'sub',
											'class'=> $staff_name_css,
											'title'=>$substituted_alert
											);
				$get_registrants_nonce = wp_create_nonce( 'mz_MBO_get_registrants_nonce');
				$linkArray['data-nonce'] = $get_registrants_nonce;
				$linkArray['data-target'] = "#mzStaffScheduleModal";  
				$linkArray['data-classID'] = $this->class_title_ID;
				$substitute_link = new html_element('a');
				$substitute_link->set('text', $staffName);
				$substitute_link->set('href', MZ_MINDBODY_SCHEDULE_URL . 'inc/modal_descriptions.php');
				$substitute_link->set($linkArray);
			else:
				$substitute_link = '';
			endif;
			
			$linkArray = array(
												'data-staffName'=>$staffName,
												'data-siteID'=>$this->pluginoptions['mz_mindbody_siteID'],
												'data-staffID'=>$staffID,
												'class'=> $staff_name_css
												);
			$get_registrants_nonce = wp_create_nonce( 'mz_MBO_get_registrants_nonce');
			$linkArray['data-nonce'] = $get_registrants_nonce;
			$linkArray['data-target'] = "#mzStaffScheduleModal";  
			$linkArray['data-classID'] = $this->class_title_ID;
			$class_name_link = new html_element('a');
			$class_name_link->set('text', $staffName);
			$class_name_link->set('href', MZ_MINDBODY_SCHEDULE_URL . 'inc/modal_descriptions.php');
			$class_name_link->set($linkArray);
				return array($class_name_link, $substitute_link);
	}
	
	private function time_of_day_maker($classDate) {
			if ($classDate < 12) {
				return __('morning', 'mz-mindbody-api');
			}else if ($classDate > 16) {
				return __('evening', 'mz-mindbody-api');
			}else{
				return __('afternoon', 'mz-mindbody-api');
			}					
		}
	
	
	private function mbo_url($sDate, $sLoc, $sTG, $sType, $class_instance_ID, $studioid) {
			$mbo_url = "https://clients.mindbodyonline.com/ws.asp?sDate={$sDate}&amp;sLoc={$sLoc}&amp;sTG={$sTG}&amp;sType={$sType}&amp;class_instance_ID={$class_instance_ID}&amp;studioid={$studioid}";
			return $mbo_url;
		}
	
	
	private function makeSignupButton($advanced, $calendar_format) {
		/*
		Render Sign-up and Manage buttons
		*/
		
		if (($this->maxCapacity != "") && ($this->totalBooked == $this->maxCapacity)):
			$this->sign_up_title = __('Sign-Up for waiting list', 'mz-mindbody-api');
		endif;

		if ($calendar_format == 'grid'):
			$this->signup_button_class = "mz_add_to_class fa fa-sign-in";
			$manage_button_class = "fa fa-wrench visitMBO";
		else:
			$this->signup_button_class = "mz_add_to_class btn";
			$manage_button_class = "visitMBO";
			$this->sign_up_text = '<span class="signup">'. __('Sign-up', 'mz-mindbody-api') .'</span>';
		endif;
		
		$signup_target = "_blank";
		
		$sign_up_link = new html_element('a');
		$manage_link = new html_element('a');
		
		if ($advanced == 1):
			$this->add_to_class_nonce = wp_create_nonce( 'mz_MBO_add_to_class_nonce');
			$signup_target = "_parent";
			if ($this->clientID == ''):
				$signupURL = home_url() . '/login';
				$sign_up_link->set('href', $signupURL);
				$this->sign_up_title = __('Login to Sign-up', 'mz-mindbody-api');
				$this->signUpButtonID = 'mz_login';
			else:
				//$signupURL = '';
				$sign_up_link = new html_element('a');
				//TODO figure out why next line is required to not break html_class
				$sign_up_link->set('link', '');
				$this->signUpButtonID = 'mz_add_to_class';
			endif;

				$sign_up_display = '';
				$manage_display = '';		
		else: // If Not Advanced
				$signupURL = $this->mbo_url;
				$sign_up_link->set('href', $signupURL);
		endif; 
		
		$signupLinkArray = array(
						'id' => $this->signUpButtonID,
						'class' => $this->signup_button_class,
						'title' => $this->sign_up_title,
						'target' => $signup_target,
						'data-nonce' => $this->add_to_class_nonce, 
						'data-className' => $this->className,
						'data-classID' => $this->class_title_ID, 
						'data-clientID' => $this->clientID,
						'data-staffName' => $this->staffName,
						'text' => $this->sign_up_text
						);
	
		$manageLinkArray = array(
					'id' => "visitMBO",
					'class' => $manage_button_class,
					'title' => $this->manage_text,
					'target' => "_blank",
					'style' => "display:none",
					'text' => ''
					);
		

		$sign_up_link->set($signupLinkArray);		
		$sign_up_display = $sign_up_link->build();
		
		$manage_link->set('href', $this->mbo_url);
		$manage_link->set($manageLinkArray);
		$manage_display = $manage_link->build();
					
		return array($sign_up_display, $manage_display);
	}
	
}
?>