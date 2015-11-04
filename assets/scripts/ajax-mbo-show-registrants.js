$(document).ready(function($) {
			var infoModal = $('#registrantModal');
			$('.mz_get_registrants').click(function(){
					var nonce = $(this).attr("data-nonce");
					classID = $(this).attr("data-classID");
					//alert("The class ID is: " + classID);
					$.ajax({
							type: "GET",
							dataType: 'json',
						 url : mZ_add_to_classes.ajaxurl,
						 data : {action: 'mz_mbo_get_registrants', nonce: nonce, classID: classID},
						 success: function(json) {
							console.log(json);
							alert("in there!");
							if(json.type == "success") {
									var htmlData = '<ul><li>';
									htmlData += json.message;
									htmlData += 'hi, ill';
									htmlData += '</li></ul>';
									infoModal.find('#class-description-modal-body')[0].innerHTML = htmlData;
									infoModal.modal();
							}else{
									infoModal.find('#class-description-modal-body')[0].innerHTML = "bla bla bla";
									infoModal.modal();
							}
					}
		 });
	});
});