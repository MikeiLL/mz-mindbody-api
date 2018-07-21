<?php
namespace MZ_Mindbody\Inc\Events;

use MZ_Mindbody as NS;

/**
 * Class that holds and formats a single event from MBO API GetClasses Result.
 *
 * These are generally MBO "Enrollments" as opposed to Drop-Ins
 *
 * @since 2.4.7
 *
 * @used in events/class-display.
 *
 * @param $event array from MBO
 * @param $atts array from shortcode attribute call
 */
class Single_Event {

    /**
     * Event ClassScheduleID
     *
     * @since 2.4.7
     *
     * @access public
     * @var $ClassScheduleID int TODO differentiate from ['ClassDescription']['ID']
     */
    public $ClassScheduleID;

    /**
     * Event Class ID
     *
     * @since 2.4.7
     *
     * @access public
     * @var $ID int TODO differentiate from ['ClassScheduleID']
     */
    public $ID;

    /**
     * Event Start Time
     *
     * @since 2.4.7
     *
     * @access public
     * @var $StartDateTime datetime as pulled in from MBO
     */
    public $StartDateTime;

    /**
     * Event End Time
     *
     * @since 2.4.7
     *
     * @access public
     * @var $EndDateTime datetime as pulled in from MBO
     */
    public $EndDateTime;

    /**
     * Event Name
     *
     * @since 2.4.7
     *
     * @access public
     * @var $ClassName string as pulled in from ['ClassDescription']['Name']
     */
    public $ClassName;

    /**
     * Event Image
     *
     * @since 2.4.7
     *
     * @access public
     * @var $ClassImage string url to image hosted with MBO
     */
    public $ClassImage;

    /**
     * Event Staff Name
     *
     * @since 2.4.7
     *
     * @access public
     * @var $StaffName string as pulled in from ['Staff']['Name']
     */
    public $StaffName;

    /**
     * Event Staff Biography
     *
     * @since 2.4.7
     *
     * @access public
     * @var $StaffName string as pulled in from ['Staff']['Bio']
     */
    public $StaffBio;

    /**
     * Event Image
     *
     * @since 2.4.7
     *
     * @access public
     * @var $StaffImage string url to image hosted with MBO
     */
    public $StaffImage;

    /**
     * Event Name
     *
     * @since 2.4.7
     *
     * @access public
     * @var $Description string as pulled in from ['ClassDescription']['Description']
     */
    public $Description;

    /**
     * Event Location ID
     *
     * @since 2.4.7
     *
     * @access public
     * @var $Location_ID string
     */
    public $Location_ID;

    /**
     * Event Location Name
     *
     * @since 2.4.7
     *
     * @access public
     * @var $Location_Name string
     */
    public $Location_Name;

    /**
     * Event Location Address
     *
     * @since 2.4.7
     *
     * @access public
     * @var $Location_Address string
     */
    public $Location_Address;

    /**
     * Event Location Address2
     *
     * @since 2.4.7
     *
     * @access public
     * @var $Location_Address2 string
     */
    public $Location_Address2;

    /**
     * Event Location City
     *
     * @since 2.4.7
     *
     * @access public
     * @var $Location_City string
     */
    public $Location_City;

    /**
     * Event Location State Provo Code
     *
     * @since 2.4.7
     *
     * @access public
     * @var $Location_StateProvCode string
     */
    public $Location_StateProvCode;

    /**
     * Event Location Postal/Zip Code
     *
     * @since 2.4.7
     *
     * @access public
     * @var $Location_PostalCode string
     */
    public $Location_PostalCode;

    /**
     * Shortcode Attributes
     *
     * @since 2.4.7
     *
     * @access protected
     * @var $atts array The shortcode atts from shortcode out of which Event is instantiated
     */
    protected $atts;

    /**
     * Populate attributes with data from MBO
     *
     * @since 2.4.7
     *
     * @access public
     * @param array $event array of class/event attributes.
     * @param array $atts array of shortcode attributes from calling shortcode.
     */
    public function __construct($event, $atts = array()) {
        $this->ClassScheduleID = $event['ClassScheduleID'];
        $this->StartDateTime = $event['StartDateTime'];
        $this->EndDateTime = $event['EndDateTime'];
        $this->ClassName = $event['ClassDescription']['Name'];
        $this->ID = $event['ClassDescription']['ID'];
        $this->StaffName = $event['Staff']['Name'];
        $this->StaffImage = $event['Staff']['ImageURL'];
        $this->StaffBio = $event['Staff']['Bio'];
        $this->Description = $event['ClassDescription']['Description'];
        $this->ClassImage = $event['ClassDescription']['ImageURL'];
        $this->Location_ID = $event['Location']['ID'];
        $this->Location_Name = $event['Location']['Name'];
        $this->Location_Address = $event['Location']['Address'];
        $this->Location_Address2 = $event['Location']['Address2'];
        $this->Location_City = $event['Location']['City'];
        $this->Location_StateProvCode = $event['Location']['StateProvCode'];
        $this->Location_PostalCode = $event['Location']['PostalCode'];
        $this->atts = $atts;
    }

}

?>