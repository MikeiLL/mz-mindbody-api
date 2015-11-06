$(document).ready(function($) {
	var infoModal = $('#registrantModal');
	$('.mz_get_registrants').click(function(){
		$(this).removeData('bs.modal');
		var nonce = $(this).attr("data-nonce");
		classID = $(this).attr("data-classID");
		classDescription = $(this).attr("data-classDescription");
		className = $(this).attr("data-className");
		//alert("The class ID is: " + classID);
		infoModal.find('#ClassTitle')[0].innerHTML = className;
		infoModal.find('#ClasseRegistrants')[0].innerHTML = '<i class="fa fa-spinner fa-3x fa-spin"></i>';
		var htmlClassDescription = '<div class="mz_modal_class_description">';
		htmlClassDescription +=  decodeURIComponent(classDescription) + '</div>';
		htmlClassDescription += '<h5 class="mz_registrants_header">' + mZ_get_registrants.registrants_header + '</h5>';
		infoModal.find('#class-description-modal-body')[0].innerHTML = htmlClassDescription;
		infoModal.modal(); // open modal before ajax here to work with jquery 1.11, wordpress core jquery
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
						infoModal.find('#ClasseRegistrants')[0].innerHTML = htmlRegistrants;
						//infoModal.modal();
				}else{
						infoModal.find('#class-description-modal-body')[0].innerHTML = mZ_get_registrants.get_registrants_error;
						//infoModal.modal();
				}
			} // ./ Ajax Success
		}); // ./Ajax
	});  // ./Click
});