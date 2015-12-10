	$(document).ready(function($) {
		$("a[data-target=#registrantModal]").click(function(ev) {
			ev.preventDefault();
			var target = $(this).attr("href");
			var classDescription = $(this).attr('data-classDescription');
			var staffName = $(this).attr('data-staffName');
			var teacherPicture = $(this).find( "img" ).attr('src');
			var className = $(this).attr("data-className");
			var popUpContent = '<h3>' + className + '</h3>' + /*'<img class="teacher-picture" src="' + teacherPicture + '" />*/'<div class="mz-class-info"></div>';
			var nonce = $(this).attr("data-nonce");
			var classID = $(this).attr("data-classID");
			var htmlClassDescription = '<div class="mz_modal_class_description">';
			htmlClassDescription += "<div class='class-description'>"  +  decodeURIComponent(classDescription) + "</div>";
			htmlClassDescription += '<h5 class="mz_registrants_header">' + mZ_get_registrants.registrants_header + '</h5>';
			popUpContent += htmlClassDescription;
			popUpContent += '<div id="modalRegistrants"><div id="ClassRegistrants" style="min-height:90px;"><i class="fa fa-spinner fa-3x fa-spin"></i></div></div></div>';
			$("#registrantModal").load(target, function() { 
				 $.colorbox({html: popUpContent, width:"75%"}); 
				 $("#registrantModal").colorbox();
			});
			$.ajax({
				type: "GET",
				dataType: 'json',
				url : mZ_add_to_classes.ajaxurl,
				data : {action: 'mz_mbo_get_registrants', nonce: nonce, classID: classID},
				success: function(json) {
					console.log(json);
					if(json.type == "success") {
							htmlRegistrants = '<ul class="mz_class_registrants">';
							if ( $.isArray(json.message)  ) {
								json.message.forEach( function(name) {
									htmlRegistrants += '<li>' + name.replace('_', ' ') + '</li>';
									});
							} else {
								htmlRegistrants += '<li>' + json.message + '</li>';
							}
							htmlRegistrants += '</ul>';
							$('#modalRegistrants').find('#ClassRegistrants')[0].innerHTML = htmlRegistrants;
							console.log(htmlRegistrants);
					}else{
							$('#modalRegistrants').find('#class-description-modal-body')[0].innerHTML = mZ_get_registrants.get_registrants_error;
					}
				} // ./ Ajax Success
			}); // ./Ajax
		});
	});
	