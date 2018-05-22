<?php
namespace MZ_Mindbody\Inc\Events;

use MZ_Mindbody;
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
     * Events object.
     *
     * @since    2.4.7
     * @access   public
     *
     * @used in handleShortcode, get_events_modal
     * @var      object $events_object The class that retrieves the MBO events.
     */
    public $events_object;

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


    public $location;
    public $locations;
    public $list_only;
    public $event_count;
    public $account;
    public $advanced;
    public $week_only;

    /**
     *
     *
     * @since    2.4.7
     * @access   public
     *
     * @used in handleShortcode, display_events
     * @var      @array
     */
    public $clientID;

    public function handleShortcode($atts, $content = null)
    {

        $this->atts = shortcode_atts( array(
            'location' => '1',
            'locations' => '',
            'list' => 0,
            'event_count' => '0',
            'account' => '0',
            'advanced' => '1',
            'week-only' => 0,
            'offset' => 0
        ), $atts );

        /*
         * This is for backwards compatibility for previous to using an array to hold one or more locations.
        */
        if (($this->locations == '') || !isset($this->locations)) {
            if ($this->locations == '') {
                $this->locations = array('1');
            }else{
                $this->locations = array($this->locations);
            }
        }else{
            $this->locations = explode(', ', $atts['locations']);
        }

        ob_start();

        $template_loader = new Core\Template_Loader();

        $this->events_object = new Retrieve_Events($this->atts);

        // Call the API and if fails, return error message.
        if ($response === $this->events_object->get_mbo_results()) return "<div>" . __("Mindbody plugin settings error.", 'mz-mindbody-api') . "</div>";
        // Add Style with script adder
        self::addScript();

        $response = $this->events_object->get_mbo_results();

        $events = ($response['GetClassesResult']['ResultCount'] >= 1) ? $events['GetClassesResult']['Classes']['Class'] : __('No Events in current cycle', 'mz-mindbody-api');

        $this->template_data = array(
            'atts' => $this->atts,
            'events' => $events
        );

        $template_loader->set_template_data($this->template_data);
        $template_loader->get_template_part('event_list');

        return ob_get_clean();
    }

    public function addScript()
    {
        if (!self::$addedAlready) {
            self::$addedAlready = true;

            wp_register_style('mz_mindbody_style', MZ_Mindbody\PLUGIN_NAME_URL . 'dist/styles/main.css');
            wp_enqueue_style('mz_mindbody_style');

            wp_register_script('mz_mbo_bootstrap_script', MZ_Mindbody\PLUGIN_NAME_URL . 'dist/scripts/main.js', array('jquery'), 1.0, true);
            wp_enqueue_script('mz_mbo_bootstrap_script');

        }
    }


}

?>
