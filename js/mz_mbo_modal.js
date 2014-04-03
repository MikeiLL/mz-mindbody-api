jQuery(document).ready(function($) {
$("a[data-target=#myModal]").click(function(ev) {
    ev.preventDefault();
    var target = $(this).attr("href");
    // load the url and show modal on success
    $("#myModal").load(target, function() { 
         $("#myModal").modal({show:true}); 
    });
});
});
