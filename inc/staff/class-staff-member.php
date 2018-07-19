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
     * @access private
     * @var $ID int example 100000285
     */
    private $ID;

    /**
     * Staff Member Full Name
     *
     * @access private
     * @var $Name string example Ken Berry
     */
    private $Name;

    /**
     * Staff Member First Name
     *
     * @access private
     * @var $FirstName string example Ken
     */
    private $FirstName;

    /**
     * Staff Member LastName
     *
     * @access private
     * @var $LastName string example Ken Berry
     */
    private $LastName;

    /**
     * Staff Member Image URL
     *
     * @access private
     *
     * Image URL key seems to only exist if it is set.
     * @var $ImageURL string example https://clients.mindbodyonline.com/studios/DemoAPISandboxRestore/staff/100000285_large.jpg?imageversion=1531922456
     */
    private $ImageURL;

    /**
     * Staff Member Biography
     *
     * This may have arbitrary HTML. Particularly <p> and/or <div> tags which need to be handled.
     *
     * @access private
     * @var $Bio string example '<p>Super cool cat.</p>'
     */
    private $Bio;

    /**
     * Replace DOM Element Beginning from Bio
     *
     * This is used in page layout so that Image can be placed withing Biography container.
     *
     * We pull it out and replace it later when building the bio element.
     *
     * @access private
     * @var $mz_replace_dom_start string example '<p>Super cool cat.</p>'
     */
    private $mz_replace_dom_start;

    /**
     * Populate attributes with data from MBO
     *
     * @since 2.4.7
     *
     * @access public
     * @param array $staff_member array of staff_member attributes.
     */
    public function __construct($staff_member) {
        $this->ID = $staff_member['ID'];
        $this->Name = $staff_member['Name'];
        $this->FirstName = $staff_member['FirstName'];
        $this->LastName = $staff_member['LastName'];
        $this->ImageURL = isset($staff_member['ImageURL']) ? $staff_member['ImageURL'] : '';
        $this->Bio = $this->clean_up_staff_bio($staff_member['Bio']);
    }

    /**
     * Clean up staff biography
     *
     * Remove empty HTML tags as well as the opening container tag (div or p) so we
     * can rebuild the bio with Image Thumbnail inside of the container with the text.
     *
     * We also populate the $mz_replace_dom_start attribute here.
     *
     * @since 2.4.7
     * @param string Biography returned by MBO
     * @return string $bio Cleaned up HTML string.
     */
    private function clean_up_staff_bio($bio){
        $bio = str_replace("/<[^\/>]*>(\s|xC2xA0|&nbsp;)*<\/[^>]*>/", '', $bio);
        if (substr($bio, 0, 3) == '<p>') {
            $mz_staff_bio = substr($bio, 3);
            $this->mz_replace_dom_start = '<p>';
        } else if (substr($bio, 0, 5) == '<div>'){
            $bio = substr($bio, 5);
            $this->mz_replace_dom_start = '<div>';
        } else {
            $this->mz_replace_dom_start = '';
        }
        return $bio;
    }

}