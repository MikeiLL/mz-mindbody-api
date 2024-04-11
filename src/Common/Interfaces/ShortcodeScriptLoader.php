<?php
/**
 * Shortcode Script Loader
 *
 * This file contains the abstract class for registering and displaying
 * shortcode which require their own assets to be loaded.
 *
 * @package MzMindbody
 */

namespace MZoo\MzMindbody\Common\Interfaces;
use MZoo\MzMindbody\Core;

/**
 * "WordPress Plugin Template" Copyright (C) 2018 Michael Simpson  (email : michael.d.simpson@gmail.com)
 * Adapted from this excellent article:
 * http://scribu.net/wordpress/optimal-script-loading.html
 *
 * The idea is you have a shortcode that needs a script loaded, but you only
 * want to load it if the shortcode is actually called.
 */
abstract class ShortcodeScriptLoader extends ShortcodeLoader {

    /**
     * If shortcode script should be enqueued.
     *
     * @since  2.4.7
     * @access private
     * @var    boolean $do_add_script True if handling shortcode wrapper.
     */
    private $do_add_script;

    /**
     * Register
     *
     * @param string $shortcode_name The shortcode string that will trigger function.
     */
    public function register( $shortcode_name ) {
        $this->register_shortcode_to_function( $shortcode_name, 'handle_shortcode_wrapper' );

        /*
         * It will be too late to enqueue the script in the header,
         * but can add them to the footer.
         */
        add_action( 'wp_footer', array( $this, 'addScriptWrapper' ) );
    }

    /**
     * Handle Shortcode Wrapper
     *
     * Make the actual call to rendering function.
     *
     * @param array       $atts Atts as included in post shortcode.
     * @param string|html $content Post content between a pair of shortcode tags.
     */
    public function handle_shortcode_wrapper( $atts, $content = null ) {
        // Flag that we need to add the script.
        $this->do_add_script = true;

        if (empty(Core\MzMindbodyApi::$basic_options['mz_mindbody_siteID'])) {
            return "Check your config settings. Site ID is not set.";
        }

        return $this->handle_shortcode( $atts, $content );
    }

    /**
     * Add Script Wrapper
     *
     * Make the actual call to rendering function.
     *
     * Will enqueue the assets if shortcode was called.
     */
    public function addScriptWrapper() {
        // Only add the script if the shortcode was actually called.
        if ( $this->do_add_script ) {
            $this->addScript();
        }
    }

    /**
     * Add Script
     *
     * @abstract override this function with calls to insert scripts needed by your shortcode in the footer
     * Example:
     *   wp_register_script('my-script', plugins_url('js/my-script.js', __FILE__), array('jquery'), '1.0', true);
     *   wp_print_scripts('my-script');
     * @return   void
     */
    abstract public function addScript();
}
