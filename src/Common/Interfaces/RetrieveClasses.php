<?php
/**
 * Retrieve Classes
 *
 * This file contains the class that extends Retrieve, specifically
 * for fetching Mindbody "classes."
 *
 * @package MzMindbody
 */

namespace MZoo\MzMindbody\Common\Interfaces;

use MZoo\MzMindbody\Core as Core;
use MZoo\MzMindbody\Libraries as Libraries;
use MZoo\MzMindbody\Schedule as Schedule;
use MZoo\MzMindbody as NS;

/**
 * Class that is extended for Schedule Display Shortcode(s)
 *
 * @param @type string $time_format Format string for php strtotime function Default: "g:i a"
 * @param @type array OR numeric $locations Single or list of MBO location numerals Default: 1
 * @param @type boolean $hide_cancelled Whether or not to display cancelled classes. Default: 0
 * @param @type array $hide Items to be removed from calendar
 * @param @type boolean $advanced Whether or not allowing online class sign-up via plugin
 * @param @type boolean $show_registrants Whether or not to display class registrants in modal popup
 * @param @type boolean $registrants_count  Whether we want to show count of registrants in a class (TODO - finish) @default: 0
 * @param @type string $calendar_format Depending on final display, we may create items in Single_event class differently.
 *                                                                          Default: 'horizontal'
 * @param @type boolean $delink Make class name NOT a link
 * @param @type string $class_type MBO API has 'Enrollment' and 'Class'. 'Enrolment' is a "workdhop". Default: 'Enrollment'
 * @param @type numeric $account Which MBO account is being interfaced with.
 * @param @type boolean $this_week If true, show only week from today.
 */
abstract class RetrieveClasses extends Retrieve {



    /**
     * Date Format for php date display
     *
     * @since  2.4.7
     * @access public
     * @var string $date_format php date format string.
     */

    public $date_format;

    /**
     * Time format for php time display
     *
     * @since  2.4.7
     * @access public
     * @var string $time_format php format string.
     */
    public $time_format;

    /**
     * This Week
     *
     * @since  2.4.7
     * @access public
     * @var bool $this_week If true, show only week from today.
     */
    public $this_week;

    /**
     * Schedule array sorted by first date then time.
     *
     * Used in horizontal schedule display.
     *
     * @since  2.4.7
     * @access public
     * @var    array $classes_by_date_then_time
     */
    public $classes_by_date_then_time;

    /**
     * Schedule array sorted by time, then date
     *
     * Used in grid schedule display.
     *
     * @since  2.4.7
     * @access public
     * @var    array $classes_by_time_then_date
     */
    public $classes_by_time_then_date;

    /**
     * Classes
     *
     * @since  2.4.7
     * @access public
     * @var array $classes as returned from MBO.
     */
    public $classes;

    /**
     * All locations included in current schedule
     *
     * Used to filter by location via jQuery in display, also to
     * print location name if multiple locations shown in same schedule.
     * Key is MBO location ID and value is location name.
     *
     * @since  2.4.7
     * @access public
     * @var    array $locations_dictionary
     */
    public $locations_dictionary;

    /**
     * Attributes sent to shortcode.
     *
     * @since  2.4.7
     * @access public
     * @var    array    $atts    Shortcode attributes filtered via shortcode_atts().
     */
    public $atts;

    /**
     * MBO Account.
     *
     * Which MBO account to pull data from, default to Options setting, but can be overridden in shortcode
     *
     * @since  2.0.0
     * @access protected
     * @var    int    $mbo_account    Which MBO account to pull data from.
     */
    protected $mbo_account;

    /**
     * Holds the time frame for the instance.
     *
     * @since  2.4.7
     * @access public
     * @var    array    $time_frame    StartDateTime and end_datetime for MBO API call.
     */
    public $time_frame;

    /**
     * Holds the current day, with offset, based on "offset" attribute/parameter.
     *
     * Set by time_frame() and used by sortClassesByDateThenTime()
     *
     * @since  2.4.7
     * @access public
     * @var    string    $current_day_offset    Formatted Datetime object.
     */
    public $current_day_offset;

    /**
     * Holds the native MBO "schedule type".
     *
     * $class_type MBO API has native 'Enrollment' and 'Class'. 'Enrolment' is a "workshop". Default: 'Enrollment'
     *
     * @since  2.4.7
     * @access public
     * @var    array    $schedule_types    Array containing MBO "class types" to display.
     */
    public $schedule_types;

    /**
     * Holds the first date of current results.
     *
     * This is used to display current week in grid schedule.
     *
     * @assigned in time_frame() method
     *
     * @since  2.4.7
     * @access public
     * @var    Datetime object    $start_date    Datetime containing start of week requested.
     */
    public $start_date;

    /**
     * Holds the last date of current week.
     *
     * This is used to set end of current week in grid array sorting in sortClassesByTimeThenDate() method.
     *
     * @assigned in time_frame() method
     *
     * @since  2.4.7
     * @access public
     * @var    Datetime object    $current_week_end    Datetime containing start of week requested.
     */
    public $current_week_end;

    /**
     * Class Constructor
     *
     * @param array $atts The attributes from WordPress Post shortcode.
     */
    public function __construct( $atts = array( 'locations' => array( 1 ) ) ) {

        parent::__construct();
        $this->date_format               = Core\MzMindbodyApi::$date_format;
        $this->time_format               = Core\MzMindbodyApi::$time_format;
        $this->classes_by_date_then_time = array();
        $this->classes                   = array();
        $this->atts                      = $atts;
        if ( ! empty( Core\MzMindbodyApi::$basic_options['mz_mindbody_siteID'] ) ) :
            $this->mbo_account = ! empty( $atts['account'] ) ? $atts['account'] : Core\MzMindbodyApi::$basic_options['mz_mindbody_siteID'];
        else :
            $this->mbo_account = '-99';
        endif;
        $this->time_frame           = $this->time_frame(null, isset($atts['duration']) ? $atts['duration'] : '+6 day' );
        $this->locations_dictionary = array();

        // Schedule types default to 'Class' (vs Enrollment).
        $this->schedule_types = array( 'Class' );

        if ( ! empty( Core\MzMindbodyApi::$advanced_options['schedule_types'] ) ) {
            $this->schedule_types = Core\MzMindbodyApi::$advanced_options['schedule_types'];
        }

        // Allow shortcode to override global setting for schedule_types.
        if ( ! empty( $this->atts['schedule_types'] ) ) {
            $this->schedule_types = $this->atts['schedule_types'];
        }

    }


    /**
     * Get a timestamp, return data from MBO api, store it in a transient and
     * as object attribute.
     *
     * @since 2.4.7
     *
     * @param timestamp $timestamp defaults to current WP time.
     *
     * @return array of MBO schedule data
     */
    public function get_mbo_results( $timestamp = null ) {

        $timestamp = isset( $timestamp ) ? $timestamp : strtotime( wp_date( 'Y-m-d H:i:s' ) );
        $mb        = $this->instantiate_mbo_api();
        if ( ! $mb ) {
            return false;
        }

        /*
        Set array string based on if called from Events Object
        * or Schedule Object.
        *
        * SessionTypeIDs key only exists for Events display.
        */
        $sc_string        = ( array_key_exists( 'SessionTypeIDs', $this->time_frame ) ) ? 'get_events' : 'get_schedule';
        $transient_string = $this->generate_transient_name( $sc_string );
        if ( false === get_transient( $transient_string ) ) {
            // If there's not a transient already, call the API and create one.

            if ( 0 !== $this->mbo_account ) {
                // If account has been specified in shortcode, update credentials.
                $mb->source_credentials['SiteIDs'][0] = $this->mbo_account;
            }

            try {
                $schedule_data = $mb->GetClasses( $this->time_frame );
            } catch ( \Exception $e ) {
                NS\MZMBO()->helpers->print( $e->getMessage() );
                return false;
            }

            if ( empty( $schedule_data ) || empty( $schedule_data['Classes'][0]['Id'] ) ) :
                echo '<!-- ' . print_r( $schedule_data, true ) . ' --> ';
                return false;
            endif;
            if ( 0 === (int) $schedule_data['PaginationResponse']['TotalResults'] ) {
                return 'No Classes';
            }

            // Otherwise (if successful API call) assign result to $this->classes.
            $this->classes = $schedule_data['Classes'];

            // Store the transient for 12 hours or admin set duration.
            $transient_duration = 43200;
            if ( ! empty( Core\MzMindbodyApi::$advanced_options['schedule_transient_duration'] ) ) {
                $transient_duration = Core\MzMindbodyApi::$advanced_options['schedule_transient_duration'];
            }

            set_transient( $transient_string, $this->classes, $transient_duration );

        } else {

            $this->classes = get_transient( $transient_string );
        }

        return $this->classes;
    }

    /**
     * Get timestamp, return week start and end timestamps based
     * on WordPress start of week config.
     *
     * @since 2.4.7
     *
     * @param timestamp $timestamp which date to return week start and end of.
     *
     * @return array 'start', 'end' of current week in timestamps
     */
    public function singleWeek( $timestamp ) {
        return get_weekstartend(
            gmdate( 'Y-m-d H:i:s', $timestamp ),
            Core\MzMindbodyApi::$start_of_week
        );
    }

    /**
     * Return timestamp of seven days from now.
     *
     * @since 2.4.7
     * @deprecated 2.6.4
     *
     * @param timestamp $timestamp of some "now".
     * @return timestamp $timestamp of seven days from param.
     */
    public function seven_days_later( $timestamp ) {
        return strtotime( '+6 day', $timestamp );
    }

    /**
     * Displayable current week start and end timestamps.
     *
     * @since  2.4.7
     * @return html string of start and end of current week
     */
    public function currentWeekDisplay() {
        $time_frame = $this->singleWeek( strtotime( wp_date( 'Y-m-d H:i:s' ) ) );
        $return     = 'Week start: ' . gmdate( 'l, M d, Y', $time_frame[ start ] ) . '<br/>';
        $return    .= 'Week end: ' . gmdate( 'l, M d, Y', $time_frame[ end ] );
        return $return;
    }

    /**
     * Return an array of MBO Class Objects, ordered by date, then time.
     *
     * This is used in Horizontal view. It receives the filtered results from the MBO API call ($mz_classes)
     * and builds an array of Class Event Objects, sequenced by date and time.
     *
     * @return @type array of Objects from Single_event class, in Date (and time) sequence.
     */
    public function sortClassesByDateThenTime() {
        /*
         * When there is only a single event in the client
         * schedule, the 'Classes' array contains that event, but when there are multiple
         * visits then the array of events is under 'Events'/'Event'
         *
         * This may not be necessary
         * if (!empty($this->classes[0]['StartDateTime'])){
         * // Multiple events
         * $classes_array_scope = $this->classes[0];
         * } else {
         * $classes_array_scope = $this->classes;
         * }
         */

        foreach ( $this->classes as $class ) {

            // TODO Don't do this twice. Filter once for BOTH schedule displays.
            // Filter out some items.
            if ( $this->filterClass( $class ) === false ) {
                continue;
            }

            // Populate the Locations Dictionary.
            $this->populateLocationsDictionary( $class );

            // Make a timestamp of just the day to use as key for that day's classes.
            $just_date = wp_date(
                'Y-m-d',
                strtotime( $class['StartDateTime'] )
            );

            // If class was previous to today ignore it.
            if ( $just_date < $this->current_day_offset->format( 'Y-m-d' ) ) {
                continue;
            }

            /*
             * Create a new array with a key for each date YYYY-MM-DD
             * and corresponding value an array of class details.
             */
            $single_event = new Schedule\ScheduleItem( $class, $this->atts );
            if ( ! empty( $this->classes_by_date_then_time[ $just_date ] ) ) {
                array_push( $this->classes_by_date_then_time[ $just_date ], $single_event );
            } else {
                $this->classes_by_date_then_time[ $just_date ] = array( $single_event );
            }
        }
        /* They are not ordered by date so order them by date */
        ksort( $this->classes_by_date_then_time );
        foreach ( $this->classes_by_date_then_time as $class_date => &$classes ) {
            /*
            * $classes is an array of all classes for given date
            * Take each of the class arrays and order it by time
            * $classes_by_date_then_time should have a length of seven, one for
            * each day of the week.
            */
            usort(
                $classes,
                function ( $a, $b ) {

                    if ( $a->start_datetime === $b->start_datetime ) {
                        return 0;
                    }
                    return $a->start_datetime < $b->start_datetime ? -1 : 1;
                }
            );
        }
        // TODO Make padEmptyCalendarDays work.
        return $this->classes_by_date_then_time;
    }

    /**
     * Get Classes By Date Then Time and return, padded for any days without events.
     *
     * @since 2.6.4
     *
     * Currently not working because $this->start_date is start of week as per WP config.
     * Needs to be current day of week, but not necessarily THIS week.
     *
     * @param array $classes_by_date_then_time of sequenced classes.
     * @return array of classes, padded to a full seven days, wether or not classes exist
     */
    private function padEmptyCalendarDays( $classes_by_date_then_time ) {

        $week_of_dates = array( $this->start_date->format( 'Y-m-d' ) => '' );
        for ( $i = 1; $i < 7; $i++ ) {
            $week_of_dates[ $this->start_date->add( new \DateInterval( 'P1D' ) )->format( 'Y-m-d' ) ] = '';
        }
        return array_merge( $week_of_dates, $classes_by_date_then_time );
    }

    /**
     * Return an array of MBO Class Objects, ordered by date.
     *
     * This is used in Grid view. It gets the filtered results from the MBO API call and builds a matrix, top level of which is
     * seven arrays, one for each of seven days in a week (for a calendar column), each one of the Day columns contains an array
     * of Class Event objects, sequenced by time of day, earliest to latest. There may be multiple classes occurring at same time,
     * which are contained in another sub-array.
     *
     * @return @type array of Objects from Single_event class, in Date (and time) sequence.
     */
    public function sortClassesByTimeThenDate() {
        /*
         * When there is only a single event in the client
         * schedule, the 'Classes' array contains that event, but when there are multiple
         * visits then the array of events is under 'Events'/'Event'
         *
         * This may not be necessary
         * if (!empty($this->classes[0]['StartDateTime'])){
         * // Multiple events
         * $classes_array_scope = $this->classes[0];
         * } else {
         * $classes_array_scope = $this->classes;
         * }
         */
        foreach ( $this->classes as $class ) {
            // Filter out some items.
            if ( false === $this->filterClass( $class ) ) {
                continue;
            }

            // Populate the Locations Dictionary.
            $this->populateLocationsDictionary( $class );

            // Ignore classes that are not part of current week (ending Sunday).
            if ( gmdate( 'Y-m-d', strtotime( $class['StartDateTime'] ) ) > $this->current_week_end->format( 'Y-m-d' ) ) :
                continue;
            endif;

            // Ignore classes that are not part of current week (beginning Monday).
            if ( gmdate( 'Y-m-d', strtotime( $class['StartDateTime'] ) ) < $this->start_date->format( 'Y-m-d' ) ) :
                continue;
            endif;

            /*
            * Create a new array with a key for each TIME (time of day, not date)
            * and corresponding value an array of class details
            * for classes at that time.
            *
            */
            $class_time = gmdate( 'G.i', strtotime( $class['StartDateTime'] ) );

            // For numerical sorting.
            $single_event = new Schedule\ScheduleItem( $class, $this->atts );

            // If there's is already an array for this time slot, add to it.
            if ( ! empty( $this->classes_by_time_then_date[ $class_time ] ) ) {
                // Create a $single_event which is a "class" object, and start the classes array with it.
                array_push( $this->classes_by_time_then_date[ $class_time ]['classes'], $single_event );
            } else {
                // Assign the first element of this time slot.
                $display_time                                   = gmdate(
                    Core\MzMindbodyApi::$time_format,
                    strtotime( $class['StartDateTime'] )
                );
                $this->classes_by_time_then_date[ $class_time ] = array(
                    'display_time' => $display_time,
                    // Add part_of_day for filter as well.
                    'part_of_day'  => $single_event->part_of_day,
                    'classes'      => array( $single_event ),
                );
            }
        }
        // Timeslot keys in new array are not time-sequenced so do so.
        ksort( $this->classes_by_time_then_date );
        foreach ( $this->classes_by_time_then_date as $schedule_time => &$classes ) {
            /*
            * $classes is an array of all class_event objects for given time
            * Take each of the class arrays and order it by days 1-7.
            */
            usort(
                $classes['classes'],
                function ( $a, $b ) {

                    if ( gmdate( 'N', strtotime( $a->start_datetime ) ) === gmdate( 'N', strtotime( $b->start_datetime ) ) ) {
                        return 0;
                    }
                    return $a->start_datetime < $b->start_datetime ? -1 : 1;
                }
            );
            $classes['classes'] = $this->week_of_timeslot( $classes['classes'], 'day_num' );
        }

        return $this->classes_by_time_then_date;
    }



    /**
     * Week of Timeslot
     *
     * Make a clean array with seven corresponding slots and populate
     * based on indicator (day) for each class. There may be more than
     * one even for each day and empty arrays will represent empty time slots.
     *
     * @param array  $classes returned from MBO.
     * @param string $indicator for example 'day_num'.
     */
    private function week_of_timeslot( $classes, $indicator ) {
        $seven_days = array_combine(
            range( 1, 7 ),
            array(
                array(),
                array(),
                array(),
                array(),
                array(),
                array(),
                array(),
            )
        );
        foreach ( $seven_days as $key => $value ) {
            foreach ( $classes as $class ) {
                if ( (int) $class->$indicator === $key ) {
                    array_push( $seven_days[ $key ], $class );
                }
            }
        }
        return $seven_days;
    }

    /**
     * Filter out Classes that we don't want.
     *
     * @param  array $class single class as returned from MBO.
     * @return boolean
     */
    protected function filterClass( $class ) {

        if ( ( ! in_array( (int) $class['Location']['Id'], array_map( 'intval', $this->atts['locations'] ), true ) )
            || ( ! in_array( $class['ClassDescription']['Program']['ScheduleType'], $this->schedule_types, true ) )
        ) {
            return false;
        }

        if ( ! empty( $this->atts['session_types'] ) ) {
            if ( ! in_array( $class['ClassDescription']['SessionType']['Name'], $this->atts['session_types'], true ) ) {
                return false;
            }
        }
        // Support old "class_types" shortcode att.
        if ( ! empty( $this->atts['class_types'] ) ) {
            if ( ! in_array( $class['ClassDescription']['SessionType']['Name'], $this->atts['class_types'], true ) ) {
                return false;
            }
        }
        // If shortcode not set to hide classes that are cancelled.
        if ( ! empty( $this->atts['hide_cancelled'] ) ) {
            if ( 1 === (int) $class['IsCanceled'] ) {
                return false;
            }
        }

        // Uncomment to view date in browser.
        // NS\MZMBO()->helpers->print(wp_date(Core\MzMindbodyApi::$date_format, strtotime($class['StartDateTime'])));.

        return true;
    }

    /**
     * Populate Locations Dictionary
     *
     * Populate the objects Locations Dictionary, which will be used to create Location links
     * as well as to populate the Filter on schedules which filter multiple locations.
     *
     * @param array $class a single "class" returned from MBO API.
     */
    protected function populateLocationsDictionary( $class ) {
        // We only need to do this once for each location.
        if ( count( $this->locations_dictionary ) === count( $this->atts['locations'] ) ) {
            return;
        }
        // Build a link TODO use HTML Element Class.
        $location_name         = $class['Location']['Name'];
        $location_name_css     = sanitize_html_class( $location_name, 'mz_location_class' );
        $location_address      = $class['Location']['Address'];
        $location_address2     = $class['Location']['Address2'];
        $url_encoded_address   = urlencode( $location_address . $location_address2 );
        $location_name_display = '<span class="location_name ' . $location_name_css . '"><a href="http://maps.google.com/maps?q=' . $url_encoded_address . '" target="_blank" title="' . $location_address . '">' . $location_name . '</a>';
        if ( ! array_key_exists( $class['Location']['Id'], $this->locations_dictionary ) ) :
            $this->locations_dictionary[ $class['Location']['Id'] ] = array(
                'name'  => $location_name,
                'link'  => $location_name_display,
                'class' => preg_replace( '/\W+/', '-', strtolower( strip_tags( $location_name ) ) ),
            );
        endif;
    }

    /**
     * Set up Time Frame with Start and End times for Schedule Request
     *
     * @since 2.4.7
     * @param timestamp $timestamp on which to begin timeframe.
     */
    abstract public function time_frame( $timestamp);
}
