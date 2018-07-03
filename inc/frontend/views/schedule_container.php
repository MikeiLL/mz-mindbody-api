<?php

use MZ_Mindbody\Inc\Libraries as Libraries;
use MZ_Mindbody as NS;

// var_dump($_COOKIE['MZ_MBO_USER']);
// var_dump(json_decode(str_replace('\\', '', $_COOKIE['MZ_MBO_USER'])));
// var_dump(json_last_error());

mz_pr(NS\MZMBO()->session->get('MBO_Client'));

mz_pr(NS\MZMBO()->session->get_id());


?>
<div id="mzScheduleNavHolder">
    <a href="#" class="previous" data-offset="-1"><?php _e('Previous Week', 'mz-mindbody-api'); ?></a> -
    <a href="#" class="following" data-offset="1"><?php _e('Following Week', 'mz-mindbody-api'); ?></a>
</div>


<?php //mz_pr($data); ?>

<div id="mzScheduleDisplay" class="mz_mbo_schedule">
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