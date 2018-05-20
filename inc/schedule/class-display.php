<?php
namespace MZ_Mindbody\Inc\Schedule;

use MZ_Mindbody;
use MZ_Mindbody\Inc\Core as Core;
use MZ_Mindbody\Inc\Common as Common;
use MZ_Mindbody\Inc\Common\Interfaces as Interfaces;

class Display extends Interfaces\ShortCode_Script_Loader {

    static $addedAlready = false;
       // Table styling option holders
    public $table_class;
    public $horizontal_class;
		public $grid_class;
		public $initial_button_text;
		public $swap_button_text;
    public $data_target; // Which modal target to use for modal pop-up,
    public $class_modal_link; // which modal include display file to select
    		
    
    /**
     * Instance of Retrieve_Schedule.
     *
     * @since    2.4.7
     * @access   public
     * @populated in handleShortcode
     * @used in handleShortcode, localizeScript, display_schedule
     * @var      object    $schedule_object    Instance of Retrieve_Schedule.
     */
    public $schedule_object;
    
    /**
     * Shortcode attributes.
     *
     * @since    2.4.7
     * @access   public
     * 
     * @used in handleShortcode, localizeScript, display_schedule
     * @var      array    $atts    Shortcode attributes function called with.
     */
    public $atts;
     
     /**
     * Data to send to template
     *
     * @since    2.4.7
     * @access   public
     * 
     * @used in handleShortcode, display_schedule
     * @var      @array    $data    array to send template.
     */
    public $template_data;

    public function handleShortcode($atts, $content = null) {

        $this->atts = shortcode_atts( array(
					'type' => 'week',
					'location' => '', // stop using this eventually, in preference "int, int" format
					'locations' => array(1),
					'account' => '0',
					'filter' => '0',
					'hide_cancelled' => 0,
					'grid' => 0,
					'advanced' => 0,
					'this_week' => 0,
					'hide' => array(),
					'class_types' => '',
					'show_registrants' => 0,
					'calendar_format' => 'horizontal',
					'class_type' => 'Enrollment',
					'show_registrants' => 0,
					'hide_cancelled' => 1,
					'registrants_count' => '0',
					'classesByDate' => array(),
					'mode_select' => 0,
					'unlink' => 0,
					'offset' => 0
				), $atts );
				
				// Define styling variables based on shortcode attribute values
				$this->table_class = ($this->atts['filter'] == 1) ? 'mz-schedule-filter' : 'mz-schedule-table';
     
				if ($this->atts['mode_select'] == 1):
					$this->grid_class = 'mz_hidden '.$this->table_class;
					$this->horizontal_class = $this->table_class;
					$this->initial_button_text = __('Grid View', 'mz-mindbody-api');
					$this->swap_button_text = __('Horizontal View', 'mz-mindbody-api');
				elseif ($this->atts['mode_select'] == 2):
					$this->horizontal_class = 'mz_hidden '.$this->table_class;
					$this->grid_class = $this->table_class;
					$this->initial_button_text = __('Horizontal View', 'mz-mindbody-api');
					$this->swap_button_text = __('Grid View', 'mz-mindbody-api');
				else:
					$this->horizontal_class = $this->table_class;
					$this->grid_class = $this->table_class;
					$this->initial_button_text = 0;
					$this->swap_button_text = 0;
				endif;
        
        $show_registrants = ( $this->atts['show_registrants'] == 1 ) ? true : false;
        // Are we displaying registrants?
        $this->data_target = $show_registrants ? 'registrantModal' : 'mzModal';
        $this->class_modal_link = MZ_Mindbody\PLUGIN_NAME_URL . 'inc/frontend/views/modals/modal_descriptions.php';
        
        ob_start();

        $template_loader = new Core\Template_Loader();
        $this->schedule_object = new Retrieve_Schedule($this->atts);
        
        // Call the API and if fails, return error message.
        if (false == $this->schedule_object->get_mbo_results()) return "<div>" . __("Mindbody plugin settings error.", 'mz-mindbody-api') . "</div>";
        
        // Add Style with script adder
        self::addScript();
                
        $horizontal_schedule = $this->schedule_object->sort_classes_by_date_and_time();

        $this->template_data = array(
			'atts' => $this->atts,
			'data_target' => $this->data_target,
			'grid_class' => $this->grid_class,
			'horizontal_class' => $this->horizontal_class,
			'initial_button_text' => $this->initial_button_text,
			'swap_button_text' => $this->swap_button_text,
            'horizontal_schedule' => $horizontal_schedule,
            'time_format' => $this->schedule_object->time_format,
            'date_format' => $this->schedule_object->date_format,
            'data_nonce' => wp_create_nonce( 'mz_MBO_get_registrants_nonce'),
            'data_target' => $this->data_target,
            'class_modal_link' => $this->class_modal_link
        );

        $template_loader->set_template_data( $this->template_data );
        $template_loader->get_template_part( 'schedule_container' );

        return ob_get_clean();
    }

    public function addScript() {
        if (!self::$addedAlready) {
            self::$addedAlready = true;
            
            wp_register_style( 'mz_mindbody_style', MZ_Mindbody\PLUGIN_NAME_URL . 'dist/styles/main.css');
            wp_enqueue_style('mz_mindbody_style');
                        
            wp_register_script('mz_mbo_bootstrap_script', MZ_Mindbody\PLUGIN_NAME_URL . 'dist/scripts/main.js', array('jquery'), 1.0, true );
            wp_enqueue_script('mz_mbo_bootstrap_script');
            
            wp_register_script('mz_display_schedule_script', MZ_Mindbody\PLUGIN_NAME_URL . 'dist/scripts/schedule-display.js', array('jquery', 'mz_mbo_bootstrap_script'), 1.0, true );
            wp_enqueue_script('mz_display_schedule_script');
            
            // wp_register_script('mz_mindbody_show_registrants', MZ_Mindbody\PLUGIN_NAME_URL . 'dist/scripts/show-registrants.js', array('jquery'), 1.0, true );
            // wp_enqueue_script('mz_mindbody_show_registrants');
          	
						wp_register_script('filterTable', MZ_Mindbody\PLUGIN_NAME_URL . 'dist/scripts/mz_filtertable.js', array('jquery'), null, true);
            wp_enqueue_script('filterTable');
            
        		$this->localizeScript();
        		
        }
    }

    public function localizeScript() {
        $protocol = isset( $_SERVER["HTTPS"]) ? 'https://' : 'http://';
        $nonce = wp_create_nonce( 'mz_schedule_display_nonce');
        $params = array(
            'ajaxurl' => admin_url( 'admin-ajax.php', $protocol ),
            'nonce' => $nonce,
            'atts' => $this->atts,
			'filter_default' => __('by teacher, class type', 'mz-mindbody-api'),
			'quick_1' => __('morning', 'mz-mindbody-api'),
			'quick_2' => __('afternoon', 'mz-mindbody-api'),
			'quick_3' => __('evening', 'mz-mindbody-api'),
			'label' => __('Filter', 'mz-mindbody-api'),
			'selector' => __('All Locations', 'mz-mindbody-api'),
			'Locations_dict' => $this->schedule_object->locations_dictionary,
			'staff_preposition' => __('with', 'mz-mindbody-api'),
			'initial' => $this->initial_button_text,
			'mode_select' => $this->atts['mode_select'],
			'swap' => $this->swap_button_text,
			'registrants_header' => __('Registrants', 'mz-mindbody-api'),
			'get_registrants_error' => __('Error retreiving class details.', 'mz-mindbody-api'),
            'error' => __('Sorry but there was an error retrieving the schedule.', 'mz-mindbody-api')
            );
        wp_localize_script( 'mz_display_schedule_script', 'mz_mindbody_schedule', $params);
        
    }

    /**
     * Ajax function to return mbo schedule
     *
     * @since 2.4.7
     *
     *
     *
     * @return @json json_encode() version of HTML from template
     */
    public function display_schedule() {

        check_ajax_referer( $_REQUEST['nonce'], "mz_schedule_display_nonce", false);

        $atts = $_REQUEST['atts'];

        $result['type'] = "success";

        ob_start();

        $template_loader = new Core\Template_Loader();
        
        $this->schedule_object = new Retrieve_Schedule($atts);
        
        // Call the API and if fails, return error message.
        if (false == $this->schedule_object->get_mbo_results()) return "<div>" . __("Mindbody plugin settings error.", 'mz-mindbody-api') . "</div>";
        
        $horizontal_schedule = $this->schedule_object->sort_classes_by_date_and_time();
        
        // Register attributes
        $this->handleShortcode($atts);
                
		// Update the data array
        $this->template_data['horizontal_schedule'] = $horizontal_schedule;
        $this->template_data['time_format'] = $this->schedule_object->time_format;
        $this->template_data['date_format'] = $this->schedule_object->date_format;

        $template_loader->set_template_data( $this->template_data );
        $template_loader->get_template_part( 'schedule' );

        $result['message'] = ob_get_clean();

        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $result = json_encode($result);
            echo $result;
        }
        else {
            header("Location: ".$_SERVER["HTTP_REFERER"]);
        }

        die();

    }

}

?>
