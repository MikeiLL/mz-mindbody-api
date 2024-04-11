<?php
/**
 * Staff Modal Window
 *
 * Over-ride-able template page for full event listing shortcode result.
 *
 * @package MzMindbody
 */

use MZoo\MzMindbody as NS;

?>
<div class="mz_modalStaffDescription">

    <p>
        <?php
        if ( ! empty( $data->staff_details->image_url ) ) :
            ?>
            <img src="<?php echo $data->staff_details->image_url; ?>" class="mz_modal_staff_image_body"/>
            <?php
        endif;
        if ( ! empty( $data->staff_details->staff_bio ) ) :
            ?>
            <?php echo html_entity_decode( $data->staff_details->staff_bio ); ?>
            <?php
        else :
            ?>
            <?php esc_html_e( 'No biography for this staff member yet.', 'mz-mindbody-api' ); ?>
            <?php
        endif;
        ?>
    </p>
        <?php echo $data->staff_details->schedule_button; ?>

</div>
