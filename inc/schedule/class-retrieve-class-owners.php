<?php
namespace MZ_Mindbody\Inc\Schedule;

use MZ_Mindbody;
use MZ_Mindbody\Inc\Core as Core;
use MZ_Mindbody\Inc\Common as Common;
use MZ_Mindbody\Inc\Common\Interfaces as Interfaces;

class Retrieve_Class_Owners extends Interfaces\Retrieve_Classes {

	/**
	 * Return Time Frame for request to MBO API
	 *
	 * @since 2.4.7
	 *
	 * Default time_frame is two dates, start of current week as set in WP, and four weeks from now.
	 *
	 * @return array or start and end dates as required for MBO API
	 */
	public function time_frame($timestamp = null){
		$start_time = new \Datetime( date_i18n('Y-m-d', current_time( 'timestamp' )) );
		$end_time = new \Datetime( date_i18n('Y-m-d', current_time( 'timestamp' )) );
        $di = new \DateInterval('P4W');
        $end_time->add($di);

		return array('StartDateTime'=> $start_time->format('Y-m-d'), 'EndDateTime'=> $end_time->format('Y-m-d'));
	}

	public function deduce_class_owners(){
        check_ajax_referer($_REQUEST['nonce'], "mz_admin_nonce", false);

        if (false == $this->get_mbo_results()) echo "<div>" . __("Mindbody plugin settings error.", 'mz-mindbody-api') . "</div>";

        ob_start();
        $result['type'] = 'success';

        $owners = $this->populate_regularly_scheduled_classes('message');

        mz_pr($owners);

        $result['message'] = ob_get_clean();

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $result = json_encode($result);
            echo $result;
        } else {
            header("Location: " . $_SERVER["HTTP_REFERER"]);
        }

        wp_die();
    }

    /**
     * Populate Matrix of Regularly Scheduled Classes
     *
     * This generates an array, saved as a transient, used when a Class Instructor is "Substitute" to see who the regular teacher is. It's an
     * ugly approach to determining which classes are unique and assigned to a particular instructor.
     *
     * The array is a matrix of these:
     * [59382] => Array
     *      (
     *      [class_name] => GX | Barre
     *      [class_description] => <div><strong>Born of the original Lotte
     *      [class_owner] => Darice Balamotti
     *      [class_owner_id] => 117
     *      [image_url] => https://clients.mindbodyonline.com/studios/Intensity/reservations/5.jpg
     *      [time] => 16:16
     *      [location] => 1
     *      [day] => Array
     *          (
     *          )
     * )
     *
     * @param string $message Whether or not to echo a message and return the resulting array
     */
    public function populate_regularly_scheduled_classes($message='no message') {
        $class_owners = array();
        $class_count = 0;

        $schedules = $this->classes['GetClassesResult']['Classes'];

        foreach($schedules as $schedule):
            foreach($schedule as $class):

                $class_count++;

                // Initialize array
                $day_of_class = array();

                $classStartTime = new \DateTime($class['StartTime'], Core\Init::$timezone);

                if (isset($class['DaySunday']) && ($class['DaySunday'] == 1)):
                    $day_of_class['Sunday'] = 1;
                endif;
                if (isset($class['DayMonday']) && ($class['DayMonday'] == 1)):
                    $day_of_class['Monday'] = 1;
                endif;
                if (isset($class['DayTuesday']) && ($class['DayTuesday'] == 1)):
                    $day_of_class['Tuesday'] = 1;
                endif;
                if (isset($class['DayWednesday']) && ($class['DayWednesday'] == 1)):
                    $day_of_class['Wednesday'] = 1;
                endif;
                if (isset($class['DayThursday']) && ($class['DayThursday'] == 1)):
                    $day_of_class['Thursday'] = 1;
                endif;
                if (isset($class['DayFriday']) && ($class['DayFriday'] == 1)):
                    $day_of_class['Friday'] = 1;
                endif;
                if (isset($class['DaySaturday']) && ($class['DaySaturday'] == 1)):
                    $day_of_class['Saturday'] = 1;
                endif;

                $class_image = isset($class['ClassDescription']['ImageURL']) ? $class['ClassDescription']['ImageURL'] : '';

                $image_path_array = explode('?imageversion=', $class_image);

                $class_description_array = explode(" ", $class['ClassDescription']['Description']);

                $class_description_substring = implode(" ", array_splice($class_description_array, 0, 5));

                $class_owners[$class['ID']] = array(
                    'class_name' => strip_tags($class['ClassDescription']['Name']),
                    'class_description' => $class_description_substring,
                    'class_owner' => strip_tags($class['Staff']['Name']),
                    'class_owner_id' => strip_tags($class['Staff']['ID']),
                    'image_url' => array_shift($image_path_array),
                    'time' => $classStartTime->format('H:i'),
                    'location' => $class['Location']['ID'],
                    'day' => $day_of_class
                );

            endforeach;
        endforeach;

        delete_transient('mz_class_owners');
        set_transient('mz_class_owners', $class_owners, 60 * 60 * 24 * 7);

        // mz_pr(array_shift($class_owners));
        if($message == 'message'):
            echo __('Classes and teachers as regularly scheduled reloaded.', 'mz-mindbody-api');
            return $class_owners;
        endif;
    }



}