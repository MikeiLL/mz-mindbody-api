<?php

use MZ_Mindbody as NS;
use MZ_Mindbody\Inc\Core as Core;

//var_dump($data);
// var_dump(json_decode(str_replace('\\', '', $_COOKIE['MZ_MBO_USER'])));
// var_dump(json_last_error());

NS\MZMBO()->helpers->mz_pr(NS\MZMBO()->session);
//NS\MZMBO()->session->clear();

// NS\MZMBO()->helpers->mz_pr($_COOKIE);

?>
<div id="mzScheduleNavHolder">
    <a href="#" class="previous" data-offset="-1"><?php _e('Previous Week', 'mz-mindbody-api'); ?></a> -
    <a href="#" class="following" data-offset="1"><?php _e('Following Week', 'mz-mindbody-api'); ?></a>
</div>

<?php //NS\MZMBO()->helpers->mz_pr($data); ?>

<div id="mzScheduleDisplay" class="mz_mbo_schedule" data-loggedMBO="<?php echo $data->loggedMBO; ?>">
    <?php
    if ($data->display_type == 'grid' || $data->display_type == 'both'): ?>
    <div id="gridDisplay" class="mz-schedule-display<?php echo $data->grid_class; ?>">
    <?php include 'grid_schedule.php';?>
    </div>
    <?
    endif;
    if ($data->display_type == 'horizontal' || $data->display_type == 'both'):?>
    <div id="horizontalDisplay" class="mz-schedule-display<?php echo $data->horizontal_class; ?>">
        <?php include 'horizontal_schedule.php';?>
    </div>
    <?
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