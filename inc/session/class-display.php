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

        return ob_get_clean();
    }



}

?>
