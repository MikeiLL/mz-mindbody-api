<?php
namespace MZ_Mindbody\Inc\Session;

use MZ_Mindbody as NS;
use MZ_Mindbody\Inc\Core as Core;
use MZ_Mindbody\Inc\Common as Common;
use MZ_Mindbody\Inc\Common\Interfaces as Interfaces;

class Display extends Interfaces\ShortCode_Loader
{

    public function handleShortcode($atts, $content = null)
    {

        ob_start();

        echo "Hello, Pooh.";

        $cart = array('tigger' => 'was bouncy');

        NS\MZMBO()->session->clear();

        mz_pr(NS\MZMBO()->session->get( 'MBO_Nothing'));

        NS\MZMBO()->session->set( 'MBO_Nothing', $cart );

        mz_pr(NS\MZMBO()->session->get( 'MBO_Nothing'));

        return ob_get_clean();
    }



}

?>
