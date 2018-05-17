(function($) {
  $(document).ready(function($) {
    var nonce = mz_mindbody_schedule.nonce,
    atts = mz_mindbody_schedule.atts,
    container = $('#mzScheduleDisplay');
    $.ajax({
     type : "post",
     dataType : "json",
     url : mz_mindbody_schedule.ajaxurl,
     data : {action: 'mz_display_schedule', nonce: nonce, atts: atts},
     success: function(json) {
        if(json.type == "success") {
            container.toggleClass('loader');
            container.html(json.message);
        } else {
            container.toggleClass('loader');
            container.html(json.message);
        }
      }
    })
    .fail( function( json ) {
        console.log('fail');
        console.log(json);
        container.toggleClass('loader');
        container.html('Sorry but there was an error retrieving events.');
    }); // End Ajax
  });
})( jQuery );