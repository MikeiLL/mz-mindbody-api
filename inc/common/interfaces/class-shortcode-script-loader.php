<?php
namespace MZ_Mindbody\Inc\Common\Interfaces;

/**
 * "WordPress Plugin Template" Copyright (C) 2018 Michael Simpson  (email : michael.d.simpson@gmail.com)
 * Adapted from this excellent article:
 * http://scribu.net/wordpress/optimal-script-loading.html
 *
 * The idea is you have a shortcode that needs a script loaded, but you only
 * want to load it if the shortcode is actually called.
 */
abstract class Shortcode_Script_Loader extends Shortcode_Loader {

    var $doAddScript;

    public function register($shortcodeName) {
        $this->registerShortcodeToFunction($shortcodeName, 'handleShortcodeWrapper');

        // It will be too late to enqueue the script in the header,
        // but can add them to the footer
        add_action('wp_footer', array($this, 'addScriptWrapper'));
    }

    public function handleShortcodeWrapper($atts, $content = null) {
        // Flag that we need to add the script
        $this->doAddScript = true;
        return $this->handleShortcode($atts, $content);
    }


    public function addScriptWrapper() {
        // Only add the script if the shortcode was actually called
        if ($this->doAddScript) {
            $this->addScript();
        }
    }

    /**
     * @abstract override this function with calls to insert scripts needed by your shortcode in the footer
     * Example:
     *   wp_register_script('my-script', plugins_url('js/my-script.js', __FILE__), array('jquery'), '1.0', true);
     *   wp_print_scripts('my-script');
     * @return void
     */
    public abstract function addScript();

}
