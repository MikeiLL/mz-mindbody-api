(function($){
$(document).ready(function($) {
	var mZ_row_count = 1;
		$('.mz_add_to_class').click(function(){
				var countSpan = $(this).closest(".mz_add_to_class").find(".count");
				var countVal = countSpan.text();
				var nuVal = parseInt(countVal) + mZ_row_count;
				mZ_row_count++;
				countSpan.text(nuVal);
				nonce = $(this).attr("data-nonce");
				clientID = $(this).attr("data-clientID");
				classID = $(this).attr("data-classID");
				$(this).closest(".mz_add_to_class").removeClass('mz_add_to_class');
				$(this).closest("#mz_add_to_class").addClass('mz_add_to_class'+nuVal);
				$(this).closest(".mz_description_holder").find('.visitMBO').removeClass('visitMBO');
				$(this).closest(".mz_description_holder").find("#visitMBO").addClass('visitMBO'+nuVal);
				$('.mz_add_to_class'+nuVal).find(".signup").text("MindBodyOnline...");
				    $.ajax({
					 type : "post",
					 dataType : "json",
					 url : mZ_add_to_classes.ajaxurl,
					 data : {action: 'mz_mbo_add_client_ajax', nonce: nonce, clientID: clientID, classID: classID},
					 success: function(json) {
						if(json.type == "success") {
						   $(".mz_add_to_class"+nuVal).find(".signup").text(json.message);
						   $(".mz_description_holder").find(".visitMBO"+nuVal).removeAttr("style");
						   //$(".mz_add_to_class"+nuVal).find(".fa-sign-in").switchClass('fa-sign-in', 'fa-check-square-o');
						}
						else {
						   $(".mz_add_to_class"+nuVal).find(".signup").text(json.message);
						   $(".mz_description_holder").find(".visitMBO"+nuVal).removeAttr("style");
						}
					 }
				  });  
		    });
		});
})(jQuery);
