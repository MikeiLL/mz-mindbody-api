(function($) {
	$(document).ready(function($) {
		$("a[data-target=#registrantModal]").click(function(ev) {
			ev.preventDefault();
			var target = $(this).attr("href");
			var classDescription = $(this).attr('data-classDescription');
			var staffName = $(this).attr('data-staffName');
			var staffImage = $(this).attr('data-staffImage');
			var className = $(this).attr("data-className");
			var classID = $(this).attr("data-classID");
			var nonce = $(this).attr("data-nonce");
			var popUpContent = '<div class="mz-classInfo">';
			popUpContent += '<h3>' + className + '</h3>';
			popUpContent += '<h4>' + mZ_get_registrants.staff_preposition + ' ' + staffName + '</h4>';
						
			if (typeof staffImage != 'undefined') {
 				popUpContent += '<img class="mz-staffImage" src="' + staffImage + '" />';
			} 

			var htmlClassDescription = '<div class="mz_modalClassDescription">';
			htmlClassDescription += "<div class='class-description'>"  +  decodeURIComponent(classDescription) + "</div></div>";
			popUpContent += htmlClassDescription;
			popUpContent += '</div>';
			
			popUpContent += '<h3>' + mZ_get_registrants.registrants_header + '</h3>';
			popUpContent += '<div id="modalRegistrants"><div id="ClassRegistrants" style="min-height:90px;">';
			popUpContent += '<i class="fa fa-spinner fa-3x fa-spin"></i></div></div>';
			$("#registrantModal").load(target, function() { 
				 $.colorbox({html: popUpContent, width:"75%", height:"80%", href:"inc/modal_descriptions.php"}); 
				 $("#registrantModal").colorbox();
			});
			$.ajax({
				type: "GET",
				dataType: 'json',
				url : mZ_add_to_classes.ajaxurl,
				data : {action: 'mz_mbo_get_registrants', nonce: nonce, classID: classID},
				success: function(json) {
					if(json.type == "success") {
							htmlRegistrants = '<ul class="mz-classRegistrants">';
							if ( $.isArray(json.message)  ) {
								json.message.forEach( function(name) {
									htmlRegistrants += '<li>' + name.replace('_', ' ') + '</li>';
									});
							} else {
								htmlRegistrants += '<li>' + json.message + '</li>';
							}
							htmlRegistrants += '</ul>';
							$('#modalRegistrants').find('#ClassRegistrants')[0].innerHTML = htmlRegistrants;
					}else{
							$('#modalRegistrants').find('#class-description-modal-body')[0].innerHTML = mZ_get_registrants.get_registrants_error;
					}
				} // ./ Ajax Success
			}); // ./Ajax
		});
	});
})( jQuery );
	