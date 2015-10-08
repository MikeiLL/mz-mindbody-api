<?php
$MBO_URL_PARTS = array('http://clients.mindbodyonline.com/ws.asp?studioid=',
											'&stype=-7&sView=week&sTrn=');
											
if (isset($_GET["staffBiography"]))
  {
    $staff_description = $_GET["staffBiography"];
    $staff_image = $_GET["staffImage"];
    $mz_staff_name = $_GET["staffName"];
    $mz_staff_id = $_GET["staffID"];
    $mz_site_id = $_GET["siteID"];
?>
<div class="modal-dialog modal-wide mz-modal">
    <div class="modal-header">
      <button type="button" class="close" id="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      <h4 class="modal-title" id="mzSmallModalLabel"><?php echo stripslashes($mz_staff_name)?></h4>
    </div>
     <div class="modal-body">
      <?php echo sprintf('<img class="img-responsive mz_modal_staff_image_body" src="%s" alt="">', $staff_image); ?>
      <?php echo stripslashes($staff_description)?>
    </div>
    <div class="modal-footer">
			<?php 
				echo '<a href="'. $MBO_URL_PARTS[0] . $mz_site_id . $MBO_URL_PARTS[1] . $mz_staff_id . '" class="btn btn-info mz-btn-info" target="_blank">See ' . $mz_staff_name .'&apos;s Schedule</a>';
			?>
    </div>
</div>
<?php } ?>
