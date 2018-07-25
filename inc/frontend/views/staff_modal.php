
<div class="mz_modalStaffDescription">

    <p>
        <?php

        if (!empty($data->staff_details['ImageURL'])): ?>
            <img src="<?php echo $data->staff_details['ImageURL']; ?>" class="mz_modal_staff_image_body"/>
            <?php
        endif;
        if (!empty($data->staff_details['Bio'])): ?>
            <?php echo $data->staff_details['Bio']; ?>
        <?php
        else: ?>
            <?php _e('No biography for this staff member yet.', 'mz-mindbody-api'); ?>
        <?php
        endif;
        ?>
    </p>
<a href="http://clients.mindbodyonline.com/ws.asp?studioid=<?php echo $data->siteID; ?>&stype=-7&sView=week&sTrn=<?php echo $data->staff_details['ID']; ?>" class="btn btn-info mz-btn-info mz-bio-button" target="_blank">See <?php echo $data->staff_details['Name']; ?> &apos;s Schedule</a>
</div>


