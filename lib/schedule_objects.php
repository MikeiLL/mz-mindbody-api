<?php

class Single_event {

	private $mz_mbo_globals;
	
	public $sDate;
	public $sLoc;
	public $sTG;
	public $studioid;
	public $sclassid;
	public $sclassidID;
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
	
	private $classStartTime;
	private $classEndTime;
	private $mbo_url;
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
	public $level; // accessing from another plugin
	
	public function __construct($class, $day_num='', $hide=array(), $locations, $hide_cancelled=0, $advanced, 
															$show_registrants, $registrants_count, $calendar_format='horizontal'){
		
		require_once(MZ_MINDBODY_SCHEDULE_DIR .'inc/mz_mbo_init.inc');
		$this->mz_mbo_globals = new MZ_Mindbody_Init();
		
		$this->sign_up_title = __('Sign-Up', 'mz-mindbody-api');
		$this->manage_text = __('Manage on MindBody Site', 'mz-mindbody-api');
		$this->sDate = date_i18n('m/d/Y', strtotime($class['StartDateTime']));
		$this->sLoc = $class['Location']['ID'];
		$this->sTG = $class['ClassDescription']['Program']['ID'];
		$this->studioid = $class['Location']['SiteID'];
		$this->sclassid = $class['ClassScheduleID'];
		$this->sclassidID = $class['ID'];
		$this->sessionTypeName = $class['ClassDescription']['SessionType']['Name'];
								//mz_pr($sclassidID);
		$this->classDescription = $class['ClassDescription']['Description'];
		$this->displayCancelled = ($class['IsCanceled'] == 1) ? '<div class="mz_cancelled_class">' .
												__('Cancelled', 'mz-mindbody-api') . '</div>' : '';
		$this->className = $class['ClassDescription']['Name'];
								//mz_pr($className);
		$this->startDateTime = date_i18n('Y-m-d H:i:s', strtotime($class['StartDateTime']));
		$this->startTime = date_i18n('H:i:s', strtotime($class['StartDateTime']));
								//mz_pr($startDateTime);
								//echo "<hr/>";
		$this->endDateTime = date_i18n('Y-m-d H:i:s', strtotime($class['EndDateTime']));
		$this->staffName = $class['Staff']['Name'];
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
		
		$this->advanced = $advanced;
		$this->calendar_format = $calendar_format;
		$this->time_of_day = $this->time_of_day_maker($this->startTime);
		$this->show_registrants = $show_registrants;
		$this->event_start = date_i18n($this->mz_mbo_globals->date_format . ' ' .$this->mz_mbo_globals->time_format, strtotime($this->startDateTime));
		$this->event_end = date_i18n($this->mz_mbo_globals->time_format, strtotime($this->endDateTime));
		
		$this->clientID = isset($_SESSION['GUID']) ? $_SESSION['client']['ID'] : '';
		$this->level = isset($class['ClassDescription']['Level']['Name']) ? $class['ClassDescription']['Level']['Name'] : '';

		if (($this->registrants_count == 1) && ($this->maxCapacity != ''))
			$this->toward_capacity = $this->totalBooked . '/' . $this->maxCapacity;
						
		//Let's find an image if there is one and assign it to $classImage

		if (!isset($class['ClassDescription']['ImageURL'])) {
			if (isset($class['ClassDescription']['AdditionalImageURLs']) && !empty($classImageArray)) {
				$this->classImage = pop($classImageArray);
			}
		} else {
			$this->classImage = $class['ClassDescription']['ImageURL'];
		}

		if(!in_array('teacher', $hide)){
			$this->teacher = __('with', 'mz-mindbody-api') . '&nbsp;' . $class['Staff']['Name'] .
			'<br/>';
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

		$this->mbo_url = $this->mbo_url($this->sDate, $this->sLoc, $this->sTG, $this->sType, $this->sclassid, $this->studioid);
				
		
		if(!in_array('signup', $hide)){
				$this->signupButton = $this->makeSignupButton($this->advanced, $this->calendar_format);
			}
											
		$this->class_name_link = $this->classLinkMaker($this->staffName, $this->className, 
																							$this->classDescription, $this->sclassidID, 
																							$this->staffImage, $this->show_registrants);
								
		if (isset($this->isAvailable) && ($this->isAvailable == 1)):		
			$sign_up_manage_links = $this->signupButton[0] . ' ' . $this->signupButton[1]	;
		else:
			$sign_up_manage_links = '';
		endif;			
											
		if ($this->calendar_format == 'grid'):																					
			$this->class_details .= $this->class_name_link->build() . '<br/>' .	 
			$this->teacher . $sign_up_manage_links .
			'<br/>' . $this->classLength . 
			$this->displayCancelled . '<br/>' . $this->locationNameDisplay . '</div>';
		elseif ($this->calendar_format == 'events'):
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
			$event_details = $title->build() . $teacher->build() . $times->build() . $location_display . $description->build() . $this->signupButton[0];
			$this->class_details .= $event_details . '<br />' .
			$this->displayCancelled . '<hr style="clear:both"/>';
		else:
			$this->class_details .= $this->class_name_link->build() . 
			'<br/><div id="visitMBO" class="btn visitMBO" style="display:none">' .
			'<a class="btn" href="'.$this->mbo_url.'" target="_blank">' .
			$this->manage_text . '</a></div>' .
			$this->displayCancelled;
		endif;

	}
	
	private function classLinkMaker($staffName, $className, $classDescription, $sclassidID, $staffImage, $show_registrants) {
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
												$linkArray['data-classID'] = $this->sclassidID;
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
	
	private function time_of_day_maker($classDate) {
			if ($classDate < 12) {
				return __('morning', 'mz-mindbody-api');
			}else if ($classDate > 16) {
				return __('evening', 'mz-mindbody-api');
			}else{
				return __('afternoon', 'mz-mindbody-api');
			}					
		}
	
	
	private function mbo_url($sDate, $sLoc, $sTG, $sType, $sclassid, $studioid) {
			$mbo_url = "https://clients.mindbodyonline.com/ws.asp?sDate={$sDate}&amp;sLoc={$sLoc}&amp;sTG={$sTG}&amp;sType={$sType}&amp;sclassid={$sclassid}&amp;studioid={$studioid}";
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
			$this->sign_up_text = '<span class="signup">'.$this->sign_up_title.'</span>';
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
						'data-classID' => $this->sclassidID, 
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
			/*if ($advanced == 1){
				if (isset($this->isAvailableisAvailable) && ($this->isAvailableisAvailable != 0)) {
					$add_to_class_nonce = wp_create_nonce( 'mz_MBO_add_to_class_nonce');
					if ($this->clientID == ''){
					 return '<a class="btn mz_add_to_class fa fa-sign-in" href="'.home_url().'/login"' .
					 'title="' . __('Login to Sign-up', 'mz-mindbody-api') . '"></a><br/>';
						}else{
						return '<br/><a id="mz_add_to_class" class="fa fa-sign-in mz_add_to_class"' 
						. 'title="' . $sign_up_title . '"'
						. ' data-nonce="' . $add_to_class_nonce 
						. '" data-className="' . $className 
						. '" data-classID="' . $sclassidID  
						. '" data-clientID="' . $clientID 
						. '" data-staffName="' . $staffName 
						. '"></a>' .
						'&nbsp; <span class="signup"> ' .
						'</span></a>&nbsp;' . 
						'<a id="visitMBO" class="fa fa-wrench visitMBO" href="'.$this->mbo_url.'" target="_blank" ' . 
						'style="display:none" title="' .
						$this->manage_text . '"></a><br/>';
						}
				}
			}else{
				return '&nbsp;<a href="'.$this->mbo_url.'" target="_blank" title="'.
								$this->sign_up_title. '"><i class="fa fa-sign-in"></i></a><br/>';
					}*/
					
		return array($sign_up_display, $manage_display);
	}
	
}
?>