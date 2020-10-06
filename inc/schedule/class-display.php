<?php
namespace MZ_Mindbody\Inc\Schedule;

use MZ_Mindbody as NS;
use MZ_Mindbody\Inc\Core as Core;
use MZ_Mindbody\Inc\Common as Common;
use MZ_Mindbody\Inc\Common\Interfaces as Interfaces;

class Display extends Interfaces\Shortcode_Script_Loader
{

    /**
     * If shortcode script has been enqueued.
     *
     * @since    2.4.7
     * @access   public
     *
     * @used in handleShortcode, addScript
     * @var      boolean $addedAlready True if shorcdoe scripts have been enqueued.
     */
    static $addedAlready = false;

    // Table styling option holders
    /**
     *
     *
     * @since    2.4.7
     * @access   public
     *
     * @var      string
     */
    public $table_class;
    /**
     *
     *
     * @since    2.4.7
     * @access   public
     *
     * @var      string
     */
    public $horizontal_class;

    /**
     *
     *
     * @since    2.4.7
     * @access   public
     *
     * @var      string
     */
    public $grid_class;

    /**
     * Text for Mode Select Button when not yet toggled
     *
     * @since    2.4.7
     * @access   public
     *
     * @var      string $initial_button_text
     */
    public $initial_button_text;

    /**
     * Text for Mode Select Button when toggled
     *
     * @since    2.4.7
     * @access   public
     *
     * @var      string $swap_button_text
     */
    public $swap_button_text;

    /**
     *
     *
     * @since    2.4.7
     * @access   public
     *
     * @var      string
     */
    public $data_target; // Which modal target to use for modal pop-up,

    /**
     *
     *
     * @since    2.4.7
     * @access   public
     *
     * @var      string
     */
    public $class_modal_link; // which modal include display file to select


    /**
     * Instance of Retrieve_Schedule.
     *
     * @since    2.4.7
     * @access   public
     * @populated in handleShortcode
     * @used in handleShortcode, localizeScript, display_schedule
     * @var      object $schedule_object Instance of Retrieve_Schedule.
     */
    public $schedule_object;

    /**
     * Site ID
     *
     * Used in Display Schedule sent to studioID in show schedule button in teacher modal.
     * Might be same as account.
     *
     * @since    2.4.7
     * @access   public
     * @var      int $siteID
     */
    public $siteID;

    /**
     * Shortcode attributes.
     *
     * @since    2.4.7
     * @access   public
     *
     * @used in handleShortcode, localizeScript, display_schedule
     * @var      array $atts Shortcode attributes function called with.
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

    /**
     * Which type of schedule to display
     *
     * Will be 'horizontal' (default), 'grid' or 'both'.
     *
     * @since    2.4.7
     * @access   public
     *
     * @used in handleShortcode, display_schedule
     * @var      string    $display_type    Which type of schedule to display.
     */
    public $display_type;

    /**
     * Which schedule_event items not to display
     *
     * Teacher, Location, Sign-up, Duration
     *
     * @since    2.4.7
     * @access   public
     *
     * @used in handleShortcode, display_schedule
     * @var      array    $hide    Which elements to not display in schedule.
     */
    public $hide;


    public function handleShortcode($atts, $content = null)
    {
    
        $this->atts = shortcode_atts(array(
            'type' => 'week',
            'location' => '', // stop using this eventually, in preference "int, int" format
            'locations' => 1,
            'account' => 0,
            'filter' => 0,
            'hide_cancelled' => 0,
            'grid' => 0,
            'advanced' => 0,
            'this_week' => 0,
            'hide' => '',
            'class_types' => '', // migrating to session_types 2019 December(ish)
            'session_types' => '',
            'session_type_ids' => '',
            'show_registrants' => 0,
            'calendar_format' => 'horizontal',
            'schedule_type' => null, // allow over-ridding of global setting
            'show_registrants' => 0,
            'registrants_count' => 0,
            'classesByDateThenTime' => array(),
            'mode_select' => 0,
            'unlink' => 0,
            'offset' => 0
        ), $atts);

        // Set siteID to option if not set explicitly in shortcode
        $this->siteID = (isset($atts['account'])) ? $atts['account'] : Core\MZ_Mindbody_Api::$basic_options['mz_mindbody_siteID'];

        $this->class_modal_link = NS\PLUGIN_NAME_URL . 'inc/frontend/views/modals/modal_descriptions.php';

        // Support the old 'class_types' shortcode attr
        $this->atts['session_types'] = !empty($this->atts['class_types']) ? $this->atts['class_types'] : $this->atts['session_types'];

        // If set, turn Session/Class Types into an Array and call it session_types
        if ($this->atts['session_types'] !== '') {
            if (!is_array($this->atts['session_types'])) // if not already an array
                $this->atts['session_types'] = explode(',', $this->atts['session_types']);
            // TODO: is this sometimes done reduntantly?
                foreach ($this->atts['session_types'] as $key => $type):
                    $this->atts['session_types'][$key] = trim($type);
                endforeach;
        } else {
            $this->atts['session_types'] == '';
        }
        
        // If set, turn Session/Class Types into an Array and call it session_types
        if ($this->atts['session_type_ids'] !== '') {
            if (!is_array($this->atts['session_type_ids'])) // if not already an array
                $this->atts['session_type_ids'] = explode(',', $this->atts['session_type_ids']);
            // TODO: is this sometimes done reduntantly?
                foreach ($this->atts['session_type_ids'] as $key => $type):
                    $this->atts['session_type_ids'][$key] = trim($type);
                endforeach;
        } else {
            $this->atts['session_type_ids'] == '';
        }

        // Break locations up into array, if it hasn't already been.
        $this->atts['locations'] = (!is_array($this->atts['locations'])) ? explode(',', str_replace(' ', '', $this->atts['locations'])) : $this->atts['locations'];

        // Set sLoc which we use in generating link, to first location in locations array.
        $this->sLoc = $this->atts['locations'][0];

        // Break hide up into array, if it hasn't already been.
        $this->atts['hide'] = (!is_array($this->atts['hide'])) ? explode(',', str_replace(' ', '', strtolower($this->atts['hide']))) : $this->atts['hide'];
        // Begin generating output
        ob_start();

        $template_loader = new Core\Template_Loader();
        $this->schedule_object = new Retrieve_Schedule($this->atts);

        // Call the API and if fails, return error message.
        if (false == $this->schedule_object->get_mbo_results()) return "<div>" . __("Error returning schedule from MBO for shortcode display.", 'mz-mindbody-api') . "</div>";
        
        /*
         * Configure the display type based on shortcode atts.
         */
        $this->display_type = (!empty($atts['grid'])) ? 'grid' : 'horizontal';

        // If mode_select is on, render both grid and horizontal
        if (!empty($atts['mode_select'])) $this->display_type = 'both';

        // Define styling variables based on shortcode attribute values
        $this->table_class = ($this->atts['filter'] == 1) ? 'mz-schedule-filter' : 'mz-schedule-table';

        if ($this->atts['mode_select'] == 1):
            $this->grid_class = ' mz_hidden';
            $this->horizontal_class = '';
            $this->initial_button_text = __('Grid View', 'mz-mindbody-api');
            $this->swap_button_text = __('Horizontal View', 'mz-mindbody-api');
        elseif ($this->atts['mode_select'] == 2):
            $this->horizontal_class = ' mz_hidden';
            $this->grid_class = '';
            $this->initial_button_text = __('Horizontal View', 'mz-mindbody-api');
            $this->swap_button_text = __('Grid View', 'mz-mindbody-api');
        else:
            $this->horizontal_class = $this->grid_class = '';
            $this->initial_button_text = 0;
            $this->swap_button_text = 0;
        endif;

        /*
         *
         * Determine which type(s) of schedule(s) need to be configured
         *
         * The schedules are not class objects because they change depending on
         * (date) offset value when they are called.
         */

        // Initialize the variables, so won't be un-set:
        $horizontal_schedule = '';
        $grid_schedule = '';

        if ($this->display_type == 'grid' || $this->display_type == 'both'):
            $grid_schedule = $this->schedule_object->sort_classes_by_time_then_date();
        endif;
        if ($this->display_type == 'horizontal' || $this->display_type == 'both'):
            $horizontal_schedule = $this->schedule_object->sort_classes_by_date_then_time();
        endif;

        $week_names = array(
            __('Sunday', 'mz-mindbody-api'),
            __('Monday', 'mz-mindbody-api'),
            __('Tuesday', 'mz-mindbody-api'),
            __('Wednesday', 'mz-mindbody-api'),
            __('Thursday', 'mz-mindbody-api'),
            __('Friday', 'mz-mindbody-api'),
            __('Saturday', 'mz-mindbody-api'),
        );

        /*
         * If wordpress-configured week starts on Monday instead of Sunday,
         * we shift our week names array
         */
        if ( Core\MZ_Mindbody_Api::$start_of_week != 0 ) {
            array_push($week_names, array_shift($week_names));
        }

        $this->template_data = array(
            'atts' => $this->atts,
            'data_target' => $this->data_target,
            'grid_class' => $this->grid_class,
            'horizontal_class' => $this->horizontal_class,
            'initial_button_text' => $this->initial_button_text,
            'swap_button_text' => $this->swap_button_text,
            'time_format' => $this->schedule_object->time_format,
            'date_format' => $this->schedule_object->date_format,
            'data_nonce' => wp_create_nonce('mz_schedule_display_nonce'),
            'siteID' => $this->siteID,
            'week_names' => $week_names,
            'start_date' => $this->schedule_object->start_date,
            'end_date' => $this->schedule_object->current_week_end,
            'display_type' => $this->display_type,
            'hide' => $this->atts['hide'],
            'table_class' => $this->table_class,
            'locations_dictionary' => $this->schedule_object->locations_dictionary,
            'login' => NS\MZMBO()->i18n->get('login'),
            'login_to_sign_up' => NS\MZMBO()->i18n->get('login_to_sign_up'),
            'signup_nonce' => wp_create_nonce('mz_signup_nonce'),
            'registration_button' => NS\MZMBO()->i18n->get('registration_button'),
            'username' => NS\MZMBO()->i18n->get('username'),
            'password' => NS\MZMBO()->i18n->get('password'),
            'manage_on_mbo' => NS\MZMBO()->i18n->get('manage_on_mbo'),
            'horizontal_schedule' => $horizontal_schedule,
            'grid_schedule' => $grid_schedule
        );

        $template_loader->set_template_data($this->template_data);
        $template_loader->get_template_part('schedule_container');


        // Add Style with script adder
        self::addScript();

        return ob_get_clean();
    }

    public function addScript()
    {
        if (!self::$addedAlready) {
            self::$addedAlready = true;

            wp_register_style('mz_mindbody_style', NS\PLUGIN_NAME_URL . 'dist/styles/main.css');
            wp_enqueue_style('mz_mindbody_style');

            wp_register_script('mz_mbo_bootstrap_script', NS\PLUGIN_NAME_URL . 'dist/scripts/main.js', array('jquery'), NS\PLUGIN_VERSION, true);
            wp_enqueue_script('mz_mbo_bootstrap_script');

            wp_register_script('mz_display_schedule_script', NS\PLUGIN_NAME_URL . 'dist/scripts/schedule-display.js', array('jquery', 'mz_mbo_bootstrap_script'), NS\PLUGIN_VERSION, true);
            wp_enqueue_script('mz_display_schedule_script');

            if ($this->atts['filter'] == 1):
                wp_register_script('filterTable', NS\PLUGIN_NAME_URL . 'dist/scripts/mz_filtertable.js', array('jquery', 'mz_display_schedule_script'), NS\PLUGIN_VERSION, true);
                wp_enqueue_script('filterTable');
            endif;

            $this->localizeScript();

        }
    }

    public function localizeScript()
    {
        // Clean out unneeded strings from Locations Dictionary
        $locations_dictionary = $this->schedule_object->locations_dictionary;
        foreach($locations_dictionary as $k => $v){
            unset($locations_dictionary[$k]['link']);
        }

        $protocol = isset($_SERVER["HTTPS"]) ? 'https://' : 'http://';

        $translated_strings = NS\MZMBO()->i18n->get();

        $params = array(
            'ajaxurl' => admin_url('admin-ajax.php', $protocol),
            'nonce' => wp_create_nonce('mz_schedule_display_nonce'),
            'atts' => $this->atts,
            'with' => __('with', 'mz-mindbody-api'),
            'account' => Core\MZ_Mindbody_Api::$basic_options['mz_mindbody_siteID'],
            'initial' => $this->initial_button_text,
            'mode_select' => $this->atts['mode_select'],
            'swap' => $this->swap_button_text,
            'registrants_header' => __('Registrants', 'mz-mindbody-api'),
            'get_registrants_error' => __('Error retrieving class details.', 'mz-mindbody-api'),
            'error' => __('Sorry but there was an error retrieving the schedule.', 'mz-mindbody-api'),
            'sub_by_text' => __('substitute for', 'mz-mindbody-api'),
            'no_bio' => __('No biography listed for this staff member.', 'mz-mindbody-api'),
            'filter_default' => __('by teacher, class type', 'mz-mindbody-api'),
            'quick_1' => __('morning', 'mz-mindbody-api'),
            'quick_2' => __('afternoon', 'mz-mindbody-api'),
            'quick_3' => __('evening', 'mz-mindbody-api'),
            'label' => __('Filter', 'mz-mindbody-api'),
            'selector' => __('All Locations', 'mz-mindbody-api'),
            'login' => $translated_strings['login'],
            'signup' => $translated_strings['sign_up'],
            'confirm_signup' => $translated_strings['confirm_signup'],
            'logout' => $translated_strings['logout'],
            'signup_heading' => $translated_strings['signup_heading'],
            'Locations_dict' => json_encode($locations_dictionary),
            'signup_nonce' =>  wp_create_nonce('mz_signup_nonce'),
            'siteID' => $this->siteID,
            'location' => $this->sLoc        );
        wp_localize_script('mz_display_schedule_script', 'mz_mindbody_schedule', $params);
    }

    /**
     * Ajax function to return mbo schedule
     *
     * @since 2.4.7
     *
     * This duplicates a lot of the handle_shortcode function, but
     * is called via AJAX and used when navigating the schedule.
     *
     *
     *
     * Echo json json_encode() version of HTML from template
     */
    public function display_schedule()
    {

        check_ajax_referer($_REQUEST['nonce'], "mz_schedule_display_nonce", false);

        $atts = $_REQUEST['atts'];

        $result['type'] = "success";

        $template_loader = new Core\Template_Loader();

        $this->schedule_object = new Retrieve_Schedule($atts);

        // Call the API and if fails, return error message.
        if (false == $this->schedule_object->get_mbo_results()) echo "<div>" . __("Error returning schedule from MBO for display.", 'mz-mindbody-api') . "</div>";

        // Register attributes
        $this->handleShortcode($atts);

        // Update the data array
        $this->template_data['time_format'] = $this->schedule_object->time_format;
        $this->template_data['date_format'] = $this->schedule_object->date_format;

        $template_loader->set_template_data($this->template_data);

        // Initialize the variables, so won't be un-set:
        $horizontal_schedule = '';
        $grid_schedule = '';
        if ($this->display_type == 'grid' || $this->display_type == 'both'):
            ob_start();
            $grid_schedule = $this->schedule_object->sort_classes_by_time_then_date();
            // Update the data array
            $this->template_data['grid_schedule'] = $grid_schedule;
            $template_loader->get_template_part('grid_schedule');
            $result['grid'] = ob_get_clean();
        endif;

        if ($this->display_type == 'horizontal' || $this->display_type == 'both'):
            ob_start();
            $horizontal_schedule = $this->schedule_object->sort_classes_by_date_then_time();
            // Update the data array
            $this->template_data['horizontal_schedule'] = $horizontal_schedule;
            $template_loader->get_template_part('horizontal_schedule');
            $result['horizontal'] = ob_get_clean();
        endif;

        $result['message'] = __('Error. Please try again.', 'mz-mindbody-api');

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $result = json_encode($result);
            echo $result;
        } else {
            header("Location: " . $_SERVER["HTTP_REFERER"]);
        }

        die();

    }

}

?>
