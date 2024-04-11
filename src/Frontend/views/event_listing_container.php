<?php
/**
 * Event Listing Container
 *
 * Over-ride-able template page for full event listing shortcode result.
 *
 * @package MzMindbody
 */

?>
<h3 id="eventsDateRangeDisplay" >
<?php
echo sprintf(
    // translators: start and end dates with "to" or "-" between them.
    __( 'Displaying events from %1$s to %2$s.', 'mz-mindbody-api' ),
    $data->display_time_frame['start']->format( 'F j' ),
    $data->display_time_frame['end']->format( 'F j' )
);
?>
</h3>

<?php if ( empty( $data->atts['week-only'] ) ) : ?>
<div id="mzEventsNavHolder">
    <a href="#" class="previous" data-offset="-1"><?php esc_html_e( 'Previous Events', 'mz-mindbody-api' ); ?></a> -
    <a href="#" class="following" data-offset="1"><?php esc_html_e( 'Future Events', 'mz-mindbody-api' ); ?></a>
</div>
<?php endif; ?>
<div id="mzEventsDisplay">
<?php
if ( 1 !== (int) $data->atts['list'] ) :
    include 'event_listing_full.php';
else :
    include 'event_listing_list.php';
endif;
?>
</div>

<div id="mzModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mzSmallModalLabel"
    aria-hidden="true"></div>
<div class="modal fade" id="mzSignUpModal" tabindex="-1" role="dialog" aria-labelledby="mzSmallModalLabel"
    aria-hidden="true"></div>
