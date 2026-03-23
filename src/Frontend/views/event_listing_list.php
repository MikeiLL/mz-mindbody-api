<?php
/**
 * Event Listing List
 *
 * @package MzMindbody
 *
 * This file contains the template for events listed in a tabular list.
 */

use MZoo\MzMindbody as NS;
use MZoo\MzMindbody\Core as Core;

if ( ! defined( 'ABSPATH' ) ) exit;

/* TODO: subclass maybe to DRY up btwn this and event_listing_full. */

if ( ( is_array( $data->events ) ) && ! empty( $data->events ) ) : ?>
    <table class="mz_event_list_listing">

        <thead>
            <tr class="header" style="display: table-row">
                <th class="mz_event_date_display" scope="header">
                    <?php echo esc_html($data->heading_date, 'mz-mindbody-api'); ?>
                </th>
                <th class="mz_event_classDetails" scope="header">
                    <?php echo esc_html($data->heading_time, 'mz-mindbody-api'); ?>
                </th>
                <th class="mz_event_name" scope="header">
                    <?php echo esc_html($data->heading_event, 'mz-mindbody-api'); ?>
                </th>
                <?php if ( $data->locations_count >= 2 ) : ?>
                    <th class="mz_event_location" scope="header">
                    <?php echo esc_html($data->heading_location, 'mz-mindbody-api'); ?>
                    </th>
                <?php endif; ?>
            </tr>
        </thead>

    <?php foreach ( $data->events as $date => $events ) : ?>
        <?php foreach ( $events as $event ) : ?>
        <tbody>
            <tr>
                <td><?php echo esc_html(gmdate( Core\MzMindbodyApi::$date_format, strtotime( $event->start_datetime ) ), 'mz-mindbody-api'); ?>
                <br /><?php echo wp_kses($event->sign_up_link->build(), [
              'a' => [
                'title' => [],
                'href' => [],
                'class' => [],
                'id' => [],
              ]
            ]); ?></td>
                <td>
            <?php echo esc_html(gmdate( Core\MzMindbodyApi::$time_format, strtotime( $event->start_datetime ) ), 'mz-mindbody-api'); ?> -
            <?php echo esc_html(gmdate( Core\MzMindbodyApi::$time_format, strtotime( $event->end_datetime ) ), 'mz-mindbody-api'); ?>
                </td>
                <td>
            <?php echo wp_kses($event->class_name_link->build(), [
              'a' => [
                'title' => [],
                'href' => [],
                'class' => [],
                'id' => [],
              ]
            ]) . ' ' . esc_html($data->with, 'mz-mindbody-api') . ' ' . wp_kses($event->staff_name_link->build(), [
              'a' => [
                'title' => [],
                'href' => [],
                'class' => [],
                'id' => [],
              ]
            ]); ?>

                </td>
            <?php
            // Display location if showing schedule for more than one location.
            if ( $data->locations_count >= 2 ) :
                ?>
                <td>
                <?php echo esc_url($data->locations_dictionary[ $event->location_id ]['link']); ?>
                </td>
            <?php endif; ?>
            </tr>
        <?php endforeach; ?>
    <?php endforeach; ?>
        </tbody>
    </table>
<?php elseif ( 0 === count( $data->events ) ) : ?>
    <h4><?php echo esc_html( $data->no_events, 'mz-mindbody-api' ); ?></h4>
<?php else : ?>
    <div class="error"><?php esc_html_e( 'Error Retrieving Events', 'mz-mindbody-api' ); ?></div>
    <p><?php var_dump( $data->events ); ?></p>

<?php endif; ?>
