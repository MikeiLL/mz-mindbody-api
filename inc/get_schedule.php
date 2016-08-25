<?php
	
class MZ_Mindbody_Get_Schedule {
	
	public function __construct(){
		require_once(MZ_MINDBODY_SCHEDULE_DIR .'inc/mz_mbo_init.inc');
		$this->mz_mbo_globals = new MZ_Mindbody_Init();
	}

	
	public function mZ_mindbody_get_schedule( $atts, $account=0 )
	{
		wp_enqueue_style('mZ_mindbody_schedule_bs', asset_path('styles/main.css'), false, null);
		wp_enqueue_script('modernizr', asset_path('scripts/modernizr.js'), array(), null, true);
		wp_enqueue_script('mz_mbo_bootstrap_script', asset_path('scripts/main.js'), array('jquery'), null, true);
		
		add_action('wp_footer', array($this, 'mbo_localize_main_js'));
		$mz_date = new DateTime();
		$mz_date = $mz_date->format('Y-m-d H:i:s');
		$mz_timeframe = array_slice(mz_getDateRange($mz_date, 30), 0, 1);
		$mb = MZ_Mindbody_Init::instantiate_mbo_API();
		if ($mb == 'NO_SOAP_SERVICE') {
			mz_pr($mb);
			}
		if ($account == 0) {
			$mz_schedule_data = $mb->GetClassSchedules($mz_timeframe);
			}else{
			$mb->sourceCredentials['SiteIDs'][0] = $account; 
			$mz_schedule_data = $mb->GetClassSchedules($mz_timeframe);
			}

		mz_pr($mz_schedule_data);
		} // EOF mZ_mindbody_get_schedule
	
	
}// EOF MZ_Mindbody_Get_Schedule Class



?>
