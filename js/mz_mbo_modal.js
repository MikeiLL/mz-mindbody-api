(function($){
$(document).ready(function($) {
		$("a[data-target=#mzModal]").click(function(ev) {
			ev.preventDefault();
			var target = $(this).attr("href");
			// load the url and show modal on success
			$("#mzModal").load(target, function() { 
				 $("#mzModal").modal({show:true});  
			});
			// kill modal contents on hide
            $('body').on('hidden.bs.modal', '#mzModal', function () {
             $(this).removeData('bs.modal');
           });	
		});
		
	});	

})(jQuery);