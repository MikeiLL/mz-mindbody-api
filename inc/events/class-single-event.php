<?php
namespace MZ_Mindbody\Inc\Events;

use MZ_Mindbody as NS;
use MZ_Mindbody\Inc\Libraries as Library;

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
     * @var $className string as pulled in from ['ClassDescription']['Name']
     */
    public $className;

    /**
     * Event Image
     *
     * @since 2.4.7
     *
     * @access public
     * @var $classImage string url to image hosted with MBO
     */
    public $classImage;

    /**
     * Event Staff Name
     *
     * @since 2.4.7
     *
     * @access public
     * @var $staffName string as pulled in from ['Staff']['Name']
     */
    public $staffName;

    /**
     * Event Staff Biography
     *
     * @since 2.4.7
     *
     * @access public
     * @var $staffName string as pulled in from ['Staff']['Bio']
     */
    public $staffBio;

    /**
     * Event Image
     *
     * @since 2.4.7
     *
     * @access public
     * @var $staffImage string url to image hosted with MBO
     */
    public $staffImage;

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
     * @var $location_ID string
     */
    public $location_ID;

    /**
     * Event Location Name
     *
     * @since 2.4.7
     *
     * @access public
     * @var $location_Name string
     */
    public $location_Name;

    /**
     * Event Location Address
     *
     * @since 2.4.7
     *
     * @access public
     * @var $location_Address string
     */
    public $location_Address;

    /**
     * Event Location Address2
     *
     * @since 2.4.7
     *
     * @access public
     * @var $location_Address2 string
     */
    public $location_Address2;

    /**
     * Event Location City
     *
     * @since 2.4.7
     *
     * @access public
     * @var $location_City string
     */
    public $location_City;

    /**
     * Event Location State Provo Code
     *
     * @since 2.4.7
     *
     * @access public
     * @var $location_StateProvCode string
     */
    public $location_StateProvCode;

    /**
     * Event Location Postal/Zip Code
     *
     * @since 2.4.7
     *
     * @access public
     * @var $location_PostalCode string
     */
    public $location_PostalCode;

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
     * Class Name Link object for display in schedules.
     *
     *
     * @since    2.4.7
     * @access   public
     * @var      HTML object    $class_name_link    Instance of HTML class.
     */
    public $class_name_link;

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
        $this->classScheduleID = $event['ClassScheduleID'];
        $this->StartDateTime = $event['StartDateTime'];
        $this->EndDateTime = $event['EndDateTime'];
        $this->className = $event['ClassDescription']['Name'];
        $this->ID = $event['ClassDescription']['ID'];
        $this->staffName = $event['Staff']['Name'];
        $this->staffImage = $event['Staff']['ImageURL'];
        $this->staffBio = NS\MZMBO()->helpers->prepare_html_string($event['Staff']['Bio']);
        $this->Description = NS\MZMBO()->helpers->prepare_html_string($event['ClassDescription']['Description']);
        $this->classImage = $event['ClassDescription']['ImageURL'];
        $this->location_ID = $event['Location']['ID'];
        $this->location_Name = $event['Location']['Name'];
        $this->location_Address = $event['Location']['Address'];
        $this->location_Address2 = $event['Location']['Address2'];
        $this->location_City = $event['Location']['City'];
        $this->location_StateProvCode = $event['Location']['StateProvCode'];
        $this->location_PostalCode = $event['Location']['PostalCode'];
        $this->atts = $atts;
        $this->class_name_link = $this->event_link_maker();
        $this->staff_name_link = $this->event_link_maker('staff');
    }

    private function event_link_maker($type = 'class'){
        $class_name_link = new Library\HTML_Element('a');
        $class_name_link->set('href', NS\PLUGIN_NAME_URL . 'inc/frontend/views/modals/modal_descriptions.php');
        $linkArray = array(
            'data-staffName' => $this->staffName
        );
        switch ($type) {

            case 'staff':
                $linkArray['data-staffImage'] = ($this->staffImage != '') ? $this->staffImage : '';
                $linkArray['data-staffBio'] = ($this->staffBio != '') ? $this->staffBio : '';
                $linkArray['text'] = $this->staffName;
                $linkArray['data-target'] = 'mzStaffScheduleModal';
                $linkArray['class'] = 'modal-toggle ' . sanitize_html_class($this->staffName, 'mz_staff_name');
                break;

            default:
                $linkArray['data-className'] = $this->className;
                $linkArray['data-classDescription'] = ($this->Description != '') ? $this->Description : '';
                $linkArray['data-eventImage'] = ($this->classImage != '') ? $this->classImage : '';
                $linkArray['text'] = $this->className;
                $linkArray['data-target'] = 'mzDescriptionModal';
                $linkArray['class'] = 'modal-toggle ' . sanitize_html_class($this->className, 'mz_class_name');

        }

        $class_name_link->set($linkArray);
        return $class_name_link;
    }

}

?>