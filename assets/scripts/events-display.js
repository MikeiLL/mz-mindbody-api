import './signup-modals';

(function ($) {
    $(document).ready(function ($) {

        // Initialize some variables
        var spinner = '<div class="d-flex justify-content-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div>',
            container = $("#mzEventsDisplay"),
            atts = mz_mindbody_schedule.atts;
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
                url: mz_mindbody_schedule.ajaxurl,
                data: {action: 'mz_display_events', nonce: mz_mindbody_schedule.nonce, atts: atts},
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

        /**
         * Event Description Modal
         *
         *
         */
        $(document).on('click', "a[data-target=mzDescriptionModal]", function (e) {
            e.preventDefault();
            var target = $(this).attr("href"),
                staffName = this.getAttribute('data-staffName'),
                eventImage = this.getAttribute('data-eventImage'),
                classDescription = decodeURIComponent(this.getAttribute('data-classDescription')),
                popUpContent = '<h3>' + this.innerHTML + ' ' + mz_mindbody_schedule.with + ' ' + staffName + '</h3>';

            popUpContent += '<div class="mz-classInfo" id="ClassInfo">';
            popUpContent += '<p><img src="' + eventImage + '" class="mz_modal_event_image_body">' + classDescription + '</p>';
            popUpContent += '</div>';

            // load the url and show modal on success
            $("#mzModal").load(target, function () {
                $.colorbox({html: popUpContent, href: target});
                $("#mzModal").colorbox();
            });
            return false;
        });

        /**
         * Staff Modal
         *
         *
         */
        $(document).on('click', "a[data-target=mzStaffScheduleModal]", function (ev) {
            ev.preventDefault();
            var target = $(this).attr("href");
            var staffName = $(this).attr('data-staffName');
            var staffBio = decodeURIComponent($(this).attr('data-staffBio'));
            var staffImage = $(this).attr('data-staffImage');
            var popUpContent = '<h3>' + staffName + '</h3><div class="mz-staffInfo" id="StaffInfo">';
            popUpContent += '<p><img src="' + staffImage + '" class="mz_modal_staff_image_body">' + staffBio + '</p>';
            popUpContent += '</div>';

            $("#mzModal").load(target, function () {
                $.colorbox({html: popUpContent, href: target});
                $("#mzModal").colorbox();
            });
        });



        /**
         * Location Filter
         *
         * Hide or Display events based on location when buttons clicked
         */
        $(document).on('click', ".filter_btn", function (ev) {
            ev.preventDefault();
            $('#locations_filter').children('a').removeClass('active');
            if (this.dataset.location === 'all'){
                $('.mz_full_listing_event').hide();
                $('.mz_full_listing_event').show(1000);
            } else {
                $('.mz_full_listing_event').hide();
                $('.'+this.dataset.location).show(1000);
            }
            $(this).toggleClass('active');
        });

    }); // End document ready
})(jQuery);