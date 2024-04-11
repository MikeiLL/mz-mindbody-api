<?php
/**
 * Staff Member
 *
 * This file contains the class that holds and formats
 * a single single staff member from MBO API Staff Result.
 *
 * @package MzMindbody
 */

namespace MZoo\MzMindbody\Staff;

use MZoo\MzMindbody as NS;
use MZoo\MzMindbody\Core as Core;
use MZoo\MzMindbody\Libraries\HtmlElement;

/**
 * Class that holds and formats a single staff member from MBO API Staff Result.
 *
 * StaffMember construct receives a single staff member from the MBO API array containing
 * sub-array for ProviderIDs.
 *
 * ImageURL seems to only be returned if populated
 *
 * @param $staff_member array
 */
class StaffMember {

    /**
     * Staff Member ID
     *
     * @access public
     * @var    $ID int example 100000285
     */
    public $ID;

    /**
     * Staff Member Full Name
     *
     * @access public
     * @var    string $staff_name Name of staff member. example Ken Berry.
     */
    public $staff_name;

    /**
     * Staff Member First Name
     *
     * @access public
     * @var    $first_name string example Ken
     */
    public $first_name;

    /**
     * Staff Member LastName
     *
     * @access public
     * @var    $last_name string example Ken Berry
     */
    public $last_name;

    /**
     * Staff Member Image URL
     *
     * @access public
     *
     * Image URL key seems to only exist if it is set.
     * @var    string $image_url example https://clients.mindbodyonline.com/studios/DemoAPISandboxRestore/staff/100000285_large.jpg?imageversion=1531922456
     */
    public $image_url;

    /**
     * Staff Member Image Tag
     *
     * @access public
     *
     * This will either be an empty string or an image tag.
     * @var    $image_tag string example https://clients.mindbodyonline.com/studios/DemoAPISandboxRestore/staff/100000285_large.jpg?imageversion=1531922456
     */
    public $image_tag;

    /**
     * Staff Member Biography
     *
     * This may have arbitrary HTML. Particularly <p> and/or <div> tags which need to be handled.
     *
     * @access public
     * @var    $staff_bio string example '<p>Super cool cat.</p>'
     */
    public $staff_bio;

    /**
     * Staff Sort Order
     *
     * MBO allows admin to set sort order for Staff Members
     *
     * @access public
     * @var    $sort_order int
     */

    public $sort_order;

    /**
     * Staff Member Schedule Link Button
     *
     * @access public
     * @var    $schedule_button string of HTML to create button link to schedule on MBO website
     */
    public $schedule_button;

    /**
     * Shortcode Attributed
     *
     * @access protected
     * @var    $atts array The shortcode atts from shortcode out of which class is instantiated
     */
    protected $atts;

    /**
     * Site ID for MBO account AKA MBO Account ID
     *
     * @access public
     * @var    $site_id int The siteID as set in shortcode or defaulting to options
     */
    public $site_id;

    /**
     * Populate attributes with data from MBO
     *
     * @since 2.4.7
     *
     * @access public
     * @param  array $staff_member array of staff_member attributes from MBO.
     * @param  array $atts         array of shortcode attributes from calling shortcode.
     */
    public function __construct( $staff_member, $atts = array() ) {

        $this->ID         = $staff_member['Id'];
        $this->first_name = $staff_member['FirstName'];
        $this->last_name  = $staff_member['LastName'];// Set Staff Name up.
        // First set first, last with default to blank string.
        $this->staff_name = isset( $this->first_name ) ? $this->first_name . ' ' . $this->last_name : '';
        // If "Name" has been set, use that.
        if ( isset( $staff_member['Name'] ) ) {
            $this->staff_name = $staff_member['Name'];
        }
        $this->sort_order = $staff_member['SortOrder'];
        $this->image_url  = isset( $staff_member['ImageUrl'] ) ? $staff_member['ImageUrl'] : '';
        $this->staff_bio  = isset( $staff_member['Bio'] ) ? NS\MZMBO()->helpers->prepare_html_string( $staff_member['Bio'] ) : '';
        $this->atts       = $atts;
        $this->site_id    = isset( $this->atts['site_id'] ) ? $this->atts['site_id'] : NS\Core\MzMindbodyApi::$basic_options['mz_mindbody_siteID'];
        // If there's an image create a tag, otherwise empty string.
        $this->image_tag        = isset( $staff_member['ImageUrl'] ) ? '<img src="' . $this->image_url . '" alt="' . $this->staff_name . '" class="img-responsive mz_modal_staff_image_body">' : '';
        $this->schedule_button  = '<a href="http://clients.mindbodyonline.com/ws.asp?studioid=' . $this->site_id . '&stype=-7&sView=week&sTrn=' . $this->ID;
        $this->schedule_button .= '" class="btn btn-info mz-btn-info" target="_blank">';
        // translators: Assign schedule to instructoru using posessive apostrophe s, like "Jaimie's".
        $this->schedule_button .= sprintf( __( 'See %s&apos;s Schedule', 'mz-mindbody-api' ), $this->staff_name ) . '</a>';
    }
}
