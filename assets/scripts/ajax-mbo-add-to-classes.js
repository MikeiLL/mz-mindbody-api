$(document).ready(function($) {
	$('.mz_add_to_class').click(function(){
	
		var nonce = $(this).attr("data-nonce");
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
				 console.log("json success");
				 $(".mz_add_to_class"+classID).find(".signup").text(json.message);
				 $(".mz_description_holder").find(".visitMBO"+classID).removeAttr("style");
				 if ($(".mz_add_to_class"+classID).hasClass("fa-sign-in")){
							$(".mz_add_to_class"+classID).removeClass("fa-sign-in").addClass("fa-check-square-o");
							$(".mz_add_to_class"+classID).removeAttr('href').attr('title', mZ_add_to_classes.registered_message);
						}
			} else {
				console.log("json error");
				 $(".mz_add_to_class"+classID).find(".signup").text(json.message);
				 $(".mz_description_holder").find(".visitMBO"+classID).removeAttr("style");
				 if ($(".mz_add_to_class"+classID).hasClass("fa-sign-in")){
							$(".mz_add_to_class"+classID).removeClass("fa-sign-in").addClass("fa-check-square-o");
							$(".mz_add_to_class"+classID).removeAttr('href').attr('title', mZ_add_to_classes.registered_message);
						}
					}
			} // End Ajax success
		 
		}); // End Ajax
		
	}); // End click
});

