<?php
/**
 * Staff Modal Window
 *
 * Over-ride-able template page for full event listing shortcode result.
 *
 * @package MzMindbody
 */

if ( ! defined( 'ABSPATH' ) ) exit;

use MZoo\MzMindbody as NS;

?>
<div class="mz_modalStaffDescription">

    <p>
        <?php
        if ( ! empty( $data->staff_details->image_url ) ) :
            ?>
            <img src="<?php echo esc_url($data->staff_details->image_url, 'mz-mindbody-api'); ?>" class="mz_modal_staff_image_body"/>
            <?php
        endif;
        if ( ! empty( $data->staff_details->staff_bio ) ) :
            ?>
            <?php echo esc_html(html_entity_decode( $data->staff_details->staff_bio ), 'mz-mindbody-api'); ?>
            <?php
        else :
            ?>
            <?php esc_html_e( 'No biography for this staff member yet.', 'mz-mindbody-api' ); ?>
            <?php
        endif;
        ?>
    </p>
        <?php echo wp_kses($data->staff_details->schedule_button, [
        'a'      => [
            'href'  => [],
            'title' => [],
            'class' => [],
            'id' => [],
        ],
    ]); ?>

</div>
