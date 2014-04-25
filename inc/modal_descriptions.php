<?php
if (isset($_GET["classDescription"]))
  {
    $modal_description = $_GET["classDescription"];
?>
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      <h4 class="modal-title" id="mySmallModalLabel">Class Description</h4>
    </div>
     <div class="modal-body">
      <?php echo stripslashes($modal_description)?>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-xs" data-dismiss="modal">Close</button>
    </div>

<?php } ?>
