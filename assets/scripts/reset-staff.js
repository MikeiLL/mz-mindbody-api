(function($) {
	$(document).ready(function($) {
		$("a[data-target=#mzResetStaff]").click(function(ev) {
			ev.preventDefault();
			$('.reset-button').hide();
			var target = $(this).attr("href");
			var nonce = $(this).attr("data-nonce");
			var spinner = '<img src="';
			spinner += mZ_reset_staff.site_url;
			spinner +=  '/wp-admin/images/spinner-2x.gif';
			spinner += '" />';
			$('.reset_class_schedule').append(spinner);
			$.ajax({
				type: "GET",
				dataType: 'json',
				url : mZ_reset_staff.ajaxurl,
				data : {action: 'mz_mbo_reset_staff', nonce: nonce},
				success: function(json) {
					$('.reset_class_schedule').html();
					if(json.type == "success") {
						if (!$.isArray(json.message)){
							$success_message = '<div class="updated">' + json.message + '</div>';
							$('.reset_class_schedule').html($success_message);
						}else{
							var mbo_errors = '<div class="settings-error"><p>' + json.message + '</p>';
							mbo_errors += '<p>' + json.mbo_status + '</p></div>';
							mbo_errors += '<pre><code>' + json.mbo_result + '</code></pre>';
							$('.reset_class_schedule').html(mbo_errors);
						}
					}else{
							$('.reset_class_schedule').html('AJAX says ERROR RESETTING STAFF INFO');
					} // ./ Did php function succeed?
				} // ./ Ajax Success
			}); // ./Ajax
		});
	});
})( jQuery );
	