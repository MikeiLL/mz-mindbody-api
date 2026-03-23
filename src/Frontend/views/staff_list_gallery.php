<?php
/**
 * Staff Gallery List
 *
 * Over-ride-able template page for full event listing shortcode result.
 *
 * @package MzMindbody
 */

use MZoo\MzMindbody as NS;

if ( ! defined( 'ABSPATH' ) ) exit;

?>
<div class="container-fluid">
    <div class="row text-center">
    <?php foreach ( $data->staff as $staff ) : ?>
        <div class="col-lg-3 col-md-4 col-6 mz-staff-thumb">
            <a class="d-block mb-4 h-100 thumbnail" data-target="mzStaffModal" data-staffImage="<?php echo rawUrlEncode( $staff->image_url ); ?>"
            data-staffName="<?php echo esc_html($staff->staff_name, 'mz-mindbody-api'); ?>"
            data-siteID="<?php echo esc_html($staff->site_id, 'mz-mindbody-api'); ?>"
            data-staffID="<?php echo esc_html($staff->ID, 'mz-mindbody-api'); ?>"
            data-staffBio="<?php echo ( ! empty( $staff->staff_bio ) )
            ? esc_html($staff->staff_bio)
            : esc_html(' ', 'mz-mindbody-api'); ?>" href="<?php echo esc_url(NS\PLUGIN_NAME_URL . 'src/Frontend/views/modals/modal_descriptions.php'); ?>">
                <img class="img-fluid img-thumbnail mz-staff-image" src="<?php echo esc_url($staff->image_url); ?>" alt="<?php echo esc_html($staff->staff_name, 'mz-mindbody-api'); ?>">
                <div class="mz-staff-name"><?php echo esc_html($staff->staff_name, 'mz-mindbody-api'); ?></div>
            </a>
        </div>
    <?php endforeach; ?>
    </div>
</div>

<div class="modal fade" id="mzStaffModal" tabindex="-1" role="dialog" aria-labelledby="mzSmallModalLabel" aria-hidden="true"></div>
