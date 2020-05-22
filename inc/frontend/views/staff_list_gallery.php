<?php
use MZ_Mindbody as NS;

//foreach($data->staff as $staff):
//    NS\MZMBO()->helpers->print($staff);
//endforeach;
?>
<?php foreach($data->staff as $staff): ?>
<div class="col-lg-3 col-md-4 col-xs-6 mz-staff-thumb">
    <a class="thumbnail" data-target="#mzStaffModal" data-staffImage="<?php echo rawUrlEncode($staff->ImageURL) ?>"
    data-staffName="<?php echo $staff->Name; ?>"
    data-siteID="<?php echo $staff->siteID; ?>"
    data-staffID="<?php echo $staff->ID; ?>"
    data-staffBio="<?php echo (!empty($staff->Bio)) ? $staff->Bio : ' '; ?>" href="<?php echo NS\PLUGIN_NAME_URL . 'inc/frontend/views/modals/modal_descriptions.php'; ?>">
        <img class="img-responsive mz-staff-image" src="<?php echo $staff->ImageURL; ?>" alt="<?php echo $staff->Name; ?>">
        <div class="mz-staff-name"><?php echo $staff->Name; ?></div>
    </a>
</div>
<?php endforeach; ?>

<div class="modal fade" id="mzStaffModal" tabindex="-1" role="dialog" aria-labelledby="mzSmallModalLabel" aria-hidden="true"></div>