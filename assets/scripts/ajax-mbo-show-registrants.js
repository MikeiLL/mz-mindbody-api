$(document).ready(function($) {
	var infoModal = $('#registrantModal');
	$('.mz_get_registrants').click(function(){
		var nonce = $(this).attr("data-nonce");
		classID = $(this).attr("data-classID");
		classDescription = $(this).attr("data-classDescription");
		className = $(this).attr("data-className");
		//alert("The class ID is: " + classID);
		$.ajax({
			type: "GET",
			dataType: 'json',
			url : mZ_add_to_classes.ajaxurl,
			data : {action: 'mz_mbo_get_registrants', nonce: nonce, classID: classID},
			success: function(json) {
				console.log(json);
				if(json.type == "success") {
						var htmlData = '<div>' + decodeURIComponent(classDescription) + '</div>';
						htmlData += '<h5 class="mz_registrants_header">' + mZ_get_registrants.registrants_header + '</h5>';
						htmlData += '<ul>';
						if ( $.isArray(json.message)  ) {
							json.message.forEach( function(name) {
								htmlData += '<li>' + name.replace('_', ' ') + '</li>';
								});
						} else {
							htmlData += '<li>' + json.message + '</li>';
						}
						htmlData += '</ul>';
						infoModal.find('#class-description-modal-body')[0].innerHTML = htmlData;
						infoModal.find('#exampleModalLabel')[0].innerHTML = className;
	
						infoModal.modal();
				}else{
						infoModal.find('#class-description-modal-body')[0].innerHTML = mZ_get_registrants.get_registrants_error;
						infoModal.modal();
				}
			} // ./ Ajax Success
		}); // ./Ajax
	});  // ./Click
});