<?php

namespace MZ_Mindbody\Inc\Common;

/**
 * Class Global_Strings
 * @package MZ_Mindbody\Inc\Common
 *
 * Store i18n strings that may be used throughout plugin
 */

class Helpers {

    /**
     * Helper function to print out arrays
     *
     * @since     1.0.0
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
     */
    public function log($message, $file_path='')
    {
        $file_path = ( ($file_path == '') || !file_exists($file_path) ) ? WP_CONTENT_DIR . '/mz_mbo_arbitrary.log' : $file_path;
        $header = date('l dS \o\f F Y h:i:s A', strtotime("now")) . "\t ";

        // Just keep seven days worth of data
        if (time() - filemtime($file_path) >= 60 * 60 * 24 * 7) { // 7 days
            unlink($file_path);
        }

        if (is_array($message)) {
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
     * @since     1.0.0
     */
    public function api_log($message, $file_path='')
    {
        $file_path = ( ($file_path == '') || !file_exists($file_path) ) ? WP_CONTENT_DIR . '/mbo_api.log' : $file_path;

        // Just keep seven days worth of data
        if (time() - filemtime($file_path) >= 60 * 60 * 24 * 7) { // 7 days
            unlink($file_path);
        }
        $header = date('Ymd G:i:s', strtotime("now")) . "\t ";
        $message .= "\n";

        file_put_contents(
            $file_path,
            $header . $message,
            FILE_APPEND | LOCK_EX
        );
    }


}
?>