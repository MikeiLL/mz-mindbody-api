<?php
/**
 * Summary.
 *
 * Gets a YYYY-mm-dd date and returns an array of four dates:
 *      start of requested week
 *      end of requested week 
 *      following week start date
 *      previous week start date.
 *
 * @since 1.0
 * @source (initially adapted) 
 * http://stackoverflow.com/questions/186431/calculating-days-of-week-given-a-week-number
 * Used by mz_show_schedule(), mz_show_events(), mz_mindbody_debug_text()
 * also used by mZ_mbo_pages_pages() in mZ MBO Pages plugin
 *
 * @param var $date Start date for date range.
 * @param var $duration Optional. Description. Default.
 * @return array Start Date, End Date and Previous Range Start Date.
 */
function mz_getDateRange($date, $duration=7) {
    /*Gets a YYYY-mm-dd date and returns an array of four dates:
        start of requested week
        end of requested week 
        following week start date
        previous week start date
    adapted from http://stackoverflow.com/questions/186431/calculating-days-of-week-given-a-week-number
    */
    $seconds_in_a_day = 86400;
    $start = new DateTime($date);
    $now = new DateTime(null, mz_mbo_198435_get_blog_timezone());
    switch ($now->format('l')) {
			case 'Tuesday':
				$into_following_week = 1;
			case 'Wednesday':
				$into_following_week = 2;
			case 'Thursday':
				$into_following_week = 3;
				break;
			case 'Friday':
				$into_following_week = 4;
				break;
			case 'Saturday':
				$into_following_week = 5;
				break;
			case 'Sunday':
				$into_following_week = 6;
				break;
			default:
				$into_following_week = 0;
				break;

		}
    $end = clone $start;
    $previous = clone $start;
    $subsequent = clone $start;
    $subsequent->add(new DateInterval('P'. ($duration) .'D'));
    $end->add(new DateInterval('P'. ($duration + $into_following_week) .'D'));
    $previous->sub(new DateInterval('P'. $duration .'D'));
    $return[0] = array('StartDateTime'=> $start->format('Y-m-d'), 'EndDateTime'=> $end->format('Y-m-d'));

    $return[1] = $subsequent->modify('Monday this week')->format('Y-m-d'); 
    $return[2] = $previous->modify('Monday this week')->format('Y-m-d');

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
	$sched_nav = '<div class="mz_schedule_nav_holder">';
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
	if (isset($_GET['mz_date'])):
		global $wp;
		$current_url = home_url(add_query_arg(array(),$wp->request));
	  $sched_nav .= ' <a href='.home_url(add_query_arg(array(),$wp->request)).'>'.$mz_nav_weeks_text_current.'</a>  - ';
	endif;
	$sched_nav .= '<a href='.add_query_arg(array('mz_date' => ($mz_start_end_date[1]))).'>'.$mz_nav_weeks_text_following.'</a>';
	$sched_nav .= '</div>';

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

/* create an html element, like in js 
 * Source: https://davidwalsh.name/create-html-elements-php-htmlelement-class
 */
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
// EOF create an html element


// Following two functions used for usort in older php versions.

function mz_sort_horizontal_times($a, $b) {
				if(strtotime($a->startDateTime) == strtotime($b->startDateTime)) {
 					return 0;
 				}
 				return $a->startDateTime < $b->startDateTime ? -1 : 1;
			}
			
function mz_sort_grid_times ($a, $b) {
			if(date_i18n("N", strtotime($a->startDateTime)) == date_i18n("N", strtotime($b->startDateTime))) {
				return 0;
			}
			return $a->startDateTime < $b->startDateTime ? -1 : 1;
		}
			
/**
 * Return an array of MBO Class Objects, ordered by date.
 *
 * This is used in Horizontal view. It gets the filtered results from the MBO API call and  builds an array of Class Event Objects,
 * sequenced by date and time.
 * 
 *
 * @param @type array $mz_classes
 * @param @type string $time_format Format string for php strtotime function Default: "g:i a"
 * @param @type array OR numeric $locations Single or list of MBO location numerals Default: 1
 * @param @type boolean $hide_cancelled Whether or not to display cancelled classes. Default: 0
 * @param @type array $hide Items to be removed from calendar
 * @param @type boolean $advanced Whether or not allowing online class sign-up via plugin
 * @param @type boolean $show_registrants Whether or not to display class registrants in modal popup
 * @param @type boolean $registrants_count  Whether we want to show count of registrants in a class (TODO - finish) @default: 0
 * @param @type string $calendar_format Depending on final display, we may create items in Single_event class differently. 
 *																			Default: 'horizontal'
 * @param @type boolean $delink Make class name NOT a link
 * @param @type string $class_type MBO API has 'Enrollment' and 'DropIn'. 'Enrolment' is a "workdhop". Default: 'Enrollment'
 * @param @type numeric $account Which MBO account is being interfaced with.
 *
 * @return @type array of Objects from Single_event class, in Date (and time) sequence.
*/
function sortClassesByDate($mz_classes = array(), 
													$time_format = "g:i a", 
													$locations=1, 
													$hide_cancelled=0, 
													$hide=array(), 
													$advanced=0, 
													$show_registrants=0, 
													$registrants_count=0, 
													$calendar_format='horizontal', 
													$delink, 
													$class_type='Enrollment', 
													$account) {

	// This is the array that will hold the classes we want to display
	$mz_classesByDate = array();

	if(!is_array($locations)):
		$locations = array($locations);
	endif;
	
	if (isset($_GET['mz_date'])):
		// If user has requested a specific start date, return dates for that period
		list($current, $current_date_string) = current_to_day_of_week_today();
	else:
		$current = new DateTime(null, mz_mbo_198435_get_blog_timezone());
		$blog_timezone = new DateTime(null, mz_mbo_198435_get_blog_timezone());
		$current_date_string = $blog_timezone->format('Y-m-d');
	endif;
	
	$end_of_week = clone $current;
	$end_of_week = $end_of_week->add(new DateInterval('P6D'))->format('Y-m-d');

	foreach($mz_classes as $class)
	{
		
		if ($hide_cancelled == 1):
			if ($class['IsCanceled'] == 1):
				continue;
			endif;
		endif;
		
		$classDate = date("Y-m-d", strtotime($class['StartDateTime']));

		// Ignore classes that are outside of seven day week starting today
		if ($classDate < $current_date_string || $classDate > $end_of_week):
			continue;
		endif;
		
		/* Create a new array with a key for each date YYYY-MM-DD
		and corresponsing value an array of class details */ 

		$single_event = new Single_event($class, $daynum="", $hide=array(), $locations, $hide_cancelled=0, 
																			$advanced, $show_registrants, $registrants_count, 
																			$calendar_format, $delink, $account);
		
		if(!empty($mz_classesByDate[$classDate])) {
			if (
				(!in_array($class['Location']['ID'], $locations)) || 
				($class['ClassDescription']['Program']['ScheduleType'] == $class_type)
				) {
					continue;
				}
			//$mz_classesByDate[$classDate] = array_merge($mz_classesByDate[$classDate], array($class));
			array_push($mz_classesByDate[$classDate]['classes'], $single_event);
		} else {
			if (
				(!in_array($class['Location']['ID'], $locations)) || 
				($class['ClassDescription']['Program']['ScheduleType'] == $class_type)
				) {
					continue;
				}
			//$mz_classesByDate[$classDate]['classes'] = $single_event;
			$mz_classesByDate[$classDate] = array('classes' => array($single_event));
		}
	}
	/* They are not ordered by date so order them by date */
	ksort($mz_classesByDate);
	foreach($mz_classesByDate as $classDate => &$mz_classes)
	{	
		/*
		$mz_classes is an array of all classes for given date
		Take each of the class arrays and order it by time
		$mz_classesByDate sould have a length of seven, one for
		each day of the week.
		*/

		if (phpversion() >= 5.3) {
			usort($mz_classes['classes'], function($a, $b) {
				if(strtotime($a->startDateTime) == strtotime($b->startDateTime)) {
					return 0;
					}
				return $a->startDateTime < $b->startDateTime ? -1 : 1;
				}); 
			} else {
			usort($mz_classes['classes'], 'mz_sort_horizontal_times');
		}
	}
		
	return $mz_classesByDate;
}

/**
 * Return an array of MBO Class Objects, ordered by date.
 *
 * This is used in Grid view. It gets the filtered results from the MBO API call and builds a matrix, top level of which is
 * seven arrays, one for each of seven days in a week (for a calendar column), each one of the Day columns contains an array
 * of Class Event objects, sequenced by time of day, earliest to latest.
 * 
 *
 * @param @type array $mz_classes
 * @param @type string $time_format Format string for php strtotime function Default: "g:i a"
 * @param @type array/numeric $locations Single or list of MBO location numerals Default: 1
 * @param @type boolean $hide_cancelled Whether or not to display cancelled classes. Default: 0
 * @param @type array $hide Items to be removed from calendar
 * @param @type boolean $advanced Whether or not allowing online class sign-up via plugin
 * @param @type boolean $show_registrants Whether or not to display class registrants in modal popup
 * @param @type boolean $registrants_count  Whether we want to show count of registrants in a class (TODO - finish) @default: 0
 * @param @type string $calendar_format Depending on final display, we may create items in Single_event class differently. 
 * @param @type boolean $delink Make class name NOT a link
 * @param @type string $class_type MBO API has 'Enrollment' and 'DropIn'. 'Enrolment' is a "workdhop". Default: 'Enrollment'
 * @param @type numeric $account Which MBO account is being interfaced with.
 *
 * @return @type array of Objects from Single_event class, in Date (and time) sequence.
*/
function sortClassesByTimeThenDay($mz_classes = array(),
																	$time_format = "g:i a", 
																	$locations=1, 
																	$hide_cancelled=0, 
																	$hide, 
																	$advanced, 
																	$show_registrants, 
																	$registrants_count, 
																	$calendar_format, 
																	$delink, 
																	$class_type = 'Enrollment', 
																	$account) {														
	$mz_classesByTime = array();

	if(!is_array($locations)):
		$locations = array($locations);
	endif;
	
	// Note: $_GET['mz_date'] is always a Monday.
	if (isset($_GET['mz_date'])):

		$end_of_range = new DateTime($_GET['mz_date'], mz_mbo_198435_get_blog_timezone());
		$end_of_range->add(new DateInterval('P1W'));
	else:
		$end_of_range = new DateTime(null, mz_mbo_198435_get_blog_timezone());
		$end_of_range->add(new DateInterval('P1W'));
	endif;
										
	foreach($mz_classes as $class)
	{
	  // Ignore classes that are not part of current week (ending Sunday)
	  if (new DateTime($class['StartDateTime'], mz_mbo_198435_get_blog_timezone()) >= $end_of_range):
			continue;
		endif;
		
		if ($hide_cancelled == 1):
			if ($class['IsCanceled'] == 1):
				continue;
			endif;
		endif;
		
		/* Create a new array with a key for each time
		and corresponsing value an array of class details 
		for classes at that time. */ 
		$classTime = date_i18n("G.i", strtotime($class['StartDateTime'])); // for numerical sorting
		// $class['day_num'] = '';
		$class['day_num'] = date_i18n("N", strtotime($class['StartDateTime'])); // Weekday num 1-7

		$single_event = new Single_event($class, 
																			$class['day_num'], 
																			$hide, 
																			$locations, 
																			$hide_cancelled, 
																			$advanced, 
																			$show_registrants, 
																			$registrants_count, 
																			$calendar_format, 
																			$delink, 
																			$account);
																			
		if(!empty($mz_classesByTime[$classTime])) {
			if (
				(!in_array($class['Location']['ID'], $locations)) || 
				($class['ClassDescription']['Program']['ScheduleType'] == 'Enrollment')
				) {
					continue;
				}
			array_push($mz_classesByTime[$classTime]['classes'], $single_event);
		} else {
			// Assign the first element ( of this time slot ?).
			if (
				(!in_array($class['Location']['ID'], $locations)) || 
				($class['ClassDescription']['Program']['ScheduleType'] == 'Enrollment')
				) {
					continue;
				}
			$display_time = (date_i18n($time_format, strtotime($class['StartDateTime']))); 
			$mz_classesByTime[$classTime] = array('display_time' => $display_time, 
													'classes' => array($single_event));
			
		}
	}

	/* Timeslot keys in new array are not time-sequenced so do so*/
	ksort($mz_classesByTime);
	foreach($mz_classesByTime as $scheduleTime => &$mz_classes)
	{
		/*
		$mz_classes is an array of all class_event objects for given time
		Take each of the class arrays and order it by days 1-7
		*/
		
		if (phpversion() >= 5.3) {
			usort($mz_classes['classes'], function($a, $b) {
				if(date_i18n("N", strtotime($a->startDateTime)) == date_i18n("N", strtotime($b->startDateTime))) {
					return 0;
					}
					return $a->startDateTime < $b->startDateTime ? -1 : 1;
				}); 
			} else {
				usort($mz_classes['classes'], 'mz_sort_grid_times'); 
			}
		$mz_classes['classes'] = week_of_timeslot($mz_classes['classes'], 'day_num');
	}
	return $mz_classesByTime;
}

/*
Make a clean array with seven corresponding slots and populate 
based on indicator (day) for each class. There may be more than
one even for each day and empty arrays will represent empty time slots.
*/
function week_of_timeslot($array, $indicator){
	$seven_days = array_combine(range(1, 7), array(array(), array(), array(),
											array(), array(), array(), array()));
		foreach($seven_days as $key => $value){
			foreach ($array as $class) {
					if ($class->$indicator == $key){
						array_push($seven_days[$key], $class);
					}
				}
			}
	return $seven_days;
	}
	
/**
 * Returns an array of two items:
 *   
 *
 * @since 1.0
 * @source (initially adapted) 
 * http://stackoverflow.com/questions/186431/calculating-days-of-week-given-a-week-number
 * Used by mz_show_schedule(), mz_show_events(), mz_mindbody_debug_text()
 * also used by mZ_mbo_pages_pages() in Mz MBO Pages plugin
 *
 * @param var $date Start date for date range.
 * @param var $duration Optional. Description. Default.
 * @return array Start Date, End Date and Previous Range Start Date.
 */	
function current_to_day_of_week_today() {
	$current = isset($_GET['mz_date']) ? new DateTime($_GET['mz_date'], mz_mbo_198435_get_blog_timezone()) : new DateTime(null, mz_mbo_198435_get_blog_timezone());
	$today = new DateTime(null, mz_mbo_198435_get_blog_timezone());
	$current_day_name = $today->format('D');
	$days_of_the_week = array(
		'Mon', 
		'Tue', 
		'Wed', 
		'Thu', 
		'Fri', 
		'Sat', 
		'Sun'
	);
	foreach($days_of_the_week as $day_name):
		if ($current_day_name != $day_name):
			$current->add(new DateInterval('P1D'));
		else:
			break;
		endif;
	endforeach;
	$clone_current = clone $current;
	return array( $current, date("Y-m-d", strtotime($clone_current->format('y-m-d'))));
}


/*
 *  Returns the blog timezone
 *
 * Gets timezone settings from the db. If a timezone identifier is used just turns
 * it into a DateTimeZone. If an offset is used, it tries to find a suitable timezone.
 * If all else fails it uses UTC.
 *
 * Source: http://wordpress.stackexchange.com/a/198453/48604
 *
 * @return DateTimeZone The blog timezone
*/
function mz_mbo_198435_get_blog_timezone() {

    $tzstring = get_option( 'timezone_string' );
    $offset   = get_option( 'gmt_offset' );

    //Manual offset...
    //@see http://us.php.net/manual/en/timezones.others.php
    //@see https://bugs.php.net/bug.php?id=45543
    //@see https://bugs.php.net/bug.php?id=45528
    //IANA timezone database that provides PHP's timezone support uses POSIX (i.e. reversed) style signs
    if( empty( $tzstring ) && 0 != $offset && floor( $offset ) == $offset ){
        $offset_st = $offset > 0 ? "-$offset" : '+'.absint( $offset );
        $tzstring  = 'Etc/GMT'.$offset_st;
    }

    //Issue with the timezone selected, set to 'UTC'
    if( empty( $tzstring ) ){
        $tzstring = 'UTC';
    }

    if( $tzstring instanceof DateTimeZone ){
        return $tzstring;
    }

    $timezone = new DateTimeZone( $tzstring );
    return $timezone; 
}
?>