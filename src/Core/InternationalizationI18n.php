<?php
/**
 * Plugin Internationalization
 *
 * Not sure if we are using this, currently.
 *
 * @package MzMindbody
 */

namespace MZoo\MzMindbody\Core;

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link  http://mzoo.org
 * @since 1.0.0
 *
 * @author Mike iLL/mZoo.org
 */
class InternationalizationI18n {



    /**
     * The text domain of the plugin.
     *
     * @since  2.4.7
     * @access protected
     * @var    string    $text_domain    The text domain of the plugin.
     */
    private $text_domain;

    /**
     * Initialize the class and set its properties.
     *
     * @since 2.4.7
     * @param string $plugin_text_domain The text domain of this plugin.
     */
    public function __construct( $plugin_text_domain ) {

        $this->text_domain = $plugin_text_domain;
    }

    /**
     * Load the plugin text domain for translation.
     *
     * @since 2.4.7
     */
    public function load_plugin_textdomain() {

        load_plugin_textdomain(
            $this->text_domain,
            false,
            dirname( dirname( dirname( plugin_basename( __FILE__ ) ) ) ) . '/languages/'
        );
    }
}
