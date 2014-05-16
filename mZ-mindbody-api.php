<?php
/**
Plugin Name: mZoo Mindbody Interface - Schedule, Events, Staff Display
Description: Interface Wordpress with MindbodyOnline data with Bootstrap Responsive Layout
Version: 1.0
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

load_plugin_textdomain('mz-mindbody-api',false,'mz-mindbody-scudule/languages');

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

if ( is_admin() ){ // admin actions
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
			<input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
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

		add_settings_section(
		'mz_mindbody_secondary',
		'MZ Mindbody Contact',
		'mz_mindbody_section2_text',
		'mz_mindbody'
	);
<<<<<<< HEAD

=======
	
			add_settings_field(
		'mz_mindbody_clear_cache',
		'Force Cache Reset ',
		'mz_mindbody_clear_cache',
		'mz_mindbody',
		'mz_mindbody_main'
	);
	
>>>>>>> master
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
	if (extension_loaded('soap')) {
	 _e( 'SOAP installed! ');
	} else {
	   _e('SOAP is not installed. ');
	   $mz_requirements = 1;
	}
	require_once 'System.php';
	if(class_exists('System')===true) {
	   _e('PEAR installed! ');
	} else {
	   _e('PEAR is not installed. ');
	   $mz_requirements = 1;
	}
	if ($mz_requirements == 1)
	echo "<p>MZ Mindbody API requires SOAP and PEAR. Please contact your hosting provider or enable via your CPANEL of php.ini file.</p>";
	else
	_e('Congratulations. Your server appears to be configured to integrate with mindbodyonline.');
}

function mz_mindbody_section_text() {
?><p><?php _e('Enter your mindbody credentials below.') ?></p>
<p><?php _e('If you do not have them yet, visit the') ?> <a href="https://api.mindbodyonline.com/Home/LogIn"><?php _e('MindBodyOnline developers website') ?></a> <?php _e('and register for developer credentials.')?></p>
<p><?php _e('Add to page or post with shortcode')?>: [mz-mindbody-show-schedule], [mz-mindbody-show-events], [mz-mindbody-staff-list], [mz-mindbody-login], [mz-mindbody-logout], [mz-mindbody-signup]</p>
<?php
}

function mz_mindbody_section2_text() {
?><p><?php _e('Contact')?>: <a href="http://www.mzoo.org"> www.mzoo.org</a></p>
<?php
}

//require_once MZ_MINDBODY_SCHEDULE_DIR .'inc/mz_mbo_init.inc';
add_action( 'admin_init', 'mz_mindbody_debug_text' );
function mz_mindbody_debug_text() {
//$mb->debug();
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
	$valid[$key] = wp_strip_all_tags(preg_replace( '/\s+/', '', $input[$key] ));
	if( $valid[$key] != $input[$key] ) {
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

} else {// non-admin enqueues, actions, and filters

  add_action( 'wp_enqueue_script', 'load_jquery' );
	function load_jquery() {
			wp_enqueue_script( 'jquery' );
		}

	function mZ_mindbody_schedule_init() {
		wp_register_style('mZ_mindbody_schedule_bs', plugins_url('/bootstrap/css/bootstrap.min.css',__FILE__ ));
		wp_enqueue_style('mZ_mindbody_schedule_bs');
		}
	add_action( 'init','mZ_mindbody_schedule_init');


	add_action('init', 'enqueue_mz_mbo_scripts');
	function enqueue_mz_mbo_scripts() {
		wp_register_script( 'mz_mbo_bootstrap_script', plugins_url('/bootstrap/js/bootstrap.min.js', __FILE__), array( 'jquery' ),'3.1.1', true );
		wp_enqueue_script( 'mz_mbo_bootstrap_script' );
		wp_register_script( 'mz_mbo_modal_script', plugins_url('/js/mz_mbo_modal.js', __FILE__), array( 'jquery' ),'1', true );
		wp_enqueue_script( 'mz_mbo_modal_script' );
		}

	include_once(dirname( __FILE__ ) . '/mindbody-api/MB_API.php');

	foreach ( glob( plugin_dir_path( __FILE__ )."inc/*.php" ) as $file )
	    include_once $file;

	add_shortcode('mz-mindbody-show-schedule', 'mZ_mindbody_show_schedule' );
	add_shortcode('mz-mindbody-show-events', 'mZ_mindbody_show_events' );
	add_shortcode('mz-mindbody-staff-list', 'mZ_mindbody_staff_listing' );
	add_shortcode('mz-mindbody-login', 'mZ_mindbody_login' );
	add_shortcode('mz-mindbody-logout', 'mZ_mindbody_logout' );
	add_shortcode('mz-mindbody-signup', 'mZ_mindbody_signup' );

	}//EOF Not Admin

function sortClassesByDate($mz_classes = array()) {
	$mz_classesByDate = array();
	foreach($mz_classes as $class) {
		$classDate = date("Y-m-d", strtotime($class['StartDateTime']));
		if(!empty($mz_classesByDate[$classDate])) {
			$mz_classesByDate[$classDate] = array_merge($mz_classesByDate[$classDate], array($class));
		} else {
			$mz_classesByDate[$classDate] = array($class);
		}
	}
	ksort($mz_classesByDate);
	foreach($mz_classesByDate as $classDate => &$mz_classes) {
		usort($mz_classes, function($a, $b) {
			if(strtotime($a['StartDateTime']) == strtotime($b['StartDateTime'])) {
				return 0;
			}
			return $a['StartDateTime'] < $b['StartDateTime'] ? -1 : 1;
		});
	}
	return $mz_classesByDate;
}


function getStartAndEndDate($week, $year) {
  // Adding leading zeros for weeks 1 - 9.
  $date_string = $year . 'W' . sprintf('%02d', $week);
  $return[0] = date('Y-m-d', strtotime($date_string));//not date('Y-n-j
  $return[1] = date('Y-m-d', strtotime($date_string . '7'));
  return $return;
}

//May need this for week iteration:
/*  w e e k n u m b e r  -------------------------------------- //
weeknumber returns a week number from a given date (>1970, <2030)
Wed, 2003-01-01 is in week 1
Mon, 2003-01-06 is in week 2
Wed, 2003-12-31 is in week 53, next years first week
Be careful, there are years with 53 weeks.
// ------------------------------------------------------------ */

function weeknumber ($y, $m, $d) {
    $wn = strftime("%W",mktime(0,0,0,$m,$d,$y));
    $wn += 0; # wn might be a string value
    $firstdayofyear = getdate(mktime(0,0,0,1,1,$y));
    if ($firstdayofyear["wday"] != 1)    # if 1/1 is not a Monday, add 1
        $wn += 1;
    return ($wn);
}    //EOF function weeknumber
	function mz_validate_weeknum( $string )
		{
			if (preg_match('/^[0-9][0-9]?$|^53$/',$string));
			return $string;
		}
	function mz_validate_year( $string )
		{
		 	if (preg_match('/^\d{4}$/',$string));
		 	return $string;
		}

		//Format arrays for display in development
			function pr($data)
			{
			    echo "<pre>";
			    print_r($data);
			    echo "</pre>";
			}
?>
