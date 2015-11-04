$(document).ready(function($) {
			var infoModal = $('#registrantModal');
			var nonce = $(this).attr("data-nonce");
			var classID = $(this).attr("data-classID");
			$('.mz_get_registrants').click(function(){
					alert("The class ID is: " + classID);
					$.ajax({
							type: "GET",
							//url: '/api/menu-item/'+$(this).data('id'),
							dataType: 'json',
						 url : mZ_add_to_classes.ajaxurl,
						 data : {action: 'mz_mbo_get_registrants', nonce: nonce, classID: classID},
						 success: function(json) {
							if(json.type == "success") {
								console.log(classID);
									var htmlData = '<ul><li>';
									htmlData += fakeResponse.name;
									htmlData += '</li></ul>';
									infoModal.find('#modal-body')[0].innerHTML = htmlData;
									infoModal.modal();
							}
					}
		 });
	});
});