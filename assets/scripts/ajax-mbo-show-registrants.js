	$(document).ready(function($) {
		$("a[data-target=#registrantModal]").click(function(ev) {
			//this is the teacher click function
			ev.preventDefault();
			var target = $(this).attr("href");
			var classDescription = $(this).attr('data-classDescription');
			var teacherName = $(this).find( "div" ).text();
			var teacherPicture = $(this).find( "img" ).attr('src');
			var className = $(this).attr("data-className");
			var popUpContent = "<h3>" + className + "</h3>" + /*"<img class='teacher-picture' src='" + teacherPicture + "' />*/"<div class='teacher-info'><div class='teacher-name'>" + teacherName + "</div><div class='teacher-description'>"  +  decodeURIComponent(classDescription) + "</div></div>";
			var nonce = $(this).attr("data-nonce");
			var classID = $(this).attr("data-classID");
			// load the url and show modal on success
			popUpContent += '<div id="ClassRegistrants"></div>';
			$("#registrantModal").load(target, function() { 
				 //$("#mzModal").modal({show:true}); 
				 $.colorbox({html: popUpContent, width:"75%"}); 
				 $("#registrantModal").colorbox();
				 //$(".modal-body span").html(decodeURIComponent(maincontent));
			});
			$.ajax({
				type: "GET",
				dataType: 'json',
				url : mZ_add_to_classes.ajaxurl,
				data : {action: 'mz_mbo_get_registrants', nonce: nonce, classID: classID},
				success: function(json) {
					console.log(json);
					if(json.type == "success") {
							htmlRegistrants = 'Yo Yo Yo<ul class="mz_class_registrants">';
							if ( $.isArray(json.message)  ) {
								json.message.forEach( function(name) {
									htmlRegistrants += '<li>' + name.replace('_', ' ') + '</li>';
									});
							} else {
								htmlRegistrants += '<li>' + json.message + '</li>';
							}
							htmlRegistrants += '</ul>';
							$('#registrantModal').find('#ClassRegistrants')[0].innerHTML = htmlRegistrants;
							//infoModal.modal();
							//$('#registrantModal').modal('show');
							console.log(htmlRegistrants);
					}else{
							$('#registrantModal').find('#class-description-modal-body')[0].innerHTML = mZ_get_registrants.get_registrants_error;
							//infoModal.modal();
					}
				} // ./ Ajax Success
			}); // ./Ajax
		});
	});
	
$(document).ready(function($) {
	$("a[data-target=#r0gistrantModal]").click(function(ev) {
		//this is the teacher click function
		ev.preventDefault();
		var target = $(this).attr("href");
		var maincontent = $(this).attr('data-maincontent');
		var teacherName = $(this).find( "div" ).text();
		var teacherPicture = $(this).find( "img" ).attr('src');
		var className = $(this).attr("data-className");
		var popUpContent = "<h3>" + className + "</h3>" + /*"<img class='teacher-picture' src='" + teacherPicture + "' />*/"<div class='teacher-info'><div class='teacher-name'>" + teacherName + "</div><div class='teacher-description'>"  +  decodeURIComponent(maincontent) + "</div></div>";
		

		$('.mz_get_registrants').click(function(){
			$(this).removeData('bs.modal');
			var nonce = $(this).attr("data-nonce");
			classID = $(this).attr("data-classID");
			classDescription = $(this).attr("data-classDescription");
			className = $(this).attr("data-className");
			//alert("The class ID is: " + classID);
			$('#registrantModal').find('#ClassTitle')[0].innerHTML = className;
			$('#registrantModal').find('#ClasseRegistrants')[0].innerHTML = '<i class="fa fa-spinner fa-3x fa-spin"></i>';
			var htmlClassDescription = '<div class="mz_modal_class_description">';
			htmlClassDescription +=  decodeURIComponent(classDescription) + '</div>';
			htmlClassDescription += '<h5 class="mz_registrants_header">' + mZ_get_registrants.registrants_header + '</h5>';
			$('#registrantModal').find('#class-description-modal-body')[0].innerHTML = htmlClassDescription;
			//$('#registrantModal').modal('show'); // open modal before ajax here to work with jquery 1.11, wordpress core jquery
			// alert("The class ID is: " + classID);
			$.colorbox({html: popUpContent, width:"75%"}); 
			$('#registrantModal').colorbox();
			$.ajax({
				type: "GET",
				dataType: 'json',
				url : mZ_add_to_classes.ajaxurl,
				data : {action: 'mz_mbo_get_registrants', nonce: nonce, classID: classID},
				success: function(json) {
					console.log(json);
					if(json.type == "success") {
							htmlRegistrants = '<ul class="mz_class_registrants">';
							if ( $.isArray(json.message)  ) {
								json.message.forEach( function(name) {
									htmlRegistrants += '<li>' + name.replace('_', ' ') + '</li>';
									});
							} else {
								htmlRegistrants += '<li>' + json.message + '</li>';
							}
							htmlRegistrants += '</ul>';
							$('#registrantModal').find('#ClassRegistrants')[0].innerHTML = htmlRegistrants;
							//infoModal.modal();
							//$('#registrantModal').modal('show');
					}else{
							$('#registrantModal').find('#class-description-modal-body')[0].innerHTML = mZ_get_registrants.get_registrants_error;
							//infoModal.modal();
					}
				} // ./ Ajax Success
			}); // ./Ajax
		});  // ./Click
	});
});
