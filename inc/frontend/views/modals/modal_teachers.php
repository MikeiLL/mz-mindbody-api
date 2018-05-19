<?php
if (isset($_GET["classDescription"]))
	{
		$modal_description = $_GET["classDescription"];
		?>
		<!-- Small modal -->
 <div class='modal-content' style="max-width:400px">
    		  <div class='modal-dialog modal-sm'>
				<?php echo stripslashes($modal_description)?>
  				</div>
    <button type="button" class="btn btn-xs" data-dismiss="modal">Close</button>
	</div>
		<?php
	}
?>
