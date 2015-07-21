<?php
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
 * Version: 		2.1.0
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
        	if (($hook['callback'] == 'instantiate_mbo_API') && ($hook['component'] == 'MZ_Mindbody_Init')) {
        		add_action( $hook['hook'], array( $hook['component'], $hook['callback'] ) );
        		//mz_pr(MZ_MBO_Instances::$instances_of_MBO);
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
        $this->version = '2.1.0';
 
        $this->load_dependencies();
        $this->define_main_hooks();
        $this->add_shortcodes();
 
    }
    
    public function whatever() {
    	$recentPosts = new WP_Query();
    	$recentPosts->query('showposts=2');
    	while ($recentPosts->have_posts()) : $recentPosts->the_post();
    		$mb = MZ_Mindbody_Init::instantiate_mbo_API();
    		mz_pr($mb);
    	endwhile;
    }
    
    public function whenever() {
    	$recentPosts = new WP_Query();
    	$recentPosts->query('showposts=2');
    	while ($recentPosts->have_posts()) : $recentPosts->the_post();
    		$mb = MZ_Mindbody_Init::instantiate_mbo_API();
    		echo '<hr/>';
    		mz_pr($mb);
    	endwhile;
    }
    
    public function in_the_title() {
    	mz_pr('In title, yo');
    }
 
    private function load_dependencies() {
    
 		//Advanced Includes
		foreach ( glob( plugin_dir_path( __FILE__ )."advanced/*.php" ) as $file )
        	include_once $file;
        	
        include_once(dirname( __FILE__ ) . '/mindbody-php-api/MB_API.php');

		foreach ( glob( plugin_dir_path( __FILE__ )."inc/*.php" ) as $file )
			include_once $file;
			
		if (phpversion() >= 5.3) {
			include_once('php_variants/sort_newer.php');
			}else{
			include_once('php_variants/sort_older.php');
			}
	
		//Functions

		require_once MZ_MINDBODY_SCHEDULE_DIR .'lib/functions.php';
        	
        $this->loader = new MZ_Mindbody_API_Loader();
    }
 
    private function define_admin_hooks() {
 
        $admin = new MZ_Mindbody_API_Admin( $this->get_version() );
        $this->loader->add_action( 'admin_enqueue_scripts', $admin, 'enqueue_styles' );
        $this->loader->add_action( 'add_meta_boxes', $admin, 'add_meta_box' );
    }
    
    private function define_main_hooks() {
 
        $this->loader->add_action( 'init', $this, 'myStartSession' );
        $this->loader->add_action( 'wp_logout', $this, 'myStartSession' );
        $this->loader->add_action( 'wp_login', $this, 'myEndSession' );
        //$this->loader->add_action( 'wp_head', $this, 'whenever' );
        //$this->loader->add_action( 'wp_head', $this, 'whatever' );
        //$this->loader->add_action( 'the_title', $this, 'in_the_title' );
        //$this->loader->add_action( 'wp_head', 'MZ_Mindbody_Init', 'instantiate_mbo_API' );
        //$this->loader->add_action( 'wp_footer', 'MZ_Mindbody_Init', 'instantiate_mbo_API' );
        
        }

    public function myStartSession() {
			if ((function_exists('session_status') && session_status() !== PHP_SESSION_ACTIVE) || !session_id()) {
				  session_start();
				}
		}

    public function myEndSession() {
			session_destroy ();
		}
 
 	private function add_shortcodes() {
 	
 		$schedule_display = new MZ_Mindbody_Schedule_Display();
 		$mz_staff = new MZ_MBO_Staff();
 		$mz_events = new MZ_MBO_Events();
 		$mz_clients = new MZ_MBO_Clients();
 		
        add_shortcode('mz-mindbody-show-schedule', array($schedule_display, 'mZ_mindbody_show_schedule'));
        add_shortcode('mz-mindbody-staff-list', array($mz_staff, 'mZ_mindbody_staff_listing'));
        add_shortcode('mz-mindbody-show-events', array($mz_events, 'mZ_mindbody_show_events'));
        add_shortcode('mz-mindbody-login', array($mz_clients, 'mZ_mindbody_login'));
        add_shortcode('mz-mindbody-signup', array($mz_clients, 'mZ_mindbody_signup'));
        add_shortcode('mz-mindbody-logout', array($mz_clients, 'mZ_mindbody_logout'));

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
	//Don't know if there's anything we need to do here.
}

function mZ_mindbody_schedule_deactivation() {
	//Don't know if there's anything we need to do here.
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

//TODO Deal with conflict when $mb class get's called twice
add_action('widgets_init', 'mZ_mindbody_schedule_register_widget');

function mZ_mindbody_schedule_register_widget() {
    register_widget( 'mZ_Mindbody_day_schedule');
}

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
            'description' => __('Display class schedule for current day.', 'mz-mindbody-api')
            );
        $this->WP_Widget('mZ_Mindbody_day_schedule', __('Today\'s MindBody Schedule', 'mz-mindbody-api'),
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
