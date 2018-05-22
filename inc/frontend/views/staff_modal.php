
<?php
if (!empty($data->staff_details['ImageURL'])): ?>
    <img src="<?php echo $data->staff_details['ImageURL']; ?>" style="max-width:25%;height:auto;float:right;padding:1em;"/>
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



