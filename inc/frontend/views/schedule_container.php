<?php

use MZ_Mindbody\Inc\Libraries as Libraries;

?>
<div id="mzScheduleNavHolder">
    <a href="#" class="previous" data-offset="-1"><?php _e('Previous Week', 'mz-mindbody-api'); ?></a> -
    <a href="#" class="following" data-offset="1"><?php _e('Following Week', 'mz-mindbody-api'); ?></a>
</div>


<?php // mz_pr($data); ?>

<div id="mzScheduleDisplay" class="">
    <?php
    if ($data->display_type == 'grid' || $data->display_type == 'both'): ?>

    <?
        include 'grid_schedule.php';?>

    <?
    endif;
    if ($data->display_type = 'horizontal' || $data->display_type == 'both'):?>

        <?
        include 'horizontal_schedule.php';?>

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