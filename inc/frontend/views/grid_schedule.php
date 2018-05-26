<?php

use MZ_Mindbody\Inc\Core;
use MZ_Mindbody\Inc\Libraries as Libraries;

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://mzoo.org
 * @since      2.4.7
 *
 * @author    Mike iLL/mZoo.org
 */

?>
<table class="<?php echo $data->grid_class; ?>">
    <tr>
        <th scope="header"></th>
        <?php foreach($data->week_names as $name): ?>
        <th scope="header"><?php echo $name; ?></th>
        <?php endforeach; ?>
    </tr>
    <?php foreach($data->grid_schedule as $time => $days): ?>
    <tr>

        <td><?php echo $days['display_time']; ?></td>
        <?php foreach($days['classes'] as $day_slot): ?>
            <td>
            <?php foreach($day_slot as $class): ?>
                <div class="mz_schedule_table mz_description_holder mz_location_1 <?php echo $class->session_type_css; echo $class->class_name_css; ?>"><?php echo $class->className; ?></div>
            <?php endforeach; ?>
            </td>
        <?php endforeach; ?>

    </tr>
    <?php endforeach; ?>
</table>
<?php mz_pr($data->week_names); ?>
<?php mz_pr($data->grid_schedule); ?>