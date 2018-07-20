<?php
namespace MZ_Mindbody\Inc\Staff;

use MZ_Mindbody as NS;
use MZ_Mindbody\Inc\Core as Core;
use MZ_Mindbody\Inc\Common as Common;
use MZ_Mindbody\Inc\Common\Interfaces as Interfaces;

class Display extends Interfaces\ShortCode_Script_Loader
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
     * Staff object.
     *
     * @since    2.4.7
     * @access   public
     *
     * @used in handleShortcode, get_staff_modal
     * @var      object $staff_object The class that retrieves the MBO staff.
     */
    public $staff_object;

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

    public function handleShortcode($atts, $content = null)
    {

        $this->atts = shortcode_atts(array(
            'account' => '0',
            'gallery' => '0',
            'hide' => ''
        ), $atts);

        $this->class_modal_link = NS\PLUGIN_NAME_URL . 'inc/frontend/views/modals/modal_descriptions.php';

        // If set, turn hide into an Array
        if ($this->atts['hide'] !== '') {
            if (!is_array($this->atts['hide'])) // if not already an array
                $this->atts['hide'] = explode(',', $this->atts['hide']);
            foreach ($this->atts['hide'] as $key => $type):
                $this->atts['hide'][$key] = trim($type);
            endforeach;
        }

        ob_start();

        $template_loader = new Core\Template_Loader();
        $this->staff_object = new Retrieve_Staff($this->atts);

        // Call the API and if fails, return error message.
        if (false === $this->staff_object->get_mbo_results()) return "<div>" . __("Mindbody plugin settings error.", 'mz-mindbody-api') . "</div>";



        if (isset($this->staff_object->staff_result['GetStaffResult']['StaffMembers']['Staff'])):
            $mz_staff_list = $this->staff_object->sort_staff_by_sort_order($this->atts);
            //$mz_staff_list = $this->staff_object->staff_result['GetStaffResult']['StaffMembers']['Staff'];
        else:
            NS\MZMBO()->helpers->mz_pr($this->staff_object);
            die('Something went wrong.');
        endif;


        // Add Style with script adder
        self::addScript();

        $this->template_data = array(
            'atts' => $this->atts,
            'staff' => $mz_staff_list
        );

        $template_loader->set_template_data($this->template_data);
        $template_loader->get_template_part('staff_list_horizontal');

        return ob_get_clean();
    }

    public function addScript()
    {
        if (!self::$addedAlready) {
            self::$addedAlready = true;

            wp_register_style('mz_mindbody_style', NS\PLUGIN_NAME_URL . 'dist/styles/main.css');
            wp_enqueue_style('mz_mindbody_style');

            wp_register_script('mz_mbo_bootstrap_script', NS\PLUGIN_NAME_URL . 'dist/scripts/main.js', array('jquery'), 1.0, true);
            wp_enqueue_script('mz_mbo_bootstrap_script');

        }
    }

    /**
     * Get Staff called via Ajax
     *
     * Used in the get schedule staff member link modal.
     */
    function get_staff_modal()
    {

        check_ajax_referer($_REQUEST['nonce'], "mz_staff_retrieve_nonce", false);

        ob_start();
        $template_loader = new Core\Template_Loader();

        $staffID = $_REQUEST['staffID'];

        $result['type'] = "success";

        $this->staff_object = new Retrieve_Staff();

        // Send an array of staffID
        $staff_details = $this->staff_object->get_mbo_results( array($staffID) );

        $this->template_data = array(
            'staff_details' => $staff_details['GetStaffResult']['StaffMembers']['Staff'],
            'staffID' => $staffID,
            'siteID' => $_REQUEST['siteID']
        );

        $template_loader->set_template_data($this->template_data);
        $template_loader->get_template_part('staff_modal');

        $result['message'] = ob_get_clean();

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $result = json_encode($result);
            echo $result;
        } else {
            header("Location: " . $_SERVER["HTTP_REFERER"]);
        }

        die();
    }
    //End Ajax Get Staff
}

?>
