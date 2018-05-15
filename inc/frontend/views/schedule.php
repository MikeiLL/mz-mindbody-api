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

<table>
    <?php foreach ($data->horizontal_schedule as $day => $classes): ?>
    <tr>
        <td>
            <?php echo date_i18n($data->date_format, strtotime($day)); ?>
        </td>
    </tr>
        <?php foreach ($classes as $k => $class): ?>
    <tr>
        <td>
            <?php mz_pr($class); ?>
        </td>
    </tr>
        <?php endforeach; ?>
    <?php endforeach; ?>
</table>

