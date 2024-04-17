<?php
/**
 * Schedule Container
 *
 * Over-ride-able template page for full event listing shortcode result.
 *
 * @package MzMindbody
 */

use MZoo\MzMindbody as NS;
use MZoo\MzMindbody\Core as Core;

/**
 * This file is the wrapper for Horizontal and/or Grid classes.
 */

?>
<?php if ( 'week' === $data->atts['type'] ) : ?>
<div id="mzScheduleNavHolder">
    <a href="#" class="previous" data-offset="-1"><?php esc_html_e( 'Previous Week', 'mz-mindbody-api' ); ?></a> -
    <a href="#" class="following" data-offset="1"><?php esc_html_e( 'Following Week', 'mz-mindbody-api' ); ?></a>
</div>
<?php endif; ?>

<div class="d-flex justify-content-center">
    <div id="mzScheduleDisplay" class="mz_mbo_schedule">
        <?php
        if ( 'grid' === $data->display_type || 'both' === $data->display_type ) :
            ?>
        <div id="gridDisplay" class="mz-schedule-display<?php echo $data->grid_class; ?>">
            <?php include 'grid_schedule.php'; ?>
        </div>
            <?php
        endif;
        if ( 'horizontal' === $data->display_type || 'both' === $data->display_type ) :
            ?>
        <div id="horizontalDisplay" class="mz-schedule-display<?php echo $data->horizontal_class; ?>">
            <?php include 'horizontal_schedule.php'; ?>
        </div>
            <?php
        endif;
        ?>
    </div>
</div>

<div id="mzModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mzSmallModalLabel"
    aria-hidden="true"></div>
<div class="modal fade" id="registrantModal" tabindex="-1" role="dialog" aria-labelledby="mzSmallModalLabel"
    aria-hidden="true"></div>
<div class="modal fade" id="mzStaffScheduleModal" tabindex="-1" role="dialog" aria-labelledby="mzSmallModalLabel"
    aria-hidden="true"></div>
<div class="modal fade" id="mzSignUpModal" tabindex="-1" role="dialog" aria-labelledby="mzSmallModalLabel"
    aria-hidden="true"></div>
