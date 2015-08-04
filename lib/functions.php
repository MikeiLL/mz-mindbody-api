<?php

function mz_getDateRange($date, $duration=7) {
    /*Gets a YYYY-mm-dd date and returns an array of four dates:
        start of requested week
        end of requested week 
        following week start date
        previous week start date
    adapted from http://stackoverflow.com/questions/186431/calculating-days-of-week-given-a-week-number
    */

    list($year, $month, $day) = explode("-", $date);

    // Get the weekday of the given date
    $wkday = date_i18n('l',mktime('0','0','0', $month, $day, $year));

    switch($wkday) {
        case __('Monday', 'mz-mindbody-api'): $numDaysFromMon = 0; break;
        case __('Tuesday', 'mz-mindbody-api'): $numDaysFromMon = 1; break;
        case __('Wednesday', 'mz-mindbody-api'): $numDaysFromMon = 2; break;
        case __('Thursday', 'mz-mindbody-api'): $numDaysFromMon = 3; break;
        case __('Friday', 'mz-mindbody-api'): $numDaysFromMon = 4; break;
        case __('Saturday', 'mz-mindbody-api'): $numDaysFromMon = 5; break;
        case __('Sunday', 'mz-mindbody-api'): $numDaysFromMon = 6; break;   
    }

    // Timestamp of the monday for that week
    $seconds_in_a_day = 86400;
    
    $monday = mktime('0','0','0', $month, $day-$numDaysFromMon, $year);
    $today = mktime('0','0','0', $month, $day, $year);
    if ($duration == 1){
        $rangeEnd = $today+($seconds_in_a_day*$duration);
    }else{
        $rangeEnd = $today+($seconds_in_a_day*($duration - $numDaysFromMon));
    }
    $previousRangeStart = $monday+($seconds_in_a_day*($numDaysFromMon - ($numDaysFromMon+$duration)));
    
    $return[0] = array('StartDateTime'=>date('Y-m-d',$today), 'EndDateTime'=>date('Y-m-d',$rangeEnd-1));
    //$return[1] = date('Y-m-d',$rangeEnd-1);
    $return[1] = date('Y-m-d',$rangeEnd+1); 
    $return[2] = date('Y-m-d',$previousRangeStart); 
    return $return;
}

class Global_Strings {
 // property declaration

	// method declaration
	public function translate_them() {
		return array( 'username' => __( 'Username', 'mz-mindbody-api' ),
					  'password' => __( 'Password', 'mz-mindbody-api' ),
					  'login' => __( 'Login', 'mz-mindbody-api' ),
					  'logout' => __( 'Log Out', 'mz-mindbody-api' ),
					  'sign_up' => __( 'Sign Up', 'mz-mindbody-api' ),
					  'login_to_sign_up' => __( 'Log in to Sign up', 'mz-mindbody-api' ),
					  'manage_on_mbo' => __('Manage on MindBody Site', 'mz-mindbody-api' ),
					  'login_url' => __( 'login', 'mz-mindbody-api' ),
					  'logout_url' => __( 'logout', 'mz-mindbody-api' ),
					  'signup_url' => __( 'signup', 'mz-mindbody-api' ),
					  'create_account_url' => __('create-account', 'mz-mindbody-api' ),
					  'or' => __( 'or', 'mz-mindbody-api' ),
					  'with' => __( 'with', 'mz-mindbody-api' ),
					  'day' => __( 'day', 'mz-mindbody-api' ),
					  'shortcode' => __( 'shortcode', 'mz-mindbody-api' ),
						);
	}
}
	
function mz_mbo_schedule_nav($date, $period, $duration=7)
{
	$sched_nav = '';
	if (!isset($period)){
		$period = __('Week',' mz-mindbody-api');
		}
	$mz_schedule_page = get_permalink();
	//Navigate through the weeks
	$mz_start_end_date = mz_getDateRange($date, $duration);
	$mz_nav_weeks_text_prev = sprintf(__('Previous %1$s','mz-mindbody-api'), $period);
	$mz_nav_weeks_text_current = sprintf(__('Current %1$s','mz-mindbody-api'), $period);
	$mz_nav_weeks_text_following = sprintf(__('Following %1$s','mz-mindbody-api'), $period);
	$sched_nav .= ' <a href='.add_query_arg(array('mz_date' => ($mz_start_end_date[2]))).'>'.$mz_nav_weeks_text_prev.'</a> - ';
	if (isset($_GET['mz_date']))
	    $sched_nav .= ' <a href='.$mz_schedule_page.'>'.$mz_nav_weeks_text_current.'</a>  - ';
	$sched_nav .= '<a href='.add_query_arg(array('mz_date' => ($mz_start_end_date[1]))).'>'.$mz_nav_weeks_text_following.'</a>';

	return $sched_nav;
}


function mz_validate_date( $string ) {
	if (preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/',$string))
	{
		return $string;
	}
	else
	{
		return "mz_validate_weeknum error";
	}
}
	
//For Testing
function mZ_write_to_file($message){
        $handle = fopen("/Applications/MAMP/logs/mZ_mbo_reader.php", "a+");
        fwrite($handle, "\nMessage:\t " . $message);
        fclose($handle);
    }

//Format arrays for display in development
function mz_pr($data)
{
  echo "<pre>";
  print_r($data);
  echo "</pre>";
}

?>