<?php

use MzMindbody as NS;

// This is already in the modal content window \\ ?>
    <table class="client-schedule">
        <?php foreach ($data->classes as $date => $classes) : ?>
            <?php foreach ($classes as $class) : ?>
                <tr>
                    <td>
                        <?php echo wp_date($data->date_format, strtotime($date)); ?>
                    </td>
                    <td>
                        <?php echo wp_date($data->time_format, strtotime($class->startDateTime)) . ' - ' . wp_date($data->time_format, strtotime($class->endDateTime)); ?>
                    </td>
                    <td>
                        <?php echo $class->className . ' with ' . $class->staffName; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endforeach; ?>

    </table>