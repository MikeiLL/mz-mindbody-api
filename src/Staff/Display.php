<?php
/**
 * This file contains the class with methods to handle the [mz-mindbody-staff-list] shortcode.
 *
 * It has been used to display php objects, text, arrays for view in the browser.
 *
 * @package MzMindbody
 */

namespace MZoo\MzMindbody\Staff;

use MZoo\MzMindbody as NS;
use MZoo\MzMindbody\Core as Core;
use MZoo\MzMindbody\Common as Common;
use MZoo\MzMindbody\Common\Interfaces as Interfaces;

/**
 * Staff Display Shortcode class
 *
 * Display MBO staff in a page via shortcode.
 */
class Display extends Interfaces\ShortcodeScriptLoader {



    /**
     * If shortcode script has been enqueued.
     *
     * @since  2.4.7
     * @access private
     *
     * @used in handle_shortcode, addScript.
     * @var  boolean $added_already True if shorcdoe scripts have been enqueued.
     */
    private static $added_already = false;

    /**
     * Shortcode attributes.
     *
     * @since  2.4.7
     * @access public
     *
     * @used in handle_shortcode, localize_script, display_schedule.
     * @var  array $atts Shortcode attributes function called with.
     */
    public $atts;

    /**
     * Staff object.
     *
     * @since  2.4.7
     * @access public
     *
     * @used in handle_shortcode, get_staff_modal.
     * @var  object $staff_object The class that retrieves the MBO staff.
     */
    public $staff_object;

    /**
     * Data to send to template
     *
     * @since  2.4.7
     * @access public
     *
     * @used in handle_shortcode, display_schedule.
     * @var  @array    $data    array to send template.
     */
    public $template_data;

    /**
     * Handle Shortcode
     *
     * @param  string $atts    shortcode inputs.
     * @param  string $content any content between start and end shortcode tags.
     * @return string shortcode result.
     */
    public function handle_shortcode( $atts, $content = null ) {

        $this->atts = shortcode_atts(
            array(
                'account'           => '0',
                'gallery'           => '0',
                'hide'              => '',
                'include_imageless' => 0,
            ),
            $atts
        );

        $this->class_modal_link = NS\PLUGIN_NAME_URL . 'src/Frontend/views/modals/modal_descriptions.php';

        // If set, turn hide into an Array.
        if ( '' !== $this->atts['hide'] ) {
            if ( ! is_array( $this->atts['hide'] ) ) { // if not already an array.
                $this->atts['hide'] = explode( ',', $this->atts['hide'] );
            }
            foreach ( $this->atts['hide'] as $key => $type ) :
                $this->atts['hide'][ $key ] = trim( $type );
            endforeach;
        }

        ob_start();

        $template_loader    = new Core\TemplateLoader();
        $this->staff_object = new RetrieveStaff( $this->atts );

        // Call the API and if fails, return error message.
        if ( false === $this->staff_object->get_mbo_results() ) {
            return '<div>' . __( 'Error displaying Staff from Mindbody.', 'mz-mindbody-api' ) . '</div>';
        }

        if ( isset( $this->staff_object->staff_result ) ) :
            $mz_staff_list = $this->staff_object->sort_staff_by_sort_order( $this->atts );
        else :
            NS\MZMBO()->helpers->print( $this->staff_object );
            die( 'Something went wrong. Check the server for staff object' );
        endif;

        // Add Style with script adder.
        self::addScript();

        $this->template_data = array(
            'atts'  => $this->atts,
            'staff' => $mz_staff_list,
        );

        $template_loader->set_template_data( $this->template_data );

        if ( 0 !== (int) $this->atts['gallery'] ) {
            $template_loader->get_template_part( 'staff_list_gallery' );
        } else {
            $template_loader->get_template_part( 'staff_list_horizontal' );
        }

        return ob_get_clean();
    }

    /**
     * Add Script.
     *
     * Add scripts if not added already.
     *
     * @return void
     */
    public function addScript() {
        if ( ! self::$added_already ) {
            self::$added_already = true;

            wp_register_style( 'mz_mindbody_style', NS\PLUGIN_NAME_URL . 'dist/styles/main.css' );
            wp_enqueue_style( 'mz_mindbody_style' );

            wp_register_script( 'mz_mbo_bootstrap_script', NS\PLUGIN_NAME_URL . 'dist/scripts/main.js', array( 'jquery' ), NS\PLUGIN_VERSION, true );
            wp_enqueue_script( 'mz_mbo_bootstrap_script' );

            wp_register_script( 'mz_mbo_staff_script', NS\PLUGIN_NAME_URL . 'dist/scripts/staff_popup.js', array( 'jquery' ), NS\PLUGIN_VERSION, true );
            wp_enqueue_script( 'mz_mbo_staff_script' );
        }
    }

    /**
     * Get Staff called via Ajax
     *
     * Used in the get schedule staff member link modal.
     */
    function get_staff_modal() {
        // Generated in Schedule\ScheduleItem.
        check_ajax_referer( 'mz_staff_retrieve_nonce', 'nonce' );

        ob_start();
        $template_loader = new Core\TemplateLoader();

        $staff_id = $_REQUEST['staffID'];

        $result['type'] = 'success';

        $this->staff_object = new RetrieveStaff();

        // Send an array of staff_id.
        $staff_result = $this->staff_object->get_mbo_results( array( $staff_id ) );

        $this->template_data = array(
            'staff_details' => new StaffMember( $staff_result[0] ), // returns an array.
            'staffID'       => $staff_id,
            'site_id'       => $_REQUEST['site_id'],
        );

        $template_loader->set_template_data( $this->template_data );
        $template_loader->get_template_part( 'staff_modal' );

        $result['message'] = ob_get_clean();

        if ( ! empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) &&
            'xmlhttprequest' === strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) ) {
            $result = wp_json_encode( $result );
            echo $result;
        } else {
            header( 'Location: ' . $_SERVER['HTTP_REFERER'] );
        }

        die();
    }
    // End Ajax Get Staff.
}
