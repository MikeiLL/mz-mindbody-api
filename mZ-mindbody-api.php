<?php
/**
Plugin Name: mZoo Mindbody Interface - Schedule, Events, Staff Display
Description: Interface Wordpress with MindbodyOnline data with Bootstrap Responsive Layout
Version: 1.7.5
Author: mZoo.org
Author URI: http://www.mZoo.org/
Plugin URI: http://www.mzoo.org/mz-mindbody-wp

Based on API written by Devin Crossman.
*/

//define plugin path and directory
define( 'MZ_MINDBODY_SCHEDULE_DIR', plugin_dir_path( __FILE__ ) );
define( 'MZ_MINDBODY_SCHEDULE_URL', plugin_dir_url( __FILE__ ) );

//register activation and deactivation hooks
register_activation_hook(__FILE__, 'mZ_mindbody_schedule_activation');
register_deactivation_hook(__FILE__, 'mZ_mindbody_schedule_deactivation');

load_plugin_textdomain('mz-mindbody-api',false,'mz-mindbody-schedule/languages');

function mZ_mindbody_schedule_activation() {
	//Don't know if there's anything we need to do here.
}

function mZ_mindbody_schedule_deactivation() {
	// actions to perform once on plugin deactivation go here
}

//register uninstaller
register_uninstall_hook(__FILE__, 'mZ_mindbody_schedule_uninstall');

function mZ_mindbody_schedule_uninstall(){
	//actions to perform once on plugin uninstall go here
	delete_option('mz_mindbody_options');
}

function mz_mbo_enqueue($hook) {
    if ( 'settings_page_mz-mindbody-api/mZ-mindbody-api' != $hook ) {
        return;
    }
    wp_register_style( 'mz_mbo_admin_css', plugin_dir_url( __FILE__ ) . 'css/mbo_style_admin.css', false, '1.0.0' );
        wp_enqueue_style( 'mz_mbo_admin_css' );

}
add_action( 'admin_enqueue_scripts', 'mz_mbo_enqueue' );

if (!function_exists( 'mZ_latest_jquery' )){
	function mZ_latest_jquery(){
		//	Use latest jQuery release
		if( !is_admin() ){
			wp_deregister_script('jquery');
			wp_register_script('jquery', ("http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"), false, '');
			wp_enqueue_script('jquery');
		}
	}
	add_action('wp_enqueue_scripts', 'mZ_latest_jquery');
}

//TODO Deal with conflict when $mb class get's called twice
add_action('widgets_init', 'mZ_mindbody_schedule_register_widget');

function mZ_mindbody_schedule_register_widget() {
    register_widget( 'mZ_Mindbody_day_schedule');
}

add_action( 'init', 'mZ_latest_jquery' );

if (!function_exists( 'mZ_latest_jquery' )){
	function mZ_latest_jquery(){
		//	Use latest jQuery release
		if( !is_admin() ){
			wp_deregister_script('jquery');
			wp_register_script('jquery', ("http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"), false, '');
			wp_enqueue_script('jquery');
		}
	}
	add_action('wp_enqueue_scripts', 'mZ_latest_jquery');
}

class mZ_Mindbody_day_schedule extends WP_Widget {

    function mZ_Mindbody_day_schedule() {
        $widget_ops = array(
            'classname' => 'mZ_Mindbody_day_schedule_class',
            'description' => 'Display class schedule for current day.'
            );
        $this->WP_Widget('mZ_Mindbody_day_schedule', 'Today\'s MindBody Schedule',
                            $widget_ops );
    } 
    
    function form($instance){
        $defaults = array('title' => 'Today\'s Classes');
        $instance = wp_parse_args( (array) $instance, $defaults);
        $title = $instance['title'];
        ?>
           <p>Title: <input class="widefat" name="<?php echo $this->get_field_name( 'title' ); ?>"  
           type="text" value="<?php echo esc_attr( $title ); ?>" /></p>
        <?php
    }
    
    //save the widget settings
    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = strip_tags( $new_instance['title'] );
        return $instance;
    }
    
    function widget($args, $instance){
        extract($args);
        echo $before_widget;
        $title = apply_filters( 'widget_title', $instance['title'] );
        $arguments['type'] = 'day';
        if (!empty($title) ) 
            { echo $before_title . $title . $after_title; };
            echo(mZ_mindbody_show_schedule($arguments, $account=0));
        echo $after_widget;
    }
}

//Force page protocol to match current
$protocol = isset( $_SERVER["HTTPS"]) ? 'https://' : 'http://';
	
	
//For Testing
function mZ_write_to_file($message){
        $handle = fopen("/Applications/MAMP/logs/mZ_mbo_reader.php", "a+");
        fwrite($handle, "\nMessage:\t " . $message);
        fclose($handle);
    }

//Ajax Handler
add_action('wp_ajax_nopriv_mz_mbo_add_client_ajax', 'mz_mbo_add_client_ajax');
add_action('wp_ajax_mz_mbo_add_client_ajax', 'mz_mbo_add_client_ajax');	
    
function mz_mbo_add_client_ajax() {

 	check_ajax_referer( $_REQUEST['nonce'], "mz_MBO_add_to_class_nonce", false);
 	
	require_once MZ_MINDBODY_SCHEDULE_DIR .'mindbody-php-api/MB_API.php';
	require_once MZ_MINDBODY_SCHEDULE_DIR .'inc/mz_mbo_init.inc';

	$additions['ClassIDs'] = array($_REQUEST['classID']);
	$additions['ClientIDs'] = array($_REQUEST['clientID']);
	//$additions['Test'] = true;
	$additions['SendEmail'] = true;
	$signupData = $mb->AddClientsToClasses($additions);
	//$mb->debug();
    //$rand_number = rand(1, 10); # for testing

	if ( $signupData['AddClientsToClassesResult']['ErrorCode'] != 200 ){
			$result['type'] = "failure";
			$result['message'] = '';
		foreach ($signupData['AddClientsToClassesResult']['Classes']['Class']['Clients']['Client']['Messages'] as $message){
				if (strpos($message, 'already booked') != false){
					$result['message'] .= "You are already registered.";
					}else{
					$result['message'] .= $message;
					}
			}
			
		}else{
			//$classDetails = $signupData['AddClientsToClassesResult']['Classes']['Class'];
			
			$result['type'] = "success";
			$result['message'] = "Registered via MindBody";
			/*$classDetails['ClassDescription']['Name']
			$classDetails['Staff']['Name'];
			$classDetails['Location']['Name'];
			$classDetails['Location']['Address'];*/
		}
		
	if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
      $result = json_encode($result);
      echo $result;
   }
   else {
      header("Location: ".$_SERVER["HTTP_REFERER"]);
   }

   die();
}
//End Ajax
    
if ( is_admin() )
{ // admin actions
	add_action ('admin_menu', 'mz_mindbody_settings_menu');

	function mz_mindbody_settings_menu() {
		//create submenu under Settings
		add_options_page ('MZ Mindbody Settings','MZ Mindbody',
		'manage_options', __FILE__, 'mz_mindbody_settings_page');
	}

	function mz_mindbody_settings_page() {
		?>
		<div class="wrap">
			<?php screen_icon(); ?>
			<form action="options.php" method="post">
				<?php settings_fields('mz_mindbody_options'); ?>
				<?php do_settings_sections('mz_mindbody'); ?>
				<input name="Submit" type="submit" class="button button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
			</form>
		</div>
		<?php
	}

	// Register and define the settings
	add_action('admin_init', 'mz_mindbody_admin_init');

	function mz_mindbody_admin_init(){
		register_setting(
			'mz_mindbody_options',
			'mz_mindbody_options',
			'mz_mindbody_validate_options'
		);

		add_settings_section(
			'mz_mindbody_server',
			'MZ Mindbody Server',
			'mz_mindbody_server_check',
			'mz_mindbody'
		);
		
		add_settings_section(
			'mz_mindbody_section2_text',
			'',
			'mz_mindbody_section2_text',
			'mz_mindbody'
		);
		
		add_settings_section(
			'mz_mindbody_section4_text',
			'',
			'mz_mindbody_section4_text',
			'mz_mindbody'
		);
		
		add_settings_section(
			'mz_mindbody_main',
			'MZ Mindbody Credentials',
			'mz_mindbody_section_text',
			'mz_mindbody'
		);

		
		add_settings_field(
			'mz_mindbody_source_name',
			'Source Name: ',
			'mz_mindbody_source_name',
			'mz_mindbody',
			'mz_mindbody_main'
		);

		add_settings_field(
			'mz_mindbody_password',
			'Key: ',
			'mz_mindbody_password',
			'mz_mindbody',
			'mz_mindbody_main'
		);

		add_settings_field(
			'mz_mindbody_siteID',
			'Site ID: ',
			'mz_mindbody_siteID',
			'mz_mindbody',
			'mz_mindbody_main'
		);

		add_settings_field(
			'mz_mindbody_eventID',
			'Event IDs: ',
			'mz_mindbody_eventID',
			'mz_mindbody',
			'mz_mindbody_main'
		);
		
		add_settings_section(
			'mz_mindbody_section3_text',
			'',
			'mz_mindbody_section3_text',
			'mz_mindbody'
		);

		add_settings_field(
			'mz_mindbody_clear_cache',
			'Force Cache Reset ',
			'mz_mindbody_clear_cache',
			'mz_mindbody',
			'mz_mindbody_main'
		);

		add_settings_section(
			'mz_mindbody_secondary',
			'Debug',
			'mz_mindbody_debug_text',
			'mz_mindbody'
		);
	}

	// Draw the section header
	function mz_mindbody_server_check() {
		$mz_requirements = 0;
		require_once 'System.php';

		if (extension_loaded('soap'))
		{
			_e( 'SOAP installed! ');
		}
		else
		{
		   _e('SOAP is not installed. ');
		   $mz_requirements = 1;
		}

		if (class_exists('System')===true)
		{
		   _e('PEAR installed! ');
		}
		else
		{
		   _e('PEAR is not installed. ');
		   $mz_requirements = 1;
		}

		if ($mz_requirements == 1)
		{
			_e('<div class="settings-error" style="max-width:60%"><p>MZ Mindbody API requires SOAP and PEAR. Please contact your hosting provider or enable via your CPANEL of php.ini file.</p></div>');
		}
		else
		{
			_e('<div class="updated" style="max-width:60%">Congratulations. Your server appears to be configured to integrate with mindbodyonline.</div>');
		}
	}

	function mz_mindbody_section_text() { ?>
		<div style="max-width:60%">
		<p><?php _e('Enter your mindbody credentials below.') ?></p>
		<p><?php _e('If you do not have them yet, visit the') ?> <a href="https://api.mindbodyonline.com/Home/LogIn"><?php _e('MindBodyOnline developers website') ?></a> <?php _e('and register for developer credentials.')?>
		(<a href="http://www.mzoo.org/creating-your-mindbody-credentials/"><?php _e('Detailed instructions here') ?></a>.)</p>
		<p><?php _e('Add to page or post with shortcode')?>: [mz-mindbody-show-schedule], [mz-mindbody-show-events], [mz-mindbody-staff-list], [mz-mindbody-show-schedule type=day location=1] </p>
		<p> Parameter 'account' can be added to any of the above shortcodes like:  [shortcode account=-99] to call from a different MBO business account. 
		(-99 is the MBO <em>sandbox</em> account)</font></p>
		</div>
	<?php
	
	}

	function mz_mindbody_section2_text() {
	?><div style="float:right;width:150px;background:#CCCCFF;padding:5px 20px 20px 20px;margin-left:20px;margin-bottom:8px;"><h4><?php _e('Contact')?></h4>
	<p><a href="http://www.mzoo.org">www.mzoo.org</a></p>
	<p><a href="mailto:mike@mzoo.org"><div class="dashicons dashicons-email-alt" alt="f466"></a></div> <a href="mailto:mike@mzoo.org">emails welcome</a>.</p>
	</div>
	<br style='clear:right;'/>
	<?php
	}
	
	function mz_mindbody_section4_text() {
	?><div style="float:right;width:150px;background:#CCCCFF;padding:5px 20px 20px 20px;margin-left:20px;">
	<h4><i class="dashicons dashicons-megaphone" alt="f488" style="max-width:90%"></i> News</h4>
	<p>Now supports multiple locations <em>and</em> MBO accounts.<p>
	<hr/>
	<h4>Advanced Version. Customization requests welcome.</h4>
	</div>
	<?php
	}

	//require_once MZ_MINDBODY_SCHEDULE_DIR .'inc/mz_mbo_init.inc';
	add_action( 'wp_footer', 'mz_mindbody_debug_text' );
	function mz_mindbody_debug_text() {
	  require_once MZ_MINDBODY_SCHEDULE_DIR .'mindbody-php-api/MB_API.php';
	  require_once MZ_MINDBODY_SCHEDULE_DIR .'inc/mz_mbo_init.inc';
	  echo "<p>Once credentials have been set and activated, look for <code>&lt;ErrorCode&gt;200&lt;/ErrorCode&gt;</code> in the GetClassesResponse box below to confirm settings are correct.</p>";
	  $mz_timeframe = array_slice(mz_getDateRange(date_i18n('Y-m-d'), 1), 0, 1);
	  $test = $mb->GetClasses($mz_timeframe);
	  echo "<p>Once credentials have been set and activated, look for <code>&lt;ErrorCode&gt;200&lt;/ErrorCode&gt;</code> in the GetClassesResponse box below to confirm settings are correct.</p>";
	  $mb->debug();
	  echo "<br/>";
	}

	// Display and fill the form field
	function mz_mindbody_source_name() {
		// get option 'mz_source_name' value from the database
		$options = get_option( 'mz_mindbody_options',__('Option Not Set') );
		$mz_source_name = (isset($options['mz_source_name'])) ? $options['mz_source_name'] : _e('YOUR SOURCE NAME');
		// echo the field
		echo "<input id='mz_source_name' name='mz_mindbody_options[mz_source_name]' type='text' value='$mz_source_name' />";
	}

	// Display and fill the form field
	function mz_mindbody_password() {
		$options = get_option( 'mz_mindbody_options',__('Option Not Set') );
		$mz_mindbody_password = (isset($options['mz_mindbody_password'])) ? $options['mz_mindbody_password'] : _e('YOUR MINDBODY PASSWORD');
		// echo the field
		echo "<input id='mz_mindbody_password' name='mz_mindbody_options[mz_mindbody_password]' type='text' value='$mz_mindbody_password' />";
	}

	// Display and fill the form field
	function mz_mindbody_siteID() {
		// get option 'text_string' value from the database
		$options = get_option( 'mz_mindbody_options',__('Option Not Set') );
		$mz_mindbody_siteID = (isset($options['mz_mindbody_siteID'])) ? $options['mz_mindbody_siteID'] : _e('YOUR SITE ID');
		// echo the field
		echo "<input id='mz_mindbody_siteID' name='mz_mindbody_options[mz_mindbody_siteID]' type='text' value='$mz_mindbody_siteID' />";
	}

	// Display and fill the form field
	function mz_mindbody_eventID() {
		// get option 'text_string' value from the database
		$options = get_option( 'mz_mindbody_options',__('Option Not Set') );
		$mz_mindbody_eventID = (isset($options['mz_mindbody_eventID'])) ? $options['mz_mindbody_eventID'] : _e('Event Category IDs');
		// echo the field
		echo "<input id='mz_mindbody_eventID' name='mz_mindbody_options[mz_mindbody_eventID]' type='text' value='$mz_mindbody_eventID' />  eg: 25,17";
	}

	function mz_mindbody_section3_text() {
		echo "Having this checked will allow you to see immediate changes in MBO, ";
		echo "<br/>";
		echo "but may end up costing more in API transfer fees.";
		echo "<br/>";
		echo "Class calendar cache is held for 1 day. Event calendar for 1 hour.";
		}
		
	// Display and fill the cache reset form field
	function mz_mindbody_clear_cache() {
		$options = get_option( 'mz_mindbody_options','Option Not Set' );
		printf(
	    '<input id="%1$s" name="mz_mindbody_options[%1$s]" type="checkbox" %2$s />',
	    'mz_mindbody_clear_cache',
	    checked( isset($options['mz_mindbody_clear_cache']) , true, false )
		);
	}

	// Validate user input (we want text only)
	function mz_mindbody_validate_options( $input ) {
	    foreach ($input as $key => $value)
	    {
				$valid[$key] = wp_strip_all_tags(preg_replace( '/\s/', '', $input[$key] ));
				if( $valid[$key] != $input[$key] )
				{
					add_settings_error(
						'mz_mindbody_text_string',
						'mz_mindbody_texterror',
						'Does not appear to be valid ',
						'error'
					);
				}
			}

		return $valid;
	}

}
else
{// non-admin enqueues, actions, and filters

    add_action('init', 'myStartSession', 1);
    add_action('wp_logout', 'myEndSession');
    add_action('wp_login', 'myEndSession');

    function myStartSession() {
    	if ((function_exists('session_status') && session_status() !== PHP_SESSION_ACTIVE) || !session_id()) {
			  session_start();
			}
    }

    function myEndSession() {
        session_destroy ();
    }
	
  
	function load_jquery() {
		wp_enqueue_script( 'jquery' );
	}
	add_action( 'wp_enqueue_script', 'load_jquery' );
	
	include_once(dirname( __FILE__ ) . '/mindbody-php-api/MB_API.php');

	foreach ( glob( plugin_dir_path( __FILE__ )."inc/*.php" ) as $file )
        include_once $file;

	add_shortcode('mz-mindbody-show-schedule', 'mZ_mindbody_show_schedule' );
	add_shortcode('mz-mindbody-show-events', 'mZ_mindbody_show_events' );
	add_shortcode('mz-mindbody-staff-list', 'mZ_mindbody_staff_listing' );
	add_shortcode('mz-mindbody-login', 'mZ_mindbody_login' );
	add_shortcode('mz-mindbody-logout', 'mZ_mindbody_logout' );
	add_shortcode('mz-mindbody-activation', 'mZ_mindbody_activation' );
	add_shortcode('mz-mindbody-add-to-classes', 'mz_mindbody_add_to_classes' );
}//EOF Not Admin

if (phpversion() >= 5.3) {
    include_once('php_variants/sort_newer.php');
    }else{
    include_once('php_variants/sort_older.php');
    }

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
    $wkday = date('l',mktime('0','0','0', $month, $day, $year));

    switch($wkday) {
        case 'Monday': $numDaysFromMon = 0; break;
        case 'Tuesday': $numDaysFromMon = 1; break;
        case 'Wednesday': $numDaysFromMon = 2; break;
        case 'Thursday': $numDaysFromMon = 3; break;
        case 'Friday': $numDaysFromMon = 4; break;
        case 'Saturday': $numDaysFromMon = 5; break;
        case 'Sunday': $numDaysFromMon = 6; break;   
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

function mz_mbo_schedule_nav($date, $period="Week", $duration=7)
{
	$sched_nav = '';
	$mz_schedule_page = get_permalink();
	//Navigate through the weeks
	$mz_start_end_date = mz_getDateRange($date, $duration);
	$mz_nav_weeks_text_prev = __('Previous')." ".$period;
	$mz_nav_weeks_text_current = __('Current')." ".$period;
	$mz_nav_weeks_text_following = __('Following')." ".$period;
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


//Format arrays for display in development
function mz_pr($data)
{
  echo "<pre>";
  print_r($data);
  echo "</pre>";
}
?>
