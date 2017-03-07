<?php
/* source: http://wordpress.stackexchange.com/a/70372/48604
*/

class Plugin_Controller {

    /**
     * Plugin instance.
     *
     * @see get_instance()
     * @type object
     */
    protected static $instance = NULL;

    /**
     * URL to this plugin's directory.
     *
     * @type string
     */
    public $plugin_url = '';

    /**
     * Path to this plugin's directory.
     *
     * @type string
     */
    public $plugin_path = '';

    /**
     * Access this plugin’s working instance
     *
     * @wp-hook plugins_loaded
     * @since   2012.10.23
     * @return  object
     */
    public static function get_instance() 
    {
        NULL === self::$instance and self::$instance = new self();

        return self::$instance;
    }

    /**
     * Used for regular plugin work.
     *
     * @wp-hook plugins_loaded
     * @since   2012.10.23
     * @return  void
     */
    public function plugin_setup() 
    {
        $this->plugin_url  = plugins_url( '/', dirname( __FILE__ ) );
        $this->plugin_path = plugin_dir_path( dirname( __FILE__ ) );
    }
}
?>