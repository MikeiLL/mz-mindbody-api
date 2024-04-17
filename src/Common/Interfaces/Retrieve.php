<?php
/**
 * Retreive
 *
 * This file contains the abstract Retrieve class with
 * methods necessary for interfacing with MBO Api.
 *
 * @package MzMindbody
 */

namespace MZoo\MzMindbody\Common\Interfaces;

use MZoo\MzMindbody as NS;
use MZoo\MzMindbody\Core as Core;
use MZoo\MzMindbody\Libraries as Libraries;

/**
 * Retrieve
 *
 * Abstract class which exposes top level methods for calls to MBO.
 */
abstract class Retrieve {

    /**
     * MBO Account Number
     *
     * This may be set in shortcode, or defaults to Options setting: mz_mindbody_siteID.
     *
     * @since  2.4.7
     * @access protected
     *
     * @var int    $mbo_account    MBO Account to retrieve data for.
     */
    protected $mbo_account;

    /**
     * Shortcode Attributes
     *
     * Full array of Shortcode atts.
     *
     * @since  2.4.7
     * @access public
     *
     * @var array    $atts    Included in shortcode.
     */
    public $atts;

    /**
     * Class Constructor
     *
     * @param array $atts from shortcode call in wp post.
     * @return void.
     */
    public function __construct( $atts = array() ) {
        $this->atts        = $atts;
        $this->mbo_account = ! empty( $this->atts['account'] ) ? $this->atts['account'] : 0;
    }

    /**
     * Instantiate MBO API
     *
     * Configure the MBO API object with Credentials stored in DB
     *
     * @since 2.4.7
     *
     */
    public function instantiate_mbo_api( ) {

        // TODO can we avoid this call to get_option?
        $basic_options = get_option( 'mz_mbo_basic', 'Error: No Options' );
        if ( 'Error: No Options' === $basic_options || empty( $basic_options ) ) {
            return false;
        } else {
            return new Libraries\MboV6Api(
                array(
                    'mz_source_name'       => $basic_options['mz_source_name'],
                    'mz_mindbody_password' => $basic_options['mz_mindbody_password'],
                    'mz_mbo_app_name'      => $basic_options['mz_mbo_app_name'],
                    'mz_mbo_api_key'       => $basic_options['mz_mbo_api_key'],
                    'sourcename_not_staff' => $basic_options['sourcename_not_staff'],
                    'mz_mindbody_siteID'   => $basic_options['mz_mindbody_siteID'],
                ),
                $this->atts
            ); // Need attributes in API now for ScheduleTypeIds.
        }
    }

    /**
     * Generate a name for transient
     *
     * Name will include whatever strings are needed to
     * differentiate it from other transients storing
     * MBO results. Attr values are substringed to limit
     * length of string.
     *
     * Used by $this->get_mbo_results
     *
     * resource: https://css-tricks.com/the-deal-with-wordpress-transients/
     *
     * @param array $shortcode stores attributes that will make
     *                   transient name unique.
     *
     * @since  2.4.7
     * @return string our prefix concat with hashed version of shortcode and atts
     */
    protected function generate_transient_name( $shortcode = 'sc' ) {
        $prefix           = 'mz_mbo_';
        $transient_string = $shortcode . '_';
        foreach ( $this->atts as $k => $attr ) {
            if ( empty( $attr ) || ( 0 === (int) $attr ) ) {
                continue;
            }
            if ( is_array( $attr ) ) {
                $attr = implode( '_', $attr );
            }
            $transient_string .= '_' . substr( $k, 0, 4 ) . '_' . substr( $attr, 0, 3 );
        }
        // append today's date. TODO: Today's date is not enough to make this unique.
        $transient_string .= gmdate( 'Y-m-d', strtotime( wp_date( 'Y-m-d H:i:s' ) ) );

        return $prefix . md5( $transient_string );
    }


    /**
     * Log via Sandbox dev plugin
     *
     * If sandbox plugin located at
     * https://github.com/MikeiLL/mz-mbo-sandbox
     * is loaded, use it to log debug info.
     */
    private function sandbox() {
        include_once ABSPATH . 'wp-admin/includes/plugin.php';
        if ( is_plugin_active( 'mz-mbo-sandbox/mZ-mbo-sandbox.php' ) ) :
            $mbo_sandbox = new MzMBO_Sandbox_Admin( '1.0' );
            $mbo_sandbox->load_sandbox();
            $mbo_sandbox->run_sandbox( 'MBO Instantiation via ' . $_SERVER['REQUEST_URI'] );
        endif;
    }

    /**
     * Get results from MBO
     *
     * @since 2.4.7
     */
    abstract public function get_mbo_results();
}
