(function ($) {
    $(document).ready(function ($) {

        // Initialize some variables
        var spinner = '<i class="fa fa-spinner fa-3x fa-spin" style="position: fixed; top: 50%; left: 50%;"></i>',
            container = $("#mzEventsDisplay"),
            atts = mz_mindbody_events.atts;
            // TODO use Ajax event handlers to globally handle loader spinners: https://stackoverflow.com/a/40513161/2223106


        /**
         * Navigate Schedule
         *
         *
         */
        $('#mzEventsNavHolder .following, #mzEventsNavHolder .previous').on('click', function (e) {
            e.preventDefault();

            container.children().each( function (e){
                $(this).html('');
            });
            container.toggleClass('loader');
            var buttons = [].slice.call(document.getElementById('mzEventsNavHolder').children);
            // Update attributes
            var offset = atts.offset = this.dataset.offset;
            // Update nav link "offset" data attribute
            if (this.className == 'following') {
                buttons.forEach(function (button) {
                    button.setAttribute('data-offset', parseInt(button.getAttribute('data-offset')) + parseInt(1));
                });
            } else if (this.className == 'previous') {
                buttons.forEach(function (button) {
                    button.setAttribute('data-offset', button.getAttribute('data-offset') - 1);
                });
            }
            $.ajax({
                type: "post",
                dataType: "json",
                context: this,
                url: mz_mindbody_events.ajaxurl,
                data: {action: 'mz_display_events', nonce: mz_mindbody_events.nonce, atts: atts},
                success: function (json) {
                    if (json.type == "success") {
                        container.toggleClass('loader');
                        document.getElementById("mzEventsDisplay").innerHTML = json.message;
                        console.log(json);
                        document.getElementById("eventsDateRangeDisplay").innerHTML = json.date_range;
                        console.log(json.date_range);
                    } else {
                        mz_reset_navigation(this, buttons);
                        container.toggleClass('loader');
                        container.html(json.message);
                    }
                }
            })
                .fail(function (json) {
                    mz_reset_navigation(this, buttons);
                    container.toggleClass('loader');
                    container.html('Sorry but there was an error retrieving schedule.');
                }); // End Ajax
        }); // End click navigation

        function mz_reset_navigation(el, buttons) {
            // Reset nav link "offset" data attribute
            if (el.className == 'previous') {
                buttons.forEach(function (button) {
                    button.setAttribute('data-offset', parseInt(button.getAttribute('data-offset')) + parseInt(1));
                });
            } else if (el.className == 'following') {
                buttons.forEach(function (button) {
                    button.setAttribute('data-offset', button.getAttribute('data-offset') - 1);
                });
            }
        }



    }); // End document ready
})(jQuery);