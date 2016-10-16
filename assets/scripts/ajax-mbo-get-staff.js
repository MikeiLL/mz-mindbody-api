(function($) {
	$(document).ready(function($) {
		$("a[data-target=#mzStaffScheduleModal]").click(function(ev) {
			ev.preventDefault();
			var target = $(this).attr("href");
			var staffID = $(this).attr('data-staffID');
			var staffName = $(this).attr('data-staffName');
			var siteID = $(this).attr('data-siteID');
			var nonce = $(this).attr("data-nonce");
			var subText = ($(this).attr("data-sub") !== undefined) ? '<span class="sub-text">' + mZ_get_staff.sub_by_text + '</span>' + ' ' : ' ';
			var popUpContent = '<h3>' + subText + staffName + '</h3><div class="mz-staffInfo" id="StaffInfo"></div>';
			var htmlStaffDescription = '<div class="mz_modalStaffDescription"></div>';
			var mbo_url_parts = ['http://clients.mindbodyonline.com/ws.asp?studioid=',
													'&stype=-7&sView=week&sTrn='];
			//popUpContent += '<br/><a href="' + mbo_url_parts[0] + siteID + mbo_url_parts[1] + staffID + '" ';
			//popUpContent += 'class="btn btn-info mz-btn-info mz-bio-button" target="_blank">See ' + staffName +'&apos;s Schedule</a>';
			$("#mzStaffScheduleModal").load(target, function() { 
				 $.colorbox({html: popUpContent, width:"75%", height:"80%", href:"inc/modal_descriptions.php"}); 
				 $("#mzStaffScheduleModal").colorbox();
			});
			$.ajax({
				type: "GET",
				dataType: 'json',
				url : mZ_get_staff.ajaxurl,
				data : {action: 'mz_mbo_get_staff', nonce: nonce, staffID: staffID, siteID: siteID},
				success: function(json) {
					if(json.type == "success") {
							var staffDetails = '';
							var imageURL = ((json.message.ImageURL === null) || (json.message.ImageURL === '')) ? '' : '<img class="mz-staffImage" src="' + json.message.ImageURL + '">';
							var bioGraphy = ((json.message.Bio === null) || (json.message.Bio === '')) ? mZ_get_staff.no_bio : '<div class="mz_staffBio">' + json.message.Bio + '</div>';
							staffDetails += imageURL;
							staffDetails += bioGraphy;
							$('#StaffInfo').html(staffDetails);
					}else{
							$('#StaffInfo').html('ERROR FINDING STAFF INFO');
					}
				} // ./ Ajax Success
			}); // ./Ajax
		});
	});
})( jQuery );
	