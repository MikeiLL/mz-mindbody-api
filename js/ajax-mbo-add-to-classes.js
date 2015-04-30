(function($){
$(document).ready(function($) {
	var mZ_row_count = 1;
		$('.mz_add_to_class').click(function(){
				if ($(this).attr("data-login") == not_logged_in ){
						$(".mz_add_to_class").find(".signup").text("Login to Sign-Up");
						return;
					}
				var countSpan = $(this).closest(".mz_add_to_class").find(".count");
				var countVal = countSpan.text();
				var nuVal = parseInt(countVal) + mZ_row_count;
				mZ_row_count++;
				countSpan.text(nuVal);
				console.log(countSpan.text());
				nonce = $(this).attr("data-nonce");
				clientID = $(this).attr("data-clientID");
				classID = $(this).attr("data-classID");
				$(this).closest(".mz_add_to_class").removeClass('mz_add_to_class');
				$(this).closest("#mz_add_to_class").addClass('mz_add_to_class'+nuVal);
				$('.mz_add_to_class'+nuVal).find(".signup").text("MindBodyOnline...");
				    $.ajax({
					 type : "post",
					 dataType : "json",
					 url : mZ_add_to_classes.ajaxurl,
					 data : {action: 'mz_mbo_add_client_ajax', nonce: nonce, clientID: clientID, classID: classID},
					 success: function(json) {
					 	console.log($("#mz_add_to_class"));
						if(json.type == "success") {
						   $(".mz_add_to_class"+nuVal).find(".signup").text(json.message);
						}
						else {
						   $(".mz_add_to_class"+nuVal).find(".signup").text(json.message);
						}
					 }
				  });  
		    });
		});
})(jQuery);
