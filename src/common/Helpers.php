<?php

namespace MzMindbody\Inc\Common;

use MzMindbody as NS;

/**
 * Class GlobalStrings
 * @package MzMindbody\Inc\Common
 *
 * Store i18n strings that may be used throughout plugin
 *
 * Using Singleton you can call helpers like NS\MZMBO()->helpers->print($param);
 *
 */

class Helpers
{

    /**
     * Helper function to print out arrays
     *
     * @since     1.0.0
     * @param $message string or array which will be printed to screen (or console)
     */
    public function print($message = '')
    {
        echo "<pre>";
        print_r($message);
        echo "</pre>";
    }

    /**
     * Helper function to write strings or arrays to a file
     *
     * @since     1.0.0
     *
     * @param $message the content to be written to file.
     * @param $file_path string optional path to write file to.
     */
    public function log($message, $file_path = '')
    {
        $file_path = ( ($file_path == '') || !file_exists($file_path) ) ? WP_CONTENT_DIR . '/mz_mbo_arbitrary.log' : $file_path;
        $header = date('l dS \o\f F Y h:i:s A', strtotime("now")) . "\t ";

        // Just keep up to seven days worth of data
        if (file_exists($file_path)) {
            if (time() - filemtime($file_path) >= 60 * 60 * 24 * 7) { // 7 days
                unlink($file_path);
            }
        }

        if (is_array($message) || is_object($message)) {
            $message = print_r($message, true);
        }
        $message .= "\n";
        file_put_contents(
            $file_path,
            $header . $message,
            FILE_APPEND | LOCK_EX
        );
    }

    /**
     * Helper function to log api calls to a file
     *
     * @since     2.4.7
     *
     * @param $message the content to be written to file.
     * @param $file_path string optional path to write file to.
     */
    public function apiLog($message, $file_path = '')
    {
        $file_path = ( ($file_path == '') || !file_exists($file_path) ) ? WP_CONTENT_DIR . '/mbo_api.log' : $file_path;

        // Just keep up to seven days worth of data
        if (file_exists($file_path)) {
            if (time() - filemtime($file_path) >= 60 * 60 * 24 * 7) { // 7 days
                unlink($file_path);
            }
        }

        $header = date('Ymd G:i:s', strtotime("now")) . "\t ";
        $message .= "\n";

        file_put_contents(
            $file_path,
            $header . $message,
            FILE_APPEND | LOCK_EX
        );
    }

    /**
     * Clear log files
     *
     * Used in deactivation hook to clear all log files
     */
    public function clearLogFiles()
    {
        $files = array(
            WP_CONTENT_DIR . '/mz_mbo_arbitrary.log',
            WP_CONTENT_DIR . '/mbo_api.log'
        );
        foreach ($files as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }


    /**
     * Delete Old Sessions
     *
     * @since 2.5.8
     * Sometimes, particularly in development, when creating and clearing
     * sessions over and over again, the session class seems to get bogged
     * down.
     */
    public function deleteOldSessions()
    {

        $session_utils = new NS\Inc\Libraries\WP_Session\WP_Session_Utils();
        $count = $session_utils->count_sessions();
        $session_utils->deleteOldSessions();
        return "Cleared " . $count . "sessions.";
    }


    /**
     * Clean up staff biography
     *
     * Remove empty HTML tags as well as the opening container tag (div, p or span) so we
     * can rebuild the bio with Image Thumbnail inside of the container with the text.
     * Also replace content with html entities.
     *
     * @since 2.4.7
     *
     * @depreciated Don't seem to need with v6 API,
     * but still using in staff biography.
     *
     * @param string Biography returned by MBO
     * @access public
     * @return string $bio Cleaned up HTML string.
     */
    public function prepareHtmlString($bio)
    {

        // Remove empty tags
        $bio = str_replace("/<[^\/>]*>(\s|xC2xA0|&nbsp;)*<\/[^>]*>/", '', $bio);
        $bio = $this->strLastReplace("</p>", "", $bio);
        $bio = $this->strLastReplace("</div>", "", $bio);
        if (substr($bio, 0, 3) == '<p>') {
            $mz_staff_bio = substr($bio, 3);
        } elseif (substr($bio, 0, 5) == '<div>') {
            $bio = substr($bio, 5);
            $mz_staff_bio = substr($bio, 3);
        } elseif (substr($bio, 0, 5) == '<span>') {
            $bio = substr($bio, 6);
        }
        return htmlentities($bio);
    }


    /**
     * Use array_map on multidimensional arrays
     *
     * @since 2.5.9
     * @source: https://stackoverflow.com/a/39637749/2223106
     *
     *
     * @param function $callback
     * @param array $array to be operated on
     *
     * @access public
     * @return array $array, with each item operated on by callback.
     */
    public function arrayMapRecursive($callback, $array)
    {
        $func = function ($item) use (&$func, &$callback) {
            return is_array($item) ? array_map($func, $item) : call_user_func($callback, $item);
        };

        return array_map($func, $array);
    }

    /**
     * String Left Replace
     *
     * source: https://stackoverflow.com/a/3835653/2223106
     *
     * @access private
     */
    private function strLastReplace($search, $replace, $subject)
    {
        $pos = strrpos($subject, $search);

        if ($pos !== false) {
            $subject = substr_replace($subject, $replace, $pos, strlen($search));
        }

        return $subject;
    }
}
