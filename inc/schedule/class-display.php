<?php
namespace MZ_Mindbody\Inc\Schedule;

use MZ_Mindbody;
use MZ_Mindbody\Inc\Core as Core;
use MZ_Mindbody\Inc\Common as Common;
use MZ_Mindbody\Inc\Common\Interfaces as Interfaces;

class Display extends Interfaces\ShortCode_Script_Loader {

    static $addedAlready = false;

    public function handleShortcode($atts, $content = null) {

        $atts = shortcode_atts( array(
			'type' => 'week',
			'location' => '', // stop using this eventually, in preference "int, int" format
			'locations' => '',
			'account' => '0',
			'filter' => '0',
			'grid' => '0',
			'advanced' => '0',
			'hide' => '',
			'class_types' => '',
			'show_registrants' => '0',
			'hide_cancelled' => '1',
			'registrants_count' => '0',
			'mode_select' => '0',
			'unlink' => 0
				), $atts );

        // Add Style with script adder
        self::addScript();
        self::localizeScript($atts);

        ob_start();
        $template_loader = new Core\Template_Loader();
        $mz_timeframe = Common\Schedule_Operations::mz_getDateRange($mz_date, 7);
        $schedule_object = new Retrieve_Schedule;
        $mb = $schedule_object->instantiate_mbo_API();
        echo date('M d, Y', $schedule_object->seven_days_from_now());
        
		if ($mb == 'NO_SOAP_SERVICE') {
			return $mb;
			}
		if ($this->account == 0) {
			$mz_schedule_data = $mb->GetClasses($mz_timeframe[0]);
			}else{
			$mb->sourceCredentials['SiteIDs'][0] = $this->account; 
			$mz_schedule_data = $mb->GetClasses($mz_timeframe);
			}
		$data = array(
			'schedule' => $mz_schedule_data
		);
        $template_loader->set_template_data( $data );
        $template_loader->get_template_part( 'schedule' );

        return ob_get_clean();
    }

    public function addScript() {
        if (!self::$addedAlready) {
            self::$addedAlready = true;
            wp_register_script('mz_mindbody_schedule_script', MZ_Mindbody\PLUGIN_NAME_URL . 'dist/scripts/get-registrants.js', array('jquery'), 1.0, true );
 	        wp_enqueue_script('mz_mindbody_schedule_script');
            wp_register_style( 'mz_mindbody_style', MZ_Mindbody\PLUGIN_NAME_URL . 'dist/styles/main.css');
            wp_enqueue_style('mz_mindbody_style');
        }
    }

    public static function localizeScript($atts = []) {

        $protocol = isset( $_SERVER["HTTPS"]) ? 'https://' : 'http://';
        $nonce = wp_create_nonce( 'mz_mindbody_schedule_nonce');
        $params = array(
            'ajaxurl' => admin_url( 'admin-ajax.php', $protocol ),
            'nonce' => $nonce,
            'atts' => $atts
            );
        wp_localize_script( 'mz_mindbody_schedule_script', 'mz_mindbody_schedule', $params);
    }

}

?>
