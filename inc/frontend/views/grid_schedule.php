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
<h4 class="mz_grid_date">
    <?php
    $this_week_start = date_i18n($data->date_format, $data->start_date->getTimestamp());
    ?>
    <?php printf(__('Week of %1$s', 'mz-mindbody-api'), $this_week_start); ?>
</h4>
<table class="<?php echo $data->table_class; ?>">
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
                <td><?php // var_dump( $day_slot ); ?>
                <?php foreach($day_slot as $class): ?>
                    <div class="mz_schedule_table mz_description_holder mz_location_1 <?php echo $class->session_type_css; echo $class->class_name_css; ?>">
                        <?php echo $class->className; ?>
                        <?php _e('with', 'mz-mindbody-api'); echo $class->staffName; ?><br />
                        <a href="<?php echo $class->mbo_url; ?>" target="_blank">
                            <svg height="20" width="20">
                                <path d="M550 0h400q165 0 257.5 92.5T1300 350v400q0 165-92.5 257.5T950 1100H550q-21 0-35.5-14.5T500 1050V950q0-21 14.5-35.5T550 900h450q41 0 70.5-29.5T1100 800V300q0-41-29.5-70.5T1000 200H550q-21 0-35.5-14.5T500 150V50q0-21 14.5-35.5T550 0zM338 233l324 284q16 14 16 33t-16 33L338 867q-16 14-27 9t-11-26V700H50q-21 0-35.5-14.5T0 650V450q0-21 14.5-35.5T50 400h250V250q0-21 11-26t27 9z"/>
                            </svg>
                        </a>
                        <?php _e('Duration:', 'mz-mindbody-api'); ?> <br/>&nbsp;<?php echo $class->class_duration->format('%H:%I'); ?>
                    </div>
                <?php endforeach; ?>
                </td>
            <?php endforeach; ?>

        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
