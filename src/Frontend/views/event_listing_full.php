<?php

use MZoo\MzMindbody as NS;
use MZoo\MzMindbody\Core as Core;

if (( $data->atts['location_filter'] == 1 ) && ( count($data->locations_dictionary) >= 2 ) ) {
    // Print out Location Filter Buttons
    ?>
    <div id="locations_filter">
        <a class="btn btn-primary filter_btn active" data-location="all">
            <?php esc_html_e($data->all_locations_copy); ?>
        </a>
    <?php
    foreach ( $data->locations_dictionary as $key => $loc ) :
        ?>
            <a class="btn btn-primary filter_btn" 
                data-location="<?php esc_html_e($loc['class']); ?>">
                <?php esc_html_e($loc['name']); ?>
            </a>
        <?php
    endforeach;
    ?>
    </div>
    <?php
}

if (( is_array($data->events) ) && ! empty($data->events) ) :
    ?>
    <?php foreach ( $data->events as $date => $events ) : ?>
        <?php foreach ( $events as $event ) : ?>
        <?php $ev_class = $data->locations_dictionary[ $event->location_ID ]['class']; ?>
        <div class="mz_full_listing_event <?php esc_html_e(($ev_class); ?>">
            <h3 class="mz_full_listing_event__title">
                <?php esc_html_e($event->className); ?>
            </h3>
            <span class="mz_full_listing_event__staff">
            <?php esc_html_e($data->with . ' ' . $event->staff_name_link->build()); ?>
            </span>
            <?php
            // Display location if showing schedule for more than one location
            if ($data->locations_count >= 2 ) :
                ?>
                <span><?php echo __('at', 'mz-mindbody-api') . ' ' . $data->locations_dictionary[ $event->location_ID ]['link']; ?></span>
            <?php endif; ?>
            <div class="mz_full_listing_event__date">
            <?php esc_html_e($event->start_date); ?>
            <?php esc_html_e($event->start_time); ?> –
            <?php esc_html_e($event->end_date); ?>
            <?php esc_html_e($event->end_time); ?>
            </div>
            <div class="mz_full_listing_event__disc">
                <p><img src="<?php esc_html_e($event->classImage); ?>" class="mz_inline_event_image_body" />
            <?php echo html_entity_decode($event->Description); ?>
                </p>
                <p>
            <?php esc_html_e($event->sign_up_link->build()); ?>
                </p>
            </div>
            <hr class="mz_full_listing_event__rule" />
        </div>

        <?php endforeach; ?>
    <?php endforeach; ?>

<?php elseif (count($data->events) == 0 ) : ?>
    <h4><?php esc_html_e($data->no_events); ?></h4>
<?php else : ?>
    <div class="error"><?php esc_html_e('Error Retrieving Events', 'mz-mindbody-api'); ?></div>
    <p><?php var_dump($data->events); ?></p>

<?php endif; ?>
