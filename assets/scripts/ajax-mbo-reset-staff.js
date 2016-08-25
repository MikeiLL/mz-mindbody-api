(function($) {
	$(document).ready(function($) {
		$("a[data-target=#mzResetStaff]").click(function(ev) {
			ev.preventDefault();
			var target = $(this).attr("href");
			var nonce = $(this).attr("data-nonce");
			$('#mzResult').html('<h3>' + mZ_reset_staff.in_process + '</h3>');
			$.ajax({
				type: "GET",
				dataType: 'json',
				url : mZ_reset_staff.ajaxurl,
				data : {action: 'mz_mbo_reset_staff', nonce: nonce},
				success: function(json) {
					var processDetails = mZ_reset_staff.success;
					if(json.type == "success") {
						console.log(processDetails);
						if (json.message == 'success'){
							
							$('#mzResult').html(processDetails);
						}else{
							$('#mzResult').html(json.mbo_status);
						}
					}else{
							processDetails = "fucked";
							console.log(json);
							$('#mzResult').html('AJAX says ERROR RESETTING STAFF INFO');
					} // ./ Did php function succeed?
				} // ./ Ajax Success
			}); // ./Ajax
		});
	});
})( jQuery );
	