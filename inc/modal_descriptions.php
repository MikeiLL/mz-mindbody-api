<?php
if (isset($_GET["classDescription"]))
  {
    $modal_description = $_GET["classDescription"];
    $modal_name = $_GET["className"];
?>
<div class="modal-dialog modal-wide mz-modal">
    <div class="modal-header">
      <button type="button" class="close" id="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      <h4 class="modal-title" id="mzSmallModalLabel"><?php echo stripslashes($modal_name)?></h4>
    </div>
     <div class="modal-body">
      <?php echo stripslashes($modal_description)?>
    </div>
    <div class="modal-footer">
    </div>
</div>
<?php } ?>
