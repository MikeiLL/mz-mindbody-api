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

    function log($message, $file_path='')
    {
        $file_path = ( ($file_path == '') || !file_exists($file_path) ) ? WP_CONTENT_DIR . '/mbo_api.log' : $file_path;
        $header = date('l dS \o\f F Y h:i:s A', strtotime("now")) . "\t ";

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
}


}
?>