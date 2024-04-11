<?php
/**
 * Event Listing Full
 *
 * Over-ride-able template page for full event listing shortcode result.
 *
 * @package MzMindbody
 */

use MZoo\MzMindbody as NS;
use MZoo\MzMindbody\Core as Core;

if ( ( 1 === (int) $data->atts['location_filter'] ) &&
    ( 2 <= count( $data->locations_dictionary ) ) ) {
    // Print out Location Filter Buttons.
    ?>
    <div id="locations_filter">
        <a class="btn btn-primary filter_btn active" data-location="all">
            <?php echo esc_html( $data->all_locations_copy ); ?>
        </a>
    <?php
    foreach ( $data->locations_dictionary as $key => $loc ) :
        ?>
            <a class="btn btn-primary filter_btn" data-location="<?php echo esc_html( $loc['class'] ); ?>">
                <?php echo esc_html( $loc['name'] ); ?>
            </a>
        <?php
    endforeach;
    ?>
    </div>
    <?php
}

if ( ( is_array( $data->events ) ) && ! empty( $data->events ) ) :
    ?>
    <?php foreach ( $data->events as $date => $events ) : ?>
        <?php foreach ( $events as $event ) : ?>
        <div class="mz_full_listing_event <?php echo $data->locations_dictionary[ $event->location_id ]['class']; ?>">
            <h3 class="mz_full_listing_event__title">
                <?php echo esc_html( $event->class_name ); ?>
            </h3>
            <span class="mz_full_listing_event__staff">
            <?php echo $data->with . ' ' . $event->staff_name_link->build(); ?>
            </span>
            <?php
            // Display location if showing schedule for more than one location.
            if ( $data->locations_count >= 2 ) :
                ?>
                <span><?php echo __( 'at', 'mz-mindbody-api' ) . ' ' . $data->locations_dictionary[ $event->location_id ]['link']; ?></span>
            <?php endif; ?>
            <div class="mz_full_listing_event__date">
            <?php echo $event->start_date; ?>
            <?php echo $event->start_time; ?> –
            <?php echo $event->end_date; ?>
            <?php echo $event->end_time; ?>
            </div>
            <div class="mz_full_listing_event__disc">
                <p><img src="<?php echo $event->class_image; ?>" class="mz_inline_event_image_body" />
            <?php echo html_entity_decode( $event->event_description ); ?>
                </p>
                <p>
            <?php echo $event->sign_up_link->build(); ?>
                </p>
            </div>
            <hr class="mz_full_listing_event__rule" />
        </div>

        <?php endforeach; ?>
    <?php endforeach; ?>

<?php elseif ( 0 === (int) count( $data->events ) ) : ?>
    <h4><?php echo $data->no_events; ?></h4>
<?php else : ?>
    <div class="error"><?php echo esc_html( 'Error Retrieving Events', 'mz-mindbody-api' ); ?></div>
    <p><?php var_dump( $data->events ); ?></p>

<?php endif; ?>
