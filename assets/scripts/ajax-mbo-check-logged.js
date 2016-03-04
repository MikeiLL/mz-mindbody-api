(function($) {
	$(document).ready(function($) {
    setTimeout(checkVariableValue, 500);
    function checkVariableValue() {
    			alert("here I am.");
         $.ajax({
					type : "post",
					dataType : "json",
					url: mZ_check_session_logged.ajaxurl,
					data : {action: 'mz_mbo_check_session_logged'},
					success: function(json) {
						console.log(json);
						//if (newVal != currentSessionValue);
								//currentSessionValue = newVal;
								alert('Value Has Changed.');
								//doSomethingDifferent_or_refreshPage();
							} // End Ajax success
         }); // End Ajax
    } // End checkVariableValue
    
			/*$.ajax({
			 type : "post",
			 dataType : "json",
			 url : mZ_mindbody_api.ajaxurl,
			 data : {action: 'mz_mbo_check_session_logged', nonce: nonce, clientID: clientID, classID: classID},
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
								$(".mz_add_to_class"+classID).removeClass("fa-sign-in").addClass("fa-thumbs-o-down");
								$(".mz_add_to_class"+classID).removeAttr('href').attr('title', mZ_add_to_classes.not_registered_message);
							}
						}
				} // End Ajax success
		 
			}); // End Ajax*/
		
	});
})( jQuery );

