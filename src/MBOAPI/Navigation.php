<?php
namespace mZoo\MBOAPI;

class Navigation () {
	public static function render_schedule_navigation($date, $period, $duration=7)
	{
		$sched_nav = '<div class="mz_schedule_nav_holder">';
		if (!isset($period)){
			$period = __('Week',' mz-mindbody-api');
			}
		$mz_schedule_page = get_permalink();
		//Navigate through the weeks
		$mz_start_end_date = Schedule_Operations::mz_getDateRange($date, $duration);
		$mz_nav_weeks_text_prev = sprintf(__('Previous %1$s','mz-mindbody-api'), $period);
		$mz_nav_weeks_text_current = sprintf(__('Current %1$s','mz-mindbody-api'), $period);
		$mz_nav_weeks_text_following = sprintf(__('Following %1$s','mz-mindbody-api'), $period);
		$sched_nav .= ' <a href='.add_query_arg(array('mz_date' => ($mz_start_end_date[2]))).'>'.$mz_nav_weeks_text_prev.'</a> - ';
		if (isset($_GET['mz_date'])):
			global $wp;
			$current_url = home_url(add_query_arg(array(),$wp->request));
			$sched_nav .= ' <a href='.home_url(add_query_arg(array(),$wp->request)).'>'.$mz_nav_weeks_text_current.'</a>  - ';
		endif;
		$sched_nav .= '<a href='.add_query_arg(array('mz_date' => ($mz_start_end_date[1]))).'>'.$mz_nav_weeks_text_following.'</a>';
		$sched_nav .= '</div>';

		return $sched_nav;
	}
}

?>