<?php
/**
 * Client Schedule
 *
 * Over-ride-able template page for full event listing shortcode result.
 *
 * @package MzMindbody
 */

use MZoo\MzMindbody as NS;

// This is already in the modal content window.
?>
    <table class="client-schedule">
        <?php foreach ( $data->classes as $date => $classes ) : ?>
            <?php foreach ( $classes as $class ) : ?>
                <tr>
                    <td>
                <?php echo wp_date( $data->date_format, strtotime( $date ) ); ?>
                    </td>
                    <td>
                <?php echo wp_date( $data->time_format, strtotime( $class->start_datetime ) ) . ' - ' . wp_date( $data->time_format, strtotime( $class->end_datetime ) ); ?>
                    </td>
                    <td>
                <?php echo $class->class_name . ' with ' . $class->staff_name; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endforeach; ?>

    </table>
