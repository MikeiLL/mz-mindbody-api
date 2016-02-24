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
	public $classImage;
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
	
	public function __construct($class, $day_num = ''){
		$this->sDate = date_i18n('m/d/Y', strtotime($class['StartDateTime']));
		$this->sLoc = $class['Location']['ID'];
		$this->sTG = $class['ClassDescription']['Program']['ID'];
		$this->studioid = $class['Location']['SiteID'];
		$this->sclassid = $class['ClassScheduleID'];
		$this->sclassidID = $class['ID'];
		$this->sessionTypeName = $class['ClassDescription']['SessionType']['Name'];
								//mz_pr($sclassidID);
		$this->classDescription = $class['ClassDescription']['Description'];
						
								//Let's find an image if there is one and assign it to $classImage
						
								if (!isset($class['ClassDescription']['ImageURL'])) {
			$this->classImage = '';
									if (isset($class['ClassDescription']['AdditionalImageURLs']) && !empty($classImageArray)) {
				$this->classImage = pop($classImageArray);
										}
								} else {
			$this->classImage = $class['ClassDescription']['ImageURL'];
								}

		$this->sType = -7;
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
	}
	
}
?>