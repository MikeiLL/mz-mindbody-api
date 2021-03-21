<?php

namespace MZoo\MzMindbody\Events;

use MZoo\MzMindbody as NS;
use MZoo\MzMindbody\Core as Core;
use MZoo\MzMindbody\Libraries as Library;

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
class SingleEvent
{


    /**
     * Event Class Schedule ID
     *
     * @since 2.4.7
     *
     * @used in making link to signup
     *
     * @access public
     * @var    $class_schedule_id int TODO differentiate from ['ClassDescription']['Id']
     */
    public $class_schedule_id;

    /**
     * Event Class ID
     *
     * @since 2.4.7
     *
     * @access public
     * @var    $ID int TODO differentiate from ['ClassScheduleId']
     */
    public $ID;

    /**
     * Event Start Time
     *
     * @since 2.4.7
     *
     * @access public
     * @var    $startDateTime datetime as pulled in from MBO
     */
    public $startDateTime;

    /**
     * Event End Time
     *
     * @since 2.4.7
     *
     * @access public
     * @var    $endDateTime datetime as pulled in from MBO
     */
    public $endDateTime;

    /**
     * Event Name
     *
     * @since 2.4.7
     *
     * @access public
     * @var    $className string as pulled in from ['ClassDescription']['Name']
     */
    public $className;

    /**
     * Event Image
     *
     * @since 2.4.7
     *
     * @access public
     * @var    $classImage string url to image hosted with MBO
     */
    public $classImage;

    /**
     * Event Staff Name
     *
     * @since 2.4.7
     *
     * @access public
     * @var    $staffName string as pulled in from ['Staff']['Name']
     */
    public $staffName;

    /**
     * Event Staff Biography
     *
     * @since 2.4.7
     *
     * @access public
     * @var    $staffName string as pulled in from ['Staff']['Bio']
     */
    public $staffBio;

    /**
     * Event Image
     *
     * @since 2.4.7
     *
     * @access public
     * @var    $staffImage string url to image hosted with MBO
     */
    public $staffImage;

    /**
     * Event Name
     *
     * @since 2.4.7
     *
     * @access public
     * @var    $Description string as pulled in from ['ClassDescription']['Description']
     */
    public $Description;

    /**
     * Event Location ID
     *
     * @since 2.4.7
     *
     * @access public
     * @var    $location_ID string
     */
    public $location_ID;

    /**
     * Event Location Name
     *
     * @since 2.4.7
     *
     * @access public
     * @var    $location_Name string
     */
    public $location_Name;

    /**
     * Event Location Address
     *
     * @since 2.4.7
     *
     * @access public
     * @var    $location_Address string
     */
    public $location_Address;

    /**
     * Event Location Address2
     *
     * @since 2.4.7
     *
     * @access public
     * @var    $location_Address2 string
     */
    public $location_Address2;

    /**
     * Event Location City
     *
     * @since 2.4.7
     *
     * @access public
     * @var    $location_City string
     */
    public $location_City;

    /**
     * Event Location State Provo Code
     *
     * @since 2.4.7
     *
     * @access public
     * @var    $location_StateProvCode string
     */
    public $location_StateProvCode;

    /**
     * Event Location Postal/Zip Code
     *
     * @since 2.4.7
     *
     * @access public
     * @var    $location_PostalCode string
     */
    public $location_PostalCode;

    /**
     * Shortcode Attributes
     *
     * @since 2.4.7
     *
     * @access protected
     * @var    $atts array The shortcode atts from shortcode out of which Event is instantiated
     */
    protected $atts;

    /**
     * Class Name Link object for display in schedules.
     *
     * @since  2.4.7
     * @access public
     * @var    string    $sDate    'Y/m/d' date string for MBO link.
     */
    public $sDate;

    /**
     * Class Name Link object for display in schedules.
     *
     * @since  2.4.7
     * @access public
     * @var    int    $siteID    MBO Account for this event.
     */
    public $siteID;

    /**
     * Link object for display in schedules.
     *
     * @since  2.4.7
     * @access public
     * @var    string    $mbo_url    URL to event on MBO site.
     */
    public $mbo_url;

    /**
     * Class Name Link object for display in schedules.
     *
     * @since  2.4.7
     * @access public
     * @var    HTML object     $class_name_link    Link to be rendered with ->build() method.
     */
    public $class_name_link;

    /**
     * Class Name Link object for display in schedules.
     *
     * @since  2.4.7
     * @access public
     * @var    HTML object     $staff_name_link    Link to be rendered with ->build() method.
     */
    public $staff_name_link;

    /**
     * Class Name Link object for display in schedules.
     *
     * @since  2.4.7
     * @access public
     * @var    HTML object    $sign_up_link    Link to be rendered with ->build() method.
     */
    public $sign_up_link;

    /**
     * Populate attributes with data from MBO
     *
     * @since 2.4.7
     *
     * @access public
     * @param  array $event array of class/event attributes.
     * @param  array $atts  array of shortcode attributes from calling shortcode.
     */
    public function __construct( $event, $atts = array() )
    {

        $this->class_schedule_id = $event['ClassScheduleId'];
        $this->startDateTime     = $event['StartDateTime'];
        $this->endDateTime       = $event['EndDateTime'];
        $this->className         = $event['ClassDescription']['Name'];
        $this->ID                = $event['ClassDescription']['Id'];

        $this->FirstName = $event['Staff']['FirstName'];
        $this->LastName  = $event['Staff']['LastName'];
        // Set Staff Name up.
        // First set first, last with default to blank string
        $this->staffName = isset($this->FirstName) ? $this->FirstName . ' ' . $this->LastName : '';
        // If "Name" has been set, use that
        if (isset($event['Staff']['Name']) ) {
            $this->staffName = $event['Staff']['Name'];
        }
        $this->staffImage             = $event['Staff']['ImageURL'];
        $this->staffBio               = $event['Staff']['Bio'];
        $this->Description            = $event['ClassDescription']['Description'];
        $this->classImage             = $event['ClassDescription']['ImageURL'];
        $this->location_ID            = $event['Location']['Id'];
        $this->location_Name          = $event['Location']['Name'];
        $this->location_Address       = $event['Location']['Address'];
        $this->location_Address2      = $event['Location']['Address2'];
        $this->location_City          = $event['Location']['City'];
        $this->location_StateProvCode = $event['Location']['StateProvCode'];
        $this->location_PostalCode    = $event['Location']['PostalCode'];
        $this->start_date             = wp_date(Core\MzMindbodyApi::$date_format, strtotime($event['StartDateTime']));
        $this->start_time             = wp_date(Core\MzMindbodyApi::$time_format, strtotime($event['StartDateTime']));

        // Leave end_date blank if same as start day
        $maybe_end_date        = wp_date(Core\MzMindbodyApi::$date_format, strtotime($event['EndDateTime']));
        $this->end_date        = ( $this->start_date == $maybe_end_date ) ? '' : $maybe_end_date;
        $this->end_time        = wp_date(Core\MzMindbodyApi::$time_format, strtotime($event['EndDateTime']));
        $this->atts            = $atts;
        $this->siteID          = ! empty($atts['account']) ? $atts['account'] : Core\MzMindbodyApi::$basic_options['mz_mindbody_siteID'];
        $this->mbo_url         = $this->mbo_url();
        $this->class_name_link = $this->eventLinkMaker('class');
        $this->staff_name_link = $this->eventLinkMaker('staff');
        $this->sign_up_link    = $this->eventLinkMaker('signup');
    }

    private function eventLinkMaker( $type = 'class' )
    {
        $class_name_link = new Library\HtmlElement('a');
        $class_name_link->set('href', NS\PLUGIN_NAME_URL . 'src/Frontend/views/modals/modal_descriptions.php');
        $linkArray = array();
        switch ( $type ) {
        case 'staff':
            $linkArray['data-staffImage'] = ( $this->staffImage != '' ) ? $this->staffImage : '';
            $linkArray['data-staffBio']   = ( $this->staffBio != '' ) ? $this->staffBio : '';
            $linkArray['text']            = $this->staffName;
            $linkArray['data-staffName']  = $this->staffName;
            $linkArray['data-target']     = 'mzStaffScheduleModal';
            $linkArray['class']           = 'modal-toggle ' . sanitize_html_class($this->staffName, 'mz_staff_name');
            break;

        case 'signup':
            $linkArray['class'] = 'btn btn-primary';

            $linkArray['text'] = __('Sign-Up', 'mz-mindbody-api');

            $linkArray['data-time'] = wp_date(Core\MzMindbodyApi::$date_format . ' ' . Core\MzMindbodyApi::$time_format, strtotime($this->startDateTime));

            if (( ! empty($this->atts['advanced']) 
                && ( $this->atts['advanced'] == '1' ) ) 
                || ( Core\MzMindbodyApi::$advanced_options['register_within_site'] == 'on') 
            ) :
                    $linkArray['data-target']    = 'mzSignUpModal';
                    $linkArray['data-nonce']     = wp_create_nonce('mz_signup_nonce');
                    $linkArray['data-siteID']    = $this->siteID;
                    $linkArray['data-classID']   = $this->ID;
                    $linkArray['data-className'] = $this->className;
                    $linkArray['data-staffName'] = $this->staffName;
                    $linkArray['data-location']  = $this->location_ID;
                    $class_name_link->set(
                        'href', 
                        NS\PLUGIN_NAME_URL . 'src/Frontend/views/modals/modal_descriptions.php'
                    );
                else :
                      $linkArray['target'] = '_blank';
                      $class_name_link->set('href', $this->mbo_url);
                endif;
            break;

        case 'class':
            $linkArray['data-className']        = $this->className;
            $linkArray['data-staffName']        = $this->staffName;
            $linkArray['data-classDescription'] = ( $this->Description != '' ) ? $this->Description : '';
            $linkArray['data-eventImage']       = ( $this->classImage != '' ) ? $this->classImage : '';
            $linkArray['text']                  = $this->className;
            $linkArray['data-target']           = 'mzDescriptionModal';
            $linkArray['class'] = 'modal-toggle ' . sanitize_html_class(
                $this->className,
                'mz_class_name'
            );
        }

        $class_name_link->set($linkArray);
        return $class_name_link;
    }

    /**
     * Generate MBO URL
     *
     * Create a URL for signing up for class.
     *
     * @return urlstring
     */
    private function mbo_url()
    {
        $mbo_link = 'https://clients.mindbodyonline.com/ws.asp';
        $mbo_link .= "?sDate={$this->sDate}";
        $mbo_link .= "&amp;sLoc={$this->location_ID}";
        $mbo_link .= "&amp;sType=7";
        $mbo_link .= "&amp;sclassid={$this->class_schedule_id}";
        $mbo_link .= "&amp;studioid={$this->siteID}";
        return $mbo_link;
    }
}
