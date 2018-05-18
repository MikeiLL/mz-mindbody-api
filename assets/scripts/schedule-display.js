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

    $('#mzScheduleNavHolder .following, #mzScheduleNavHolder .previous').on('click',function(e) {
        e.preventDefault();
        container.html("");
        container.toggleClass('loader');
        var buttons = [].slice.call(document.getElementById('mzScheduleNavHolder').children);
        // Update attributes
        var offset = atts.offset = this.dataset.offset;
        // Update nav link "offset" data attribute
        if (this.className == 'following') {
            buttons.forEach( function(button) {
                button.setAttribute('data-offset', parseInt(button.getAttribute('data-offset')) + parseInt(1));
            });
        } else if (this.className == 'previous') {
            buttons.forEach( function(button) {
                button.setAttribute('data-offset', button.getAttribute('data-offset') - 1);
            });
        }
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
            container.html('Sorry but there was an error retrieving schedule.');
        }); // End Ajax
    }); // End click navigation
  }); // End document ready
})( jQuery );