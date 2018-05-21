(function ($) {
    'use strict';
    $(document).ready(function ($) {

        // Initialize some variables
        var nonce = mz_mindbody_schedule.nonce,
            atts = mz_mindbody_schedule.atts,
            container = $('#mzClearTransients');

        /*
        * Clear Transients
        *
        *
        */
        $('#mzClearTransients').on('click', function (e) {
            e.preventDefault();
            $.ajax({
                type: "post",
                dataType: "json",
                context: this,
                url: mz_mindbody_schedule.ajaxurl,
                data: {action: 'mz_mbo_clear_transients', nonce: nonce},
                success: function (json) {
                    if (json.type == "success") {
                        alert('Transients cleared.');
                    } else {
                        alert('Something went wrong.');
                    }
                }
            }) // Emd ajax
                .fail(function (json) {
                    console.log('fail');
                    console.log(json);
                    alert('Something went wrong.');
                });
        }); // End click
    }); // End document ready
})(jQuery);
