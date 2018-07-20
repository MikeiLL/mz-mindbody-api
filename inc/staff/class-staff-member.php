<?php
namespace MZ_Mindbody\Inc\Staff;

use MZ_Mindbody as NS;
use MZ_Mindbody\Inc\Core as Core;
use MZ_Mindbody\Inc\Libraries\HTML_Element;

/**
 * Class that holds and formats a single staff member from MBO API Staff Result.
 *
 * Staff_Member construct receives a single staff member from the MBO API array containing
 * sub-array for ProviderIDs.
 *
 * ImageURL seems to only be returned if populated
 *
 * @param $staff_member array
 */
class Staff_Member {

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
    public function __construct($staff_member, $atts = array()) {
        $this->ID = $staff_member['ID'];
        $this->Name = $staff_member['Name'];
        $this->FirstName = $staff_member['FirstName'];
        $this->LastName = $staff_member['LastName'];
        $this->ImageURL = isset($staff_member['ImageURL']) ? $staff_member['ImageURL'] : '';
        $this->Bio = $this->clean_up_staff_bio($staff_member['Bio']);
        $this->atts = $atts;
        $this->siteID = isset($this->atts['siteID']) ? $this->atts['siteID'] : NS\Inc\Core\MZ_Mindbody_Api::$basic_options['mz_mindbody_siteID'];
        // If there's an image create a tag, otherwise empty string.
        $this->ImageTag = isset($staff_member['ImageURL']) ? '<img src="' . $this->ImageURL . '" alt="' . $this->Name . '" class="img-responsive mz_modal_staff_image_body">' : '';
        $this->ScheduleButton = '<a href="http://clients.mindbodyonline.com/ws.asp?studioid=' . $this->siteID . '&stype=-7&sView=week&sTrn=' . $this->ID . '" class="btn btn-info mz-btn-info" target="_blank">'.sprintf(__('See %s&apos;s Schedule', 'mz-mindbody-api'), $this->Name).'</a>';
    }

    /**
     * Clean up staff biography
     *
     * Remove empty HTML tags as well as the opening container tag (div, p or span) so we
     * can rebuild the bio with Image Thumbnail inside of the container with the text.
     **
     * @since 2.4.7
     * @param string Biography returned by MBO
     * @return string $bio Cleaned up HTML string.
     */
    private function clean_up_staff_bio($bio){
        // Remove empty tags
        $bio = str_replace("/<[^\/>]*>(\s|xC2xA0|&nbsp;)*<\/[^>]*>/", '', $bio);
        $bio = $this->str_lreplace("</p>", "", $bio);
        $bio = $this->str_lreplace("</div>", "", $bio);
        if (substr($bio, 0, 3) == '<p>') {
            $mz_staff_bio = substr($bio, 3);
        } else if (substr($bio, 0, 5) == '<div>'){
            $bio = substr($bio, 5);
            $mz_staff_bio = substr($bio, 3);
        } else if (substr($bio, 0, 5) == '<span>'){
            $bio = substr($bio, 6);
        }
        return $bio;
    }

    /**
     * String Left Replace
     *
     * source: https://stackoverflow.com/a/3835653/2223106
     */
    public function str_lreplace($search, $replace, $subject)
    {
        $pos = strrpos($subject, $search);

        if($pos !== false)
        {
            $subject = substr_replace($subject, $replace, $pos, strlen($search));
        }

        return $subject;
    }


}