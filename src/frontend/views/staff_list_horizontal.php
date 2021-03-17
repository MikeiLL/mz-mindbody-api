<?php foreach ($data->staff as $staff) : ?>
<div class="MzStaff_profile clearfix">
    <div class="MzStaff_caption">
        <h3><?php echo $staff->Name; ?></h3>
        <div class="MzStaff_bio">
            <?php echo $staff->ImageTag; ?>
            
            <?php echo htmlspecialchars_decode($staff->Bio); ?>
            <p class="MzStaff_schedule">
                <?php echo $staff->ScheduleButton; ?>
            </p>
            <hr style="clear:both"/>
        </div>
    </div>
</div>
<?php endforeach; ?>