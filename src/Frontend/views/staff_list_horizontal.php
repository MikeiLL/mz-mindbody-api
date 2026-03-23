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
        <h3><?php echo esc_html($staff->staff_name, 'mz-mindbody-api' ); ?></h3>
        <div class="MzStaff_bio">
    <?php echo wp_kses($staff->image_tag, [
        'img'      => [
            'src'  => [],
            'title' => [],
            'class' => [],
            'id' => [],
        ],
    ]); ?>
    <?php echo esc_html(htmlspecialchars_decode( $staff->staff_bio ), 'mz-mindbody-api' ); ?>
            <p class="MzStaff_schedule">
                <?php echo wp_kses($staff->schedule_button, [
        'a'      => [
            'href'  => [],
            'title' => [],
            'class' => [],
            'id' => [],
        ],
    ]); ?>
            </p>
            <hr style="clear:both"/>
        </div>
    </div>
</div>
<?php endforeach; ?>
