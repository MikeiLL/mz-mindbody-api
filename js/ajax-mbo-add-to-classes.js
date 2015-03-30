(function($){
$(document).ready(function($) {
		$( '.mz_add_to_class' ).click( function( e ) {
			var mz_mbo_params = {"ajaxurl":"http:\/\/localhost:8888\/wp-admin\/admin-ajax.php"};
			var link = this;
			var id   = $( link ).attr( 'data-id' );
			var nonce = $( link ).attr( 'data-nonce' );
			var clientID = $( link ).attr( 'data-clientID' );
 
			// This is what we are sending the server
			var data = {
			    action: 'mz_mindbody_ajax_add_to_class',
			    classID: id,
			    nonce: nonce,
			    clientID: clientID,
			}
			// Change the anchor text of the link
			// To provide the user some immediate feedback
			$( link ).text( 'Adding you to the Class' );
 
			// Post to the server
			$.post( mz_mbo_params.ajaxurl, data, function( data, textStatus, jqxhr ) {
			// Parse the XML response with jQuery
			
			//console.log(jqxhr.responseText);
			console.log(data.length);
			// Get the Status
			data = data.substring(208);
			
			var xmlDocument = $.parseXML(data);
			var xml = $(xmlDocument.documentElement);
			var status = xml.find("response_data").text();
			var message = xml.find("supplemental message").text();
			console.log(status); // empty string
			if ( status == "success" ) {
			   $( link )
			   .parent()
			   .after("<p><strong>" + message + "</strong></p>").remove();
			} else {
			   // An error occurred, alert an error message
			   alert( message );
			}
			});
			// Prevent the default behavior for the link
			e.preventDefault();
		    });
	});	
})(jQuery);
