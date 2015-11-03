$(document).ready(function($) {
		$('.mz_add_to_class').click(function(){
				nonce = $(this).attr("data-nonce");
				clientID = $(this).attr("data-clientID");
				classID = $(this).attr("data-classID");
				$(this).closest(".mz_add_to_class").removeClass('mz_add_to_class');
				$(this).closest("#mz_add_to_class").addClass('mz_add_to_class'+classID);
				$(this).closest(".mz_description_holder").find('.visitMBO').removeClass('visitMBO');
				$(this).closest(".mz_description_holder").find("#visitMBO").addClass('visitMBO'+classID);
				$('.mz_add_to_class'+classID).find(".signup").text("MindBodyOnline...");
				     $.ajax({
					 type : "post",
					 dataType : "json",
					 url : mZ_add_to_classes.ajaxurl,
					 data : {action: 'mz_mbo_add_client', nonce: nonce, clientID: clientID, classID: classID},
					 success: function(json) {
						if(json.type == "success") {
							console.log(classID);
						   $(".mz_add_to_class"+classID).find(".signup").text(json.message);
						   $(".mz_description_holder").find(".visitMBO"+classID).removeAttr("style");
						   if ($(".mz_add_to_class"+classID).hasClass("fa-sign-in")){
						   	$(".mz_add_to_class"+classID).removeClass("fa-sign-in").addClass("fa-check-square-o");
						   	$(".mz_add_to_class"+classID).removeAttr('href').attr('title', mZ_add_to_classes.registered_message);
						   	}
						}
						else {
						   $(".mz_add_to_class"+classID).find(".signup").text(json.message);
						   $(".mz_description_holder").find(".visitMBO"+classID).removeAttr("style");
						   if ($(".mz_add_to_class"+classID).hasClass("fa-sign-in")){
						   	$(".mz_add_to_class"+classID).removeClass("fa-sign-in").addClass("fa-check-square-o");
						   	$(".mz_add_to_class"+classID).removeAttr('href').attr('title', mZ_add_to_classes.registered_message);
						   	}
						}
					 }
				  }); 
		    });
		});

(function() {
    var infoModal = $('#myModal');
    $('.modal-toggle').on('click', function(){
        $.ajax({
            type: "GET",
            url: '/api/menu-item/'+$(this).data('id'),
            dataType: 'json',
            error: function(data){
                fakeResponse = {"id":4,"menu_category_id":446,"name":"kunzereichert","description":"Dolores impedit ut doloribus et a et aut.","price":"999.99","created_at":"2015-04-10 05:55:23","updated_at":"2015-04-10 05:55:23"}
;
                var htmlData = '<ul><li>';
                htmlData += fakeResponse.name;
                htmlData += '</li></ul>';
                infoModal.find('#modal-body')[0].innerHTML = htmlData;
                infoModal.modal();
            }
        });
    });
})(jQuery);