<?php
namespace MZ_Mindbody\Inc\Session;

use MZ_Mindbody;
use MZ_Mindbody\Inc\Core as Core;
use MZ_Mindbody\Inc\Common as Common;
use MZ_Mindbody\Inc\Common\Interfaces as Interfaces;

class Display extends Interfaces\ShortCode_Loader
{

    public function handleShortcode($atts, $content = null)
    {

        ob_start();

        echo "Hello, Pooh.";

        $cart = array('tigger' => 'was here');

        MZ_Mindbody\MZMBO()->session->set( 'mzmbo_cart', $cart );

        mz_pr(MZ_Mindbody\MZMBO()->session->get( 'mzmbo_cart'));

        return ob_get_clean();
    }



}

?>
