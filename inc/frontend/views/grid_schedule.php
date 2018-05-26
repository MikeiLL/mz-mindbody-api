<?php

use MZ_Mindbody\Inc\Core;
use MZ_Mindbody\Inc\Libraries as Libraries;

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
<table class="<?php echo $data->grid_class; ?>">
    <tr>
        <th></th>
        <?php foreach($data->week_names as $name): ?>
        <th><?php echo $name; ?></th>
        <?php endforeach; ?>
    </tr>
    <?php foreach($data->grid_schedule as $time => $days): ?>
    <tr>

        <td><?php echo $days['display_time']; ?></td>
        <?php foreach($days['classes'] as $day_slot): ?>
            <td>
            <?php foreach($day_slot as $time_slot): ?>
                <div> <?php echo $time_slot->className; ?></div>
            <?php endforeach; ?>
            </td>
        <?php endforeach; ?>

    </tr>
    <?php endforeach; ?>
</table>
<?php mz_pr($data->week_names); ?>
<?php mz_pr($data->grid_schedule); ?>