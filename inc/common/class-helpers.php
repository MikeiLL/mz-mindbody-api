<?php

namespace MZ_Mindbody\Inc\Common;

/**
 * Class Global_Strings
 * @package MZ_Mindbody\Inc\Common
 *
 * Store i18n strings that may be used throughout plugin
 *
 * Using Singleton you can call helpers like NS\MZMBO()->helpers->mz_pr($param);
 *
 */

class Helpers {

    /**
     * Helper function to print out arrays
     *
     * @since     1.0.0
     * @param $message string or array which will be printed to screen (or console)
     */
    public function mz_pr($message = '')
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
    public function log($message, $file_path='')
    {
        $file_path = ( ($file_path == '') || !file_exists($file_path) ) ? WP_CONTENT_DIR . '/mz_mbo_arbitrary.log' : $file_path;
        $header = date('l dS \o\f F Y h:i:s A', strtotime("now")) . "\t ";

        // Just keep up to seven days worth of data
        if (file_exists($file_path)){
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
    public function api_log($message, $file_path='')
    {
        $file_path = ( ($file_path == '') || !file_exists($file_path) ) ? WP_CONTENT_DIR . '/mbo_api.log' : $file_path;

        // Just keep up to seven days worth of data
        if (file_exists($file_path)){
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
    public function clear_log_files(){
        $files = array(
            WP_CONTENT_DIR . '/mz_mbo_arbitrary.log',
            WP_CONTENT_DIR . '/mbo_api.log'
        );
        foreach ($files as $file){
            if (file_exists($file)){
                unlink($file);
            }
        }
    }

    /**
     * Clean up staff biography
     *
     * Remove empty HTML tags as well as the opening container tag (div, p or span) so we
     * can rebuild the bio with Image Thumbnail inside of the container with the text.
     * Also replace content with html entities.
     *
     * @since 2.4.7
     * @param string Biography returned by MBO
     * @access public
     * @return string $bio Cleaned up HTML string.
     */
    public function prepare_html_string($bio){
    print_r("BIO: ");
    print_r($bio);
        // Remove empty tags
        $bio = str_replace("/<[^\/>]*>(\s|xC2xA0|&nbsp;)*<\/[^>]*>/", '', $bio);
        $bio = $this->str_last_replace("</p>", "", $bio);
        $bio = $this->str_last_replace("</div>", "", $bio);
        if (substr($bio, 0, 3) == '<p>') {
            $mz_staff_bio = substr($bio, 3);
        } else if (substr($bio, 0, 5) == '<div>'){
            $bio = substr($bio, 5);
            $mz_staff_bio = substr($bio, 3);
        } else if (substr($bio, 0, 5) == '<span>'){
            $bio = substr($bio, 6);
        }
        return htmlentities($bio);
    }

    /**
     * String Left Replace
     *
     * source: https://stackoverflow.com/a/3835653/2223106
     *
     * @access private
     */
    private function str_last_replace($search, $replace, $subject)
    {
        $pos = strrpos($subject, $search);

        if($pos !== false)
        {
            $subject = substr_replace($subject, $replace, $pos, strlen($search));
        }

        return $subject;
    }


}
?>