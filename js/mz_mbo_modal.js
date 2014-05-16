(function($){
$(document).ready(function($) {
		$("a[data-target=#myModal]").click(function(ev) {
			ev.preventDefault();
			var target = $(this).attr("href");
			// load the url and show modal on success
			$("#myModal").load(target, function() { 
				 $("#myModal").modal({show:true}); 
			});
		});
	});	
})(jQuery);
/*
http://stackoverflow.com/questions/20590227/twitter-bootstrap-how-to-clear-out-a-modal
$(document).on 'click', '.close', ->
    $(this).parent().fadeOut -> $(this).html('')
http://stackoverflow.com/questions/12286332/twitter-bootstrap-remote-modal-shows-same-content-everytime
    $('body').on('hidden.bs.modal', '.modal', function () {
  $(this).removeData('bs.modal');
});
*/