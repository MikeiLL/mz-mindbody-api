<?php
/**
 * Class MZMBO_UnitTestCase
 *
 * @package Mz_Mindbody_Api
 */

/**
 * Add a logging method to WP UnitTestCase Class.
 */
class MZMBO_UnitTestCase extends WP_UnitTestCase
{
    public function el($message){
        file_put_contents('./log_'.date("j.n.Y").'.log', $message, FILE_APPEND);
    }
}