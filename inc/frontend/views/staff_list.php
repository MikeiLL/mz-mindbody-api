<?php

//foreach($data->staff as $staff):
//    var_dump($staff->ImageURL);
//endforeach;
?>
<?php foreach($data->staff as $staff): ?>
<div class="MZ_Staff_profile clearfix">
    <div class="MZ_Staff_caption">
        <h3><?php echo $staff->Name; ?></h3>
        <div class="MZ_Staff_bio">
            <?php echo $staff->ImageTag; ?>
            <?php echo $staff->Bio; ?>
            <p class="MZ_Staff_schedule">
                <?php echo $staff->ScheduleButton; ?>
            </p>
            <hr style="clear:both"/>
        </div>
    </div>
</div>
<?php endforeach; ?>