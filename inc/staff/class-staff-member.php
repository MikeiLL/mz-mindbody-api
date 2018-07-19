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
     * int example 100000285
     */
    private $ID;

    /**
     * Staff Member Name
     *
     * @access private
     * string example Ken Berry
     */
    private $Name;

    /**
     * Staff Member First Name
     *
     * @access private
     * string example Ken
     */
    private $FirstName;

    /**
     * Staff Member LastName
     *
     * @access private
     * string example Ken Berry
     */
    private $LastName;

    /**
     * Staff Member Image URL
     *
     * @access private
     *
     * Image URL key seems to only exist if it is set.
     * string example https://clients.mindbodyonline.com/studios/DemoAPISandboxRestore/staff/100000285_large.jpg?imageversion=1531922456
     */
    private $ImageURL;

    /**
     * Staff Member Biography
     *
     * This may have arbitrary HTML. Particularly <p> and/or <div> tags which need to be handled.
     *
     * @access private
     * string example '<p>Super cool cat.</p>'
     */
    private $Bio;

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
        $this->Bio = $staff_member['Bio'];
    }

}