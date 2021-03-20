<?php foreach ( $data->staff as $staff ) : ?>
<div class="MzStaff_profile clearfix">
    <div class="MzStaff_caption">
        <h3><?php esc_html_e($staff->Name); ?></h3>
        <div class="MzStaff_bio">
    <?php esc_html_e($staff->ImageTag); ?>
            
    <?php echo htmlspecialchars_decode($staff->Bio); ?>
            <p class="MzStaff_schedule">
                <?php esc_html_e($staff->ScheduleButton); ?>
            </p>
            <hr style="clear:both"/>
        </div>
    </div>
</div>
<?php endforeach; ?>
