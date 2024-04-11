<?php
/**
 * Helpers
 *
 * This file contains the class which exposes various helper methods.
 *
 * @package MzMindbody
 */

namespace MZoo\MzMindbody\Common;

use MZoo\MzMindbody as NS;

/**
 * Class Helpers
 *
 * @package MzMindbody
 *
 * Helper methods used throughout the Mz Mindbody ecosystem.
 */
class Helpers {

    /**
     * Helper function to print out arrays
     *
     * @since 1.0.0
     * @param mixed $message string|array|object which will be printed to screen (or console).
     */
    public function print( $message = '' ) {
        echo '<pre>';
        print_r( $message );
        echo '</pre>';
    }

    /**
     * Helper function to provide a base path for logging.
     *
     * @since 2.8.6
     */
    private function get_log_api_calls_path() {
        $path = WP_CONTENT_DIR;
        if ( ! empty( NS\Core\MzMindbodyApi::$advanced_options['log_api_calls_path'] ) ) {
            $path = NS\Core\MzMindbodyApi::$advanced_options['log_api_calls_path'];
        }
        return is_dir( $path ) && is_writable( $path ) ? $path : WP_CONTENT_DIR;
    }

    /**
     * Helper function to write strings or arrays to a file.
     *
     * @since 1.0.0
     *
     * Use like NS\MZMBO()->helpers->log( $message, $file_path );
     *
     * @param mixed  $message string|array|object the content to be written to file.
     * @param string $file_path optional path to write file to.
     */
    public function log( $message, $file_path = '' ) {
        $file_path = ( ( '' === $file_path ) || ! file_exists( $file_path ) ) ? $this->get_log_api_calls_path() . '/mz_mbo_arbitrary.log' : $file_path;
        $header    = gmdate( 'l dS \o\f F Y h:i:s A', strtotime( 'now' ) ) . "\t ";

        // Just keep up to seven days worth of data.
        if ( file_exists( $file_path ) ) {
            if ( time() - filemtime( $file_path ) >= 60 * 60 * 24 * 7 ) { // 7 days.
                unlink( $file_path );
            }
        }

        if ( is_array( $message ) || is_object( $message ) ) {
            $message = print_r( $message, true );
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
     * @since 2.4.7
     *
     * @param mixed  $message string|object|array the content to be written to file.
     *
     */
    public function api_log( $message) {
        /* Time returns seconds since 1970.
        Divide by number of seconds in a week */
        $this_week = (int) (time() / (7 * 24 * 60 * 60)); // Seven days of 24 hours of 60 minutes etc...
        $file_path = $this->get_log_api_calls_path() . '/' . $this_week . '_mbo_api.log';

        $header   = gmdate( 'Ymd G:i:s', strtotime( 'now' ) ) . "\t ";
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
     * Used in deactivation hook to clear all log files.
     */
    public function clear_log_files() {
        $log_path = $this->get_log_api_calls_path();
        $files    = array(
            $this->get_log_api_calls_path() . '/mz_mbo_arbitrary.log',
            $this->get_log_api_calls_path() . '/mbo_api.log',
        );
        foreach ( $files as $file ) {
            if ( file_exists( $file ) ) {
                unlink( $file );
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
    public function delete_old_sessions() {

        $session_utils = new NS\Libraries\WP_Session\WP_Session_Utils();
        $count         = $session_utils->count_sessions();
        $session_utils->delete_old_sessions();
        return 'Cleared ' . $count . 'sessions.';
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
     * @param  string $bio Biography returned by MBO.
     * @access public
     * @return string $bio Cleaned up HTML string.
     */
    public function prepare_html_string( $bio ) {

        // Remove empty tags.
        $bio = str_replace( '/<[^\/>]*>(\s|xC2xA0|&nbsp;)*<\/[^>]*>/', '', $bio );
        $bio = $this->str_left_replace( '</p>', '', $bio );
        $bio = $this->str_left_replace( '</div>', '', $bio );
        if ( '<p>' === substr( $bio, 0, 3 ) ) {
            $mz_staff_bio = substr( $bio, 3 );
        } elseif ( '<div>' === substr( $bio, 0, 5 ) ) {
            $bio          = substr( $bio, 5 );
            $mz_staff_bio = substr( $bio, 3 );
        } elseif ( '<span>' === substr( $bio, 0, 5 ) ) {
            $bio = substr( $bio, 6 );
        }
        return htmlentities( $bio );
    }


    /**
     * Use array_map on multidimensional arrays
     *
     * @since   2.5.9
     * @source: https://stackoverflow.com/a/39637749/2223106
     *
     * @access public
     *
     * @param function $callback The function to run on each array element.
     * @param array    $array    to be operated on.
     * @return array $array, with each item operated on by callback.
     */
    public function array_map_recursive( $callback, $array ) {
        $func = function ( $item ) use ( &$func, &$callback ) {
            return is_array( $item ) ? array_map( $func, $item ) : call_user_func( $callback, $item );
        };

        return array_map( $func, $array );
    }

    /**
     * String Left Replace
     *
     * Replace the last occurrence of a string with another string in a string.
     *
     * source: https://stackoverflow.com/a/3835653/2223106
     *
     * Used by prepare_html_string above.
     *
     * @access private
     * @param string $search String to find.
     * @param string $replace String to replace.
     * @param string $subject String to search within.
     */
    private function str_left_replace( $search, $replace, $subject ) {
        $pos = strrpos( $subject, $search );

        if ( false !== $pos ) {
            $subject = substr_replace( $subject, $replace, $pos, strlen( $search ) );
        }

        return $subject;
    }

    /**
     * Log Caller
     *
     * Log calling function to mz_arbitrary_log file.
     *
     * @param object $trace Expecting debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 2 )[1].
     */
    public function log_caller( $trace ) {
        $caller_details = $trace['function'];
        if ( isset( $trace['class'] ) ) {
            $caller_details .= ' in:' . $trace['class'];
        }
        $this->log( print_r( $caller_details, true ) . ' caller:' . $caller_details );
    }
}
