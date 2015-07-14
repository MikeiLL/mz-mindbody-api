<?php
if (isset($_GET["clientID"]))
  {
    $clientID = $_GET["clientID"];
    $classID = $_GET["classID"];
    print_r($_GET);
    function mz_client_api_call($classID,$clientID){
	
	return 1==1;
}
mz_client_api_call($classID,$clientID);
?>
<div class="modal-dialog modal-sm">
    <div class="modal-header">
      <button type="button" class="close" id="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      <h4 class="modal-title" id="mzSmallModalLabel"><?php echo stripslashes($modal_name)?></h4>
    </div>
     <div class="modal-body">
      Hi, iLL.<?php echo stripslashes($modal_description)?>
    </div>
    <div class="modal-footer">
      <button id="close" type="button" class="btn btn-xs" data-dismiss="modal">Close</button>
    </div>
</div>
<?php } ?>
