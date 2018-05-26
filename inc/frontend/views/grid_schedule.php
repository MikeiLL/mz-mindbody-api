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
<h4 class="mz_grid_date <?php echo $data->grid_class; ?>">
    <?php
    $this_week_start = date_i18n($data->date_format, $data->start_date->getTimestamp());
    ?>
    <?php printf(__('Week of %1$s', 'mz-mindbody-api'), $this_week_start); ?>
</h4>
<table class="<?php echo $data->grid_class; ?>">
    <thead>
        <tr>
            <th scope="header"></th>
            <?php foreach($data->week_names as $name): ?>
                <th scope="header"><?php echo $name; ?></th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach($data->grid_schedule as $time => $days): ?>
        <tr>

            <td><?php echo $days['display_time']; ?><span class="mz_hidden"><?php echo $days['part_of_day']; ?></span></td>
            <?php foreach($days['classes'] as $day_slot): ?>
                <td>
                <?php foreach($day_slot as $class): ?>
                    <div class="mz_schedule_table mz_description_holder mz_location_1 <?php echo $class->session_type_css; echo $class->class_name_css; ?>">
                        <?php //echo $class->className; ?>
                        <?php echo date_i18n('l, m-d', strtotime($class->startDateTime)); ?>
                    </div>
                <?php endforeach; ?>
                </td>
            <?php endforeach; ?>

        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php //mz_pr($data->grid_schedule); ?>