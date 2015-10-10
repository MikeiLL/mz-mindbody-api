(function($){
$(document).ready(function($) {
		$("a[data-target=#mzModal]").click(function(ev) {
			ev.preventDefault();
			var target = $(this).attr("href");
			var maincontent = $(this).attr('data-maincontent');
      console.log( decodeURIComponent(maincontent) );
			// load the url and show modal on success
			$("#mzModal").load(target, function() { 
				 $("#mzModal").modal({show:true});  
				 $(".modal-body span").html(decodeURIComponent(maincontent));
			});
			// kill modal contents on hide
			    $('body').on('hidden.bs.modal', '#mzModal', function () {
			     $(this).removeData('bs.modal');
			   });	
		});
	});
	
$(document).ready(function($) {
		$("a[data-target=#mzStaffModal]").click(function(ev) {
			ev.preventDefault();
			var target = $(this).attr("href");
			var maincontent = $(this).attr('data-maincontent');
      console.log( 'i am here ' + maincontent );
			// load the url and show modal on success
			$("#mzStaffModal").load(target, function() { 
				 $("#mzStaffModal").modal({show:true});  
				 $("#staffBio").innerHTML = staffBio;
			});
			// kill modal contents on hide
			    $('body').on('hidden.bs.modal', '#mzStaffModal', function () {
			     $(this).removeData('bs.modal');
			   });	
		});
	});
})(jQuery);



