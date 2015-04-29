(function($){
$(document).ready(function($) {
		$('.mz_add_to_class').click(function(){
				nonce = $(this).attr("data-nonce");
				clientID = $(this).attr("data-clientID");
				classID = $(this).attr("data-classID");
				    $.ajax({
					 type : "post",
					 dataType : "json",
					 url : mZ_add_to_classes.ajaxurl,
					 data : {action: 'mz_mbo_add_client_ajax', nonce: nonce, clientID: clientID, classID: classID},
					 success: function(json) {
					 	console.log("we did it!");
					 	alert(json.message);
						if(json.type == "success") {
						   $("#mz_add_to_class").html(json.message);
						}
						else {
						   $("#mz_add_to_class").html(json.message);
						}
					 }
				  });  
		    });
		});
})(jQuery);
