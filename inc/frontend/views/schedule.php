<?php
use MZ_Mindbody\Inc\Core;

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://mzoo.org
 * @since      1.0.0
 *
 * @author    Mike iLL/mZoo.org
 */

?>

<h2>Hi there</h2>

<?php mz_pr($data->schedule['GetClassesResult']['Classes']['Class'][0]); ?>

<?php


$temp = get_option('mz_mindbody_options','Error: No Options');
mz_pr($temp);
mz_pr(MZ_Mindbody\Inc\Core\Init::$basic_options);
die();
?>
