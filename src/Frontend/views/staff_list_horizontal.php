<?php
/**
 * Staff Listing Horizontal
 *
 * Over-ride-able template page for full event listing shortcode result.
 *
 * @package MzMindbody
 */

?>

<?php foreach ( $data->staff as $staff ) : ?>
<div class="MzStaff_profile clearfix">
    <div class="MzStaff_caption">
        <h3><?php echo $staff->staff_name; ?></h3>
        <div class="MzStaff_bio">
    <?php echo $staff->image_tag; ?>
    <?php echo htmlspecialchars_decode( $staff->staff_bio ); ?>
            <p class="MzStaff_schedule">
                <?php echo $staff->schedule_button; ?>
            </p>
            <hr style="clear:both"/>
        </div>
    </div>
</div>
<?php endforeach; ?>
