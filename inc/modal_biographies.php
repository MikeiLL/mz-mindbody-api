<?php
if (isset($_GET["staffBiography"]))
  {
    $staff_description = $_GET["staffBiography"];
    $staff_image = $_GET["staffImage"];
    $staff_name = $_GET["staffName"];
?>
<div class="modal-dialog modal-wide mz-modal">
    <div class="modal-header">
      <button type="button" class="close" id="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      <h4 class="modal-title" id="mzSmallModalLabel"><?php echo stripslashes($staff_name)?></h4>
    </div>
     <div class="modal-body">
     <?php echo sprintf('<img class="img-responsive mz_modal_staff_image_body" src="%s" alt="">', $staff_image); ?>
      <?php echo stripslashes($staff_description)?>
    </div>
    <div class="modal-footer">
    	<?php echo sprintf('<img class="img-responsive mz_modal_staff_image_footer" src="%s" alt="">', $staff_image); ?>
    </div>
</div>
<?php } ?>
