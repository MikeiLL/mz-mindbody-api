<?php

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
class StaffMember
{

    /**
     * Staff Member ID
     *
     * @access public
     * @var $ID int example 100000285
     */
    public $ID;

    /**
     * Staff Member Full Name
     *
     * @access public
     * @var $Name string example Ken Berry
     */
    public $Name;

    /**
     * Staff Member First Name
     *
     * @access public
     * @var $FirstName string example Ken
     */
    public $FirstName;

    /**
     * Staff Member LastName
     *
     * @access public
     * @var $LastName string example Ken Berry
     */
    public $LastName;

    /**
     * Staff Member Image URL
     *
     * @access public
     *
     * Image URL key seems to only exist if it is set.
     * @var $ImageURL string example https://clients.mindbodyonline.com/studios/DemoAPISandboxRestore/staff/100000285_large.jpg?imageversion=1531922456
     */
    public $ImageURL;

    /**
     * Staff Member Image Tag
     *
     * @access public
     *
     * This will either be an empty string or an image tag.
     * @var $ImageTag string example https://clients.mindbodyonline.com/studios/DemoAPISandboxRestore/staff/100000285_large.jpg?imageversion=1531922456
     */
    public $ImageTag;

    /**
     * Staff Member Biography
     *
     * This may have arbitrary HTML. Particularly <p> and/or <div> tags which need to be handled.
     *
     * @access public
     * @var $Bio string example '<p>Super cool cat.</p>'
     */
    public $Bio;

    /**
     * Staff Sort Order
     *
     * MBO allows admin to set sort order for Staff Members
     *
     * @access public
     * @var $SortOrder int
     */

    public $SortOrder;

    /**
     * Staff Member Schedule Link Button
     *
     * @access public
     * @var $ScheduleButton string of HTML to create button link to schedule on MBO website
     */
    public $ScheduleButton;

    /**
     * Shortcode Attributed
     *
     * @access protected
     * @var $atts array The shortcode atts from shortcode out of which class is instantiated
     */
    protected $atts;

    /**
     * Site ID for MBO account AKA MBO Account ID
     *
     * @access public
     * @var $siteID int The siteID as set in shortcode or defaulting to options
     */
    public $siteID;

    /**
     * Populate attributes with data from MBO
     *
     * @since 2.4.7
     *
     * @access public
     * @param array $staff_member array of staff_member attributes.
     * @param array $atts array of shortcode attributes from calling shortcode.
     */
    public function __construct($staff_member, $atts = array())
    {

        $this->ID = $staff_member['Id'];
        $this->FirstName = $staff_member['FirstName'];
        $this->LastName = $staff_member['LastName'];// Set Staff Name up.
        // First set first, last with default to blank string
        $this->Name = isset($this->FirstName) ? $this->FirstName . ' ' . $this->LastName : '';
        // If "Name" has been set, use that
        if (isset($staff_member['Name'])) {
            $this->Name = $staff_member['Name'];
        }
        $this->SortOrder = $staff_member['SortOrder'];
        $this->ImageURL = isset($staff_member['ImageUrl']) ? $staff_member['ImageUrl'] : '';
        $this->Bio = isset($staff_member['Bio']) ? NS\MZMBO()->helpers->prepareHtmlString($staff_member['Bio']) : '';
        $this->atts = $atts;
        $this->siteID = isset($this->atts['siteID']) ? $this->atts['siteID'] : NS\Core\MzMindbodyApi::$basic_options['mz_mindbody_siteID'];
        // If there's an image create a tag, otherwise empty string.
        $this->ImageTag = isset($staff_member['ImageUrl']) ? '<img src="' . $this->ImageURL . '" alt="' . $this->Name . '" class="img-responsive mz_modal_staff_image_body">' : '';
        $this->ScheduleButton = '<a href="http://clients.mindbodyonline.com/ws.asp?studioid=' . $this->siteID . '&stype=-7&sView=week&sTrn=' . $this->ID . '" class="btn btn-info mz-btn-info" target="_blank">' . sprintf(__('See %s&apos;s Schedule', 'mz-mindbody-api'), $this->Name) . '</a>';
    }
}
