<?php
	
class MZ_Mindbody_Get_Schedule {
	
	public function __construct(){
		require_once(MZ_MINDBODY_SCHEDULE_DIR .'inc/mz_mbo_init.inc');
		$this->mz_mbo_globals = new MZ_Mindbody_Init();
	}

	/*
	 * This method creates a transient containing a hashed array assigning each
	 * Class ID, which is for a Unique, recurring "class", to some details with
	 * which we can disambiguate it from any other classes which might contain common data
	 * like Name, Description, Image.
	*/
	public function mZ_mindbody_get_schedule( $message='no message', $account=0 )
	{
	
		$mz_date = new DateTime();
		$mz_date = $mz_date->format('Y-m-d H:i:s');
		$mz_timeframe = array_slice(mz_getDateRange($mz_date, 30), 0, 1);
		$mb = MZ_Mindbody_Init::instantiate_mbo_API();
		if ($mb == 'NO_SOAP_SERVICE') {
			//fill in second two parameters with space holders and return error.
			return array($mb, '', array());
			}
		if ($account == 0) {
			$mz_schedule_data = $mb->GetClassSchedules($mz_timeframe);
			}else{
			$mb->sourceCredentials['SiteIDs'][0] = $account; 
			$mz_schedule_data = $mb->GetClassSchedules($mz_timeframe);
			}
		if ($mz_schedule_data['GetClassSchedulesResult']['Status'] != 'Success'):
			return array(__('There was an error populating schedule. Details below. Could be a network connection. Consider trying again.', 'mz-mindbody-api'),
									$mz_schedule_data['GetClassSchedulesResult']['Status'],
									$mz_schedule_data['GetClassSchedulesResult']);
		else:
			$schedules = $mz_schedule_data['GetClassSchedulesResult']['ClassSchedules'];
		endif;
		$class_owners = array();
		foreach($schedules as $schedule):
			foreach($schedule as $class):
			$classStartTime = new DateTime($class['StartTime']);
			$classStartDate = new DateTime($class['StartDate']);
			$class_image = isset($class['ClassDescription']['ImageURL']) ? $class['ClassDescription']['ImageURL'] : '';
			$class_description_array = explode(" ", $class['ClassDescription']['Description']);
			$class_description_substring = implode(" ", array_splice($class_description_array, 0, 5));
			$class_owners[$class['ID']] = array('class_name' => strip_tags($class['ClassDescription']['Name']),
																							'class_description' => $class_description_substring,
																							'class_owner' => strip_tags($class['Staff']['Name']),
																							'class_owner_id' => strip_tags($class['Staff']['ID']),
																							'image_url' => $class_image,
																							'time' => $classStartTime->format('H:i'),
																							'day' => $classStartDate->format('l'));
			endforeach;
		endforeach;
		delete_transient('mz_class_owners');
		set_transient('mz_class_owners', $class_owners, 60 * 60 * 24 * 7);
		//mz_pr($class_owners);
		if($message == 'message'):
			return __('Classes and teachers as regularly scheduled reloaded.', 'mz-mindbody-api');
		endif;
		} // EOF mZ_mindbody_get_schedule
	
	
}// EOF MZ_Mindbody_Get_Schedule Class



?>
