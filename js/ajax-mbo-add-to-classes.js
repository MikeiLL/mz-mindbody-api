(function($){
$(document).ready(function($) {
	console.log($(this).closest(".mz_add_to_class").find(".count"));
		$('.mz_add_to_class').click(function(){
				var countSpan = $(this).closest(".mz_add_to_class").find(".count");
				var countVal = countSpan.text();
				var nuVal = parseInt(countVal) + 1;
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
