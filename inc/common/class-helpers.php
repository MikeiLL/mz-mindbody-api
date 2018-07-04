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

}
?>