<?php
use mZoo\MBOAPI;
/**
 * This file contains main plugin class and, defines and plugin loader.
 *
 * The mZoo Mindbody Interface plugin utilizes the Devin Crossman Mindbody API
 * to interface with mindbody's SOAP API. This particular file is responsible for
 * including the necessary dependencies and starting the plugin.
 *
 * @package MZAPI
 *
 * @wordpress-plugin
 * Plugin Name: 	mZoo Mindbody Interface - Schedule, Events, Staff Display
 * Description: 	Interface Wordpress with MindbodyOnline data with Bootstrap Responsive Layout.
 * Version: 		2.4.6
 * Author: 			mZoo.org
 * Author URI: 		http://www.mZoo.org/
 * Plugin URI: 		http://www.mzoo.org/mz-mindbody-wp
 * License:         GPL-2.0+
 * License URI:     http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: 	mz-mindbody-api
 * Domain Path: 	/languages
*/

if ( !defined( 'WPINC' ) ) {
    die;
}

require __DIR__ . "/src/Autoload.php";
spl_autoload_register( [ new Loader\Autoload( 'mZoo', __DIR__ . '/src/' ), 'load' ] );

//define plugin path and directory
define( 'MZ_MINDBODY_SCHEDULE_DIR', plugin_dir_path( __FILE__ ) );
define( 'MZ_MINDBODY_SCHEDULE_URL', plugin_dir_url( __FILE__ ) );

//register activation and deactivation hooks
register_activation_hook(__FILE__, 'mZ_mindbody_schedule_activation');
register_deactivation_hook(__FILE__, 'mZ_mindbody_schedule_deactivation');


/**
 * The MZ Mindbody API Admin defines all the functionality for the dashboard
 * of the plugin.
 *
 * This class defines version and loads the actions and functions
 * that create the dashboard.
 *
 * @since    2.1.0
 */
class MZ_Mindbody_API_Admin {
    
    protected $version;
 
    public function __construct( $version ) {
        $this->version = $version;
        $this->load_sections();
        }
        
    public function load_sections() {
        require_once MZ_MINDBODY_SCHEDULE_DIR .'lib/sections.php';
        require_once MZ_MINDBODY_SCHEDULE_DIR .'lib/class.pluginController.php';
        }
}

/**
 * The MZ Mindbody API Loader class is responsible
 * coordinating most of the actions and filters used in the plugin.
 *
 * This class maintains two internal collections - one for actions, one for
 * hooks - each of which are coordinated through external classes that
 * register the various hooks through this class. Note that the actions
 * specific to the admin sections are loaded in /lib/sections.php
 *
 * @since    2.1.0
 */
class MZ_Mindbody_API_Loader {
    /**
     * A reference to the collection of actions used throughout the plugin.
     *
     * @access protected
     * @var    array    $actions    The array of actions that are defined throughout the plugin.
     */
    protected $actions;
 
    /**
     * A reference to the collection of filters used throughout the plugin.
     *
     * @access protected
     * @var    array    $actions    The array of filters that are defined throughout the plugin.
     */
    protected $filters;
 
    /**
     * Instantiates the plugin by setting up the data structures that will
     * be used to maintain the actions and the filters.
     */
    public function __construct() {
 
        $this->actions = array();
        $this->filters = array();
 
    }
    
    /**
     * Registers the actions with WordPress and the respective objects and
     * their methods.
     *
     * @param  string    $hook        The name of the WordPress hook to which we're registering a callback.
     * @param  object    $component   The object that contains the method to be called when the hook is fired.
     * @param  string    $callback    The function that resides on the specified component.
     */ 
    public function add_action( $hook, $component, $callback ) {
        $this->actions = $this->add( $this->actions, $hook, $component, $callback );
    }

    /**
     * Registers the filters with WordPress and the respective objects and
     * their methods.
     *
     * @param  string    $hook        The name of the WordPress hook to which we're registering a callback.
     * @param  object    $component   The object that contains the method to be called when the hook is fired.
     * @param  string    $callback    The function that resides on the specified component.
     */ 
    public function add_filter( $hook, $component, $callback ) {
        $this->filters = $this->add( $this->filters, $hook, $component, $callback );
    }
    
    /**
     * Registers the filters with WordPress and the respective objects and
     * their methods.
     *
     * @access private
     *
     * @param  array     $hooks       The collection of existing hooks to add to the collection of hooks.
     * @param  string    $hook        The name of the WordPress hook to which we're registering a callback.
     * @param  object    $component   The object that contains the method to be called when the hook is fired.
     * @param  string    $callback    The function that resides on the specified component.
     *
     * @return array                  The collection of hooks that are registered with WordPress via this class.
     */ 
    private function add( $hooks, $hook, $component, $callback ) {
 
        $hooks[] = array(
            'hook'      => $hook,
            'component' => $component,
            'callback'  => $callback
        );
 
        return $hooks;
 
    }
 
     /**
     * Calls the add methods for above referenced filters and actions and registers them with WordPress.
     */
    public function run() {
 		
        foreach ( $this->filters as $hook ) {
            add_filter( $hook['hook'], array( $hook['component'], $hook['callback'] ) );
        }
 
        foreach ( $this->actions as $hook ) {
        	if (($hook['callback'] == 'mZoo\MBOAPI\instantiate_mbo_API') && ($hook['component'] == 'mZoo\MBOAPI\MZ_Mindbody_Init')) {
        		add_action( $hook['hook'], array( $hook['component'], $hook['callback'] ) );
        	}else{
            	add_action( $hook['hook'], array( $hook['component'], $hook['callback'] ) );
            }
        }
 
    }
 
}


class MZ_Mindbody_API {
 
    protected $loader;
 
    protected $plugin_slug;
 
    protected $version;
 
    public function __construct() {
 
        $this->plugin_slug = 'mz-mindbody-api';
        $this->version = '2.4.6';
        $this->load_dependencies();
        $this->define_main_hooks();
        $this->add_shortcodes();
 
    }
 
    private function load_dependencies() {
    

		foreach ( glob( plugin_dir_path( __FILE__ )."inc/*.php" ) as $file )
			include_once $file;
			        	
        $this->loader = new MZ_Mindbody_API_Loader();
    }
 
    private function define_admin_hooks() {
 
        $admin = new MZ_Mindbody_API_Admin( $this->get_version() );
        $this->loader->add_action( 'admin_enqueue_scripts', $admin, 'enqueue_styles' );
        $this->loader->add_action( 'add_meta_boxes', $admin, 'add_meta_box' );
    }
    
    private function define_main_hooks() {
        // Add Session actions for user login and logout MBO 
        //TODO Avoid Sessions Entirely if possible
        // see: https://pressjitsu.com/blog/wordpress-sessions-performance/
        add_action( 'template_redirect', $this, 'StartSession', 1 );
        add_action( 'wp_logout', $this, 'StartSession' );
        add_action( 'wp_login', $this, 'EndSession' );
        }
        
  	public function StartSession() {
			if ((function_exists('session_status') && session_status() !== PHP_SESSION_ACTIVE) || !session_id()) {
				  session_start();
				}
		}

		public function EndSession() {
		/* Following line to deal with Warning: session_destroy(): Trying to destroy uninitialized session
		when auto-logged out of WP admin session. */
    	if(!isset($_SESSION)) 
    		{ 
        	session_destroy(); 
    		} 
			
		}
		
 	private function add_shortcodes() {
 	
 		$mz_staff = new MBOAPI\MZ_Staff();
 		$mz_events = new MBOAPI\MZ_Events();
 		$mz_clients = new MBOAPI\MZ_Clients();
 		$get_schedule = new MBOAPI\MZ_Get_Schedule();
 		
		add_shortcode('mz-mindbody-show-schedule', array(new MBOAPI\Schedule_Display(), 'mZ_mindbody_show_schedule'));
		add_shortcode('mz-mindbody-staff-list', array($mz_staff, 'mZ_mindbody_staff_listing'));
		add_shortcode('mz-mindbody-show-events', array($mz_events, 'mZ_mindbody_show_events'));
		add_shortcode('mz-mindbody-login', array($mz_clients, 'mZ_mindbody_login'));
		add_shortcode('mz-mindbody-signup', array($mz_clients, 'mZ_mindbody_signup'));
		add_shortcode('mz-mindbody-logout', array($mz_clients, 'mZ_mindbody_logout'));
		add_shortcode('mz-mindbody-get-schedule', array($get_schedule, 'MZ_Get_Schedule'));

    }
 
    public function run() {
        $this->loader->run();
    }
 
    public function get_version() {
        return $this->version;
    }
 
}

function mZ_MBO_load_plugin_textdomain() {
	load_plugin_textdomain('mz-mindbody-api',false,dirname(plugin_basename(__FILE__)) . '/languages');
	}
add_action( 'plugins_loaded', 'mZ_MBO_load_plugin_textdomain' );

function mZ_mindbody_schedule_activation() {
	// Nothing to see here, folks.
}

function mZ_mindbody_schedule_deactivation() {
	wp_clear_scheduled_hook('make_pages_weekly');
}

//register uninstaller
register_uninstall_hook(__FILE__, 'mZ_mindbody_schedule_uninstall');

function mZ_mindbody_schedule_uninstall(){
	wp_clear_scheduled_hook('make_pages_weekly');
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

//TODO Deal with conflict when $mb class get's called twice
add_action('widgets_init', 'mZ_mindbody_schedule_register_widget');

function mZ_mindbody_schedule_register_widget() {
    register_widget( 'mZ_Mindbody_day_schedule');
}


class mZ_Mindbody_day_schedule extends WP_Widget {

    function __construct() {
        $widget_ops = array(
            'classname' => 'mZ_Mindbody_day_schedule_class',
            'description' => __('Display class schedule for current day.', 'mz-mindbody-api')
            );
        parent::__construct('mZ_Mindbody_day_schedule', __('Today\'s MindBody Schedule', 'mz-mindbody-api'),
                            $widget_ops );
    } 
    
    function form($instance){
        $defaults = array('title' => __('Today\'s Classes', 'mz-mindbody-api'));
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
        $arguments[__('type', 'mz-mindbody-api')] = __('day', 'mz-mindbody-api');
        if (!empty($title) ) 
            { echo $before_title . $title . $after_title; };
            echo(mZ_mindbody_show_schedule($arguments, $account=0));
        echo $after_widget;
    }
}


    
if ( is_admin() )
{     
	$admin_backend = new MZ_Mindbody_API_Admin('2.1.0');
	//Start Ajax Signup
	 //(Ajax Handler has to be within admin section)
 add_action('wp_ajax_nopriv_mz_mbo_add_client', 'mz_mbo_add_client_callback');
 add_action('wp_ajax_mz_mbo_add_client', 'mz_mbo_add_client_callback');	

 function mz_mbo_add_client_callback() {

  check_ajax_referer( $_REQUEST['nonce'], "mz_MBO_add_to_class_nonce", false);
  	
 	require_once(MZ_MINDBODY_SCHEDULE_DIR .'mindbody-php-api/MB_API.php');
	require_once(MZ_MINDBODY_SCHEDULE_DIR .'inc/mz_mbo_init.inc');
	$mb = MZ_Mindbody_Init::instantiate_mbo_API();
 
 	$additions['ClassIDs'] = array($_REQUEST['classID']);
 	$additions['ClientIDs'] = array($_REQUEST['clientID']);
 	//$additions['Test'] = true;
 	$additions['SendEmail'] = true;
 	$signupData = $mb->AddClientsToClasses($additions);
 	//$mb->debug();
     //$rand_number = rand(1, 10); # for testing
 
 	if ( $signupData['AddClientsToClassesResult']['ErrorCode'] != 200 ) {
 			$result['type'] = "error";
 			$result['message'] = '';
 			
 		if (!isset($signupData['AddClientsToClassesResult']['Classes']['Class']['Clients']['Client'])) :
 			
 			if (function_exists(mZ_write_to_file)) {
 				//mZ_write_to_file($signupData['AddClientsToClassesResult']['ErrorCode']);
 				}
 			$result['type'] = "error";
 			$result['message'] = __('Cannot add to class.', 'mz-mindbody-api');
 			
 		else:
 			
			foreach ($signupData['AddClientsToClassesResult']['Classes']['Class']['Clients']['Client']['Messages'] as $message){
					if (strpos($message, 'already booked') != false){
						$result['message'] .= __('Already registered.', 'mz-mindbody-api');
						}else{
						$result['message'] .= $message;
						}
				}
				
		endif;
			
 		}else{ 			
 			$result['type'] = "success";
 			$result['message'] = __('Registered via MindBody', 'mz-mindbody-api');
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
 //End Ajax Signup
 
 	//Start Ajax Check Signed In
 add_action('wp_ajax_nopriv_mz_mbo_check_session_logged', 'mz_mbo_check_session_logged_callback');
 add_action('wp_ajax_mz_mbo_check_session_logged', 'mz_mbo_check_session_logged_callback');	

 function mz_mbo_check_session_logged_callback() {

  //check_ajax_referer( $_REQUEST['nonce'], "mz_MBO_add_to_class_nonce", false);
 	if ((function_exists('session_status') && session_status() !== PHP_SESSION_ACTIVE) || !session_id()) {
				  session_start();
				}
	if (function_exists(mZ_write_to_file)) {
	// This function is contained in 
  	//mZ_write_to_file(array($_REQUEST, $_SESSION));
  }
 	if (isset($_SESSION['GUID'])) {
  	$result['logged_in'] = 1; // 'user_logged_in'
  	} else {
  	$result['logged_in'] = 0; // 'user_not_logged_in'
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
 //End Ajax Check Signed In
 
//Start Ajax Reset Main Schedule
 add_action('wp_ajax_nopriv_mz_mbo_reset_staff', 'mz_mbo_reset_staff_callback');
 add_action('wp_ajax_mz_mbo_reset_staff', 'mz_mbo_reset_staff_callback');	

function mz_mbo_reset_staff_callback() {
	
	require_once( MZ_MINDBODY_SCHEDULE_DIR .'inc/get_schedule.php' );
  $classes_pages = new MZ_Get_Schedule();
  $php_result = $classes_pages->MZ_Get_Schedule('message');
  if (function_exists(mZ_write_to_file)) {
	// This function is contained in 
		//mZ_write_to_file($result);
	}
  if(is_array($php_result)):
  	$result['message'] = array_shift($result);
  	$result['mbo_status'] =  array_shift($result);
  	$result['mbo_result'] = array_shift($result);
  	$result['type'] = "error";
  else:
  	$result['message'] = $php_result;
  	$result['type'] = "success";
  endif;
  


 	if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
		 $result = json_encode($result);
		 echo $result;
	}
	else {
		 header("Location: ".$_SERVER["HTTP_REFERER"]);
	}

	die();
 }
 //End Ajax Reset Main Schedule

 require_once('lib/functions.php'); // for testing functions
 //Start Ajax Get Registrants
 add_action('wp_ajax_nopriv_mz_mbo_get_registrants', 'mz_mbo_get_registrants_callback');
 add_action('wp_ajax_mz_mbo_get_registrants', 'mz_mbo_get_registrants_callback');	

 function mz_mbo_get_registrants_callback() {

  check_ajax_referer( $_REQUEST['nonce'], "mz_MBO_get_registrants_nonce", false);
  	
 	require_once(MZ_MINDBODY_SCHEDULE_DIR .'mindbody-php-api/MB_API.php');
	require_once(MZ_MINDBODY_SCHEDULE_DIR .'inc/mz_mbo_init.inc');
	
	$mb = MZ_Mindbody_Init::instantiate_mbo_API();
 
 	$classid = $_REQUEST['classID'];
 	$result['type'] = "success";
 	$result['message'] = $classid;
 	$class_visits = $mb->GetClassVisits(array('ClassID'=> $classid));
		if ($class_visits['GetClassVisitsResult']['Status'] != 'Success'):
				$result['type'] = "error";
 				$result['message'] = __("Unable to retrieve registrants.", 'mz-mindbody-api');
 		else:
				if (empty($class_visits['GetClassVisitsResult']['Class']['Visits'])) :
					$result['type'] = "success";
 					$result['message'] = __("No registrants yet.", 'mz-mindbody-api');
 					//mZ_write_to_file($class_visits['GetClassVisitsResult']['Class']['Visits']);
				else:
					$result['message'] = array();
					$result['type'] = "success";
					foreach($class_visits['GetClassVisitsResult']['Class']['Visits'] as $registrants) {
						if (!isset($registrants['Client']['FirstName'])):
							foreach ($registrants as $key => $registrant) {
									if (isset($registrant['Client'])): 
									$result['message'][] = $registrant['Client']['FirstName'] . '_' 
																					. substr($registrant['Client']['LastName'], 0, 1);
									endif;
								}
						else: 
								$result['message'][] = $registrants['Client']['FirstName'] . '_' 
																					. substr($registrants['Client']['LastName'], 0, 1);
						endif;
					}
				endif;
		endif;		
 		
 	if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
       $result = json_encode($result);
       echo $result;
    }
    else {
       header("Location: ".$_SERVER["HTTP_REFERER"]);
    }
 
    die();
 }
 //End Ajax Get Registrants
 
 //Start Ajax Get Staff
 add_action('wp_ajax_nopriv_mz_mbo_get_staff', 'mz_mbo_get_staff_callback');
 add_action('wp_ajax_mz_mbo_get_staff', 'mz_mbo_get_staff_callback');	

 function mz_mbo_get_staff_callback() {

  check_ajax_referer( $_REQUEST['nonce'], "mz_MBO_get_registrants_nonce", false);
  	
 	require_once(MZ_MINDBODY_SCHEDULE_DIR .'mindbody-php-api/MB_API.php');
	require_once(MZ_MINDBODY_SCHEDULE_DIR .'inc/mz_mbo_init.inc');
	
	$mb = MZ_Mindbody_Init::instantiate_mbo_API();
 
 	$classid = $_REQUEST['staffID'];
 	$account_number = $_REQUEST['accountNumber'];
 	$result['type'] = "success";
 	$result['message'] = $classid;
	if ($account_number == 0) {
		$staff_details = $mb->GetStaff(array('StaffIDs'=>array($classid)));
	}else{
		$mb->sourceCredentials['SiteIDs'][0] = $account_number; 
		$staff_details = $mb->GetStaff(array('StaffIDs'=>array($classid)));
	}
 	
 	if (isset($staff_details['GetStaffResult'])):
 		if ($staff_details['GetStaffResult']['Status'] != 'Success'):
				$result['type'] = "error";
 				$result['message'] = __("Unable to retrieve staff details.", 'mz-mindbody-api');
 		else:
				$staffMember = $staff_details['GetStaffResult']['StaffMembers']['Staff'];
				$result['message'] = array();
				$result['type'] = "success";
				$result['message']['Name'] = $staffMember['Name'];
				$result['message']['Bio'] = $staffMember['Bio'];
				$result['message']['ImageURL'] = $staffMember['ImageURL'];
				$result['message']['Full'] = $staffMember;
		endif;
	endif;
 		
 	if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
       $result = json_encode($result);
       echo $result;
    }
    else {
       header("Location: ".$_SERVER["HTTP_REFERER"]);
    }
 
    die();
 }
 //End Ajax Get Staff
}
else
{// non-admin enqueues, actions, and filters

function run_mz_mindbody_schedule_api() {
 
    $mz_mbo = new MZ_Mindbody_API();
    $mz_mbo->run();
}
 
run_mz_mindbody_schedule_api();
	
/*	function load_jquery() {
		wp_enqueue_script( 'jquery' );
	}
	add_action( 'wp_enqueue_script', 'load_jquery' );
	*/
	
}//EOF Not Admin


?>
