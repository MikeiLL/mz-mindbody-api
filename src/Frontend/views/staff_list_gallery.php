<?php
/**
 * Staff Gallery List
 *
 * Over-ride-able template page for full event listing shortcode result.
 *
 * @package MzMindbody
 */

use MZoo\MzMindbody as NS;

?>
<div class="container-fluid">
    <div class="row text-center">
    <?php foreach ( $data->staff as $staff ) : ?>
        <div class="col-lg-3 col-md-4 col-6 mz-staff-thumb">
            <a class="d-block mb-4 h-100 thumbnail" data-target="mzStaffModal" data-staffImage="<?php echo rawUrlEncode( $staff->image_url ); ?>"
            data-staffName="<?php echo $staff->staff_name; ?>"
            data-siteID="<?php echo $staff->site_id; ?>"
            data-staffID="<?php echo $staff->ID; ?>"
            data-staffBio="<?php echo ( ! empty( $staff->staff_bio ) ) ? $staff->staff_bio : ' '; ?>" href="<?php echo NS\PLUGIN_NAME_URL . 'src/Frontend/views/modals/modal_descriptions.php'; ?>">
                <img class="img-fluid img-thumbnail mz-staff-image" src="<?php echo $staff->image_url; ?>" alt="<?php echo $staff->staff_name; ?>">
                <div class="mz-staff-name"><?php echo $staff->staff_name; ?></div>
            </a>
        </div>
    <?php endforeach; ?>
    </div>
</div>

<div class="modal fade" id="mzStaffModal" tabindex="-1" role="dialog" aria-labelledby="mzSmallModalLabel" aria-hidden="true"></div>
