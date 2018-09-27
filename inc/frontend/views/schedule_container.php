<?php

use MZ_Mindbody as NS;
use MZ_Mindbody\Inc\Core as Core;

/**
 * This file is the wrapper for Horizontal and/or Grid classes.
 */
$navigation_text = array(
        'prev' => __('Previous Week', 'mz-mindbody-api'),
        'next' => __('Following Week', 'mz-mindbody-api'),
);
if ($data->atts['type'] === 'day'):
    $navigation_text['prev'] = __('Previous Day', 'mz-mindbody-api');
    $navigation_text['next'] = __('Following Day', 'mz-mindbody-api');
endif;
?>
<div id="mzScheduleNavHolder">
    <a href="#" class="previous" data-offset="-1"><?php echo $navigation_text['prev']; ?></a> -
    <a href="#" class="following" data-offset="1"><?php echo $navigation_text['next']; ?></a>
</div>

<div id="mzScheduleDisplay" class="mz_mbo_schedule">
    <?php
    if ($data->display_type == 'grid' || $data->display_type == 'both'): ?>
    <div id="gridDisplay" class="mz-schedule-display<?php echo $data->grid_class; ?>">
    <?php include 'grid_schedule.php';?>
    </div>
    <?php
    endif;
    if ($data->display_type == 'horizontal' || $data->display_type == 'both'):?>
    <div id="horizontalDisplay" class="mz-schedule-display<?php echo $data->horizontal_class; ?>">
        <?php include 'horizontal_schedule.php';?>
    </div>
    <?php
    endif;
    ?>
</div>

<div id="mzModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mzSmallModalLabel"
     aria-hidden="true"></div>
<div class="modal fade" id="registrantModal" tabindex="-1" role="dialog" aria-labelledby="mzSmallModalLabel"
     aria-hidden="true"></div>
<div class="modal fade" id="mzStaffScheduleModal" tabindex="-1" role="dialog" aria-labelledby="mzSmallModalLabel"
     aria-hidden="true"></div>
<div class="modal fade" id="mzSignUpModal" tabindex="-1" role="dialog" aria-labelledby="mzSmallModalLabel"
     aria-hidden="true"></div>

<?php include('login_form.php'); ?>
