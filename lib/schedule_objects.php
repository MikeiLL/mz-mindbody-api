<?php

class Single_event {

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
	public $locationNameDisplay = '';
	public $sign_up_text;
	public $manage_text;
	public $class_details;
	
	private $classStartTime;
	private $classEndTime;
	private $mbo_url;
	private $sType = -7;
	private $session_type_css;
	private $class_name_css;
	private $show_registrants;
	
	public function __construct($class, $day_num='', $hide, $locations, $advanced, $show_registrants){
	
		$this->sign_up_text = __('Sign-Up', 'mz-mindbody-api');
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
				$this->locationNameDisplay = '<div class="'.$this->location_name_css.'"><a href="#" title="'. $this->locationAddress. '">' . 
										$this->locationName . '</a>';
			}

			$this->mbo_url = $this->mbo_url($this->sDate, $this->sLoc, $this->sTG, $this->sType, $this->sclassid, $this->studioid);
					
				if ($class['TotalBooked'] == $class['MaxCapacity']):
					$sign_up_text = __('Sign-Up for waiting list', 'mz-mindbody-api');
				endif;
				if(!in_array('signup', $hide)){
					if ($advanced == 1){
						if (isset($isAvailable) && ($isAvailable != 0)) {
							$add_to_class_nonce = wp_create_nonce( 'mz_MBO_add_to_class_nonce');
							if ($clientID == ''){
								 $this->signupButton = '<a class="btn mz_add_to_class fa fa-sign-in" href="'.home_url().'/login"' .
								 'title="' . __('Login to Sign-up', 'mz-mindbody-api') . '"></a><br/>';
									}else{
									$this->signupButton = '<br/><a id="mz_add_to_class" class="fa fa-sign-in mz_add_to_class"' 
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
									$this->manage_text . '"></a><br/>';
									}
							}
						}else{
							$this->signupButton = '&nbsp;<a href="'.$linkURL.'" target="_blank" title="'.
											$this->sign_up_text. '"><i class="fa fa-sign-in"></i></a><br/>';
								}
				}
									
		
		
		$this->class_name_link = $this->classLinkMaker($this->staffName, $this->className, 
																							$this->classDescription, $this->sclassidID, 
																							$this->staffImage, $this->show_registrants);
																							
		$this->class_details .= $this->class_name_link->build() . 
									'<br/>' .	 
									$this->teacher . $this->signupButton .
									$this->classLength . $this->displayCancelled . $this->locationNameDisplay . 
									$this->startDateTime . "<br/>" .
									$this->locationName . '</div>';

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
	
	private function time_of_day($classDate) {
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
	
	}
?>