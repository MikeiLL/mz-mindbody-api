
<?php
use MZ_Mindbody as NS;
use MZ_Mindbody\Inc\Core as Core;

if ((is_array($data->events)) && !empty($data->events)): ?>

    <?php foreach ($data->events as $date => $events): ?>
        <?php foreach ($events as $event): ?>
        <div class="mz_full_listing_event <?php echo $data->locations_dictionary[$event->location_ID]['class']; ?>">
            <h3 class="mz_full_listing_event__title"><?php echo $event->className; ?></h3>
            <span class="mz_full_listing_event__staff">
                <?php echo  $data->with . ' ' . $event->staff_name_link->build(); ?>
            </span>
            <?php
            // Display location if showing schedule for more than one location
            if($data->locations_count >= 2): ?>
                <span><?php echo __('at', 'mz_mindbody_api') . ' ' . $data->locations_dictionary[$event->location_ID]['link']; ?></span>
            <?php endif; ?>
            <div class="mz_full_listing_event__date">
                <?echo date_i18n(Core\MZ_Mindbody_Api::$date_format, strtotime($event->startDateTime)); ?>
                <?echo date_i18n(Core\MZ_Mindbody_Api::$time_format, strtotime($event->startDateTime)); ?> -
                <?echo date_i18n(Core\MZ_Mindbody_Api::$time_format, strtotime($event->endDateTime)); ?>
            </div>
            <div class="mz_full_listing_event__disc">
                <p><img src="<?php echo $event->classImage; ?>" class="mz_inline_event_image_body" />
                    <?php echo html_entity_decode($event->Description); ?>
                </p>
                <p>
                    <?php echo $event->sign_up_link->build(); ?>
                </p>
            </div>
        </div>
        <hr class="mz_full_listing_event__rule" />

        <?php endforeach; ?>
    <?php endforeach; ?>

<?php elseif (count($data->events) == 0 ): ?>
    <h4><?php echo $data->no_events; ?></h4>
<?php else: ?>
    <div class="error"><?php _e('Error Retrieving Events', 'mz_mindbody_api'); ?></div>
    <p><?php var_dump( $data->events ); ?></p>

<?php endif; ?>