(function ($) {
    $(document).ready(function ($) {

        // Initialize some variables
        var nonce = mz_mindbody_schedule.nonce,
            atts = mz_mindbody_schedule.atts,
            container = $('#mzScheduleDisplay');

        /*
         * Navigate Schedule
         *
         *
         */
        $('#mzScheduleNavHolder .following, #mzScheduleNavHolder .previous').on('click', function (e) {
            e.preventDefault();
            container.html("");
            container.toggleClass('loader');
            var buttons = [].slice.call(document.getElementById('mzScheduleNavHolder').children);
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
                data: {action: 'mz_display_schedule', nonce: nonce, atts: atts},
                success: function (json) {
                    if (json.type == "success") {
                        container.toggleClass('loader');
                        container.html(json.message);
                    } else {
                        reset_navigation(this, buttons);
                        container.toggleClass('loader');
                        container.html(json.message);
                    }
                }
            })
                .fail(function (json) {
                    reset_navigation(this, buttons);
                    console.log('fail');
                    console.log(json);
                    container.toggleClass('loader');
                    container.html('Sorry but there was an error retrieving schedule.');
                }); // End Ajax
        }); // End click navigation

        function reset_navigation(el, buttons) {
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


        /*
         * Class Description Modal
         *
         *
         */
        $(document).on('click', "a[data-target=mzModal]", function (e) {
            e.preventDefault();
            var target = $(this).attr("href"),
                staffName = this.getAttribute('data-staffName'),
                classDescription = decodeURIComponent(this.getAttribute('data-classDescription')),
                popUpContent = '<h3>' + this.innerHTML + ' ' + mz_mindbody_schedule.staff_preposition + ' ' + staffName + '</h3>';

            popUpContent += '<div class="mz-staffInfo" id="StaffInfo">' + classDescription + '</div>';

            // load the url and show modal on success
            $("#mzModal").load(target, function () {
                $.colorbox({html: popUpContent, width: "75%", height: "80%", href: target});
                $("#mzModal").colorbox();
            });
            return false;
        });

        /**
         * Show Registrants
         *
         *
         */
        $(document).on('click', "a[data-target=registrantModal]", function (e) {
            e.preventDefault();
            var target = $(this).attr("href");
            var classDescription = $(this).attr('data-classDescription');
            var staffName = $(this).attr('data-staffName');
            var staffImage = $(this).attr('data-staffImage');
            var className = $(this).attr("data-className");
            var classID = $(this).attr("data-classID");
            var nonce = $(this).attr("data-nonce");
            var popUpContent = '<div class="mz-classInfo">';
            popUpContent += '<h3>' + className + '</h3>';
            popUpContent += '<h4>' + mz_mindbody_schedule.staff_preposition + ' ' + staffName + '</h4>';

            if (typeof staffImage != 'undefined') {
                popUpContent += '<img class="mz-staffImage" src="' + staffImage + '" />';
            }

            var htmlClassDescription = '<div class="mz_modalClassDescription">';
            htmlClassDescription += "<div class='class-description'>" + decodeURIComponent(classDescription) + "</div></div>";
            popUpContent += htmlClassDescription;
            popUpContent += '</div>';

            popUpContent += '<h3>' + mz_mindbody_schedule.registrants_header + '</h3>';
            popUpContent += '<div id="modalRegistrants"><div id="ClassRegistrants" style="min-height:90px;">';
            popUpContent += '<i class="fa fa-spinner fa-3x fa-spin"></i></div></div>';
            $("#registrantModal").load(target, function () {
                $.colorbox({html: popUpContent, width: "75%", height: "80%", href: target});
                $("#registrantModal").colorbox();
            });
            $.ajax({
                type: "GET",
                dataType: 'json',
                url: mz_mindbody_schedule.ajaxurl,
                data: {action: 'mz_mbo_get_registrants', nonce: nonce, classID: classID},
                success: function (json) {
                    console.log(json);
                    if (json.type == "success") {
                        htmlRegistrants = '<ul class="mz-classRegistrants">';
                        if ($.isArray(json.message)) {
                            json.message.forEach(function (name) {
                                htmlRegistrants += '<li>' + name.replace('_', ' ') + '</li>';
                            });
                        } else {
                            htmlRegistrants += '<li>' + json.message + '</li>';
                        }
                        htmlRegistrants += '</ul>';
                        $('#modalRegistrants').find('#ClassRegistrants')[0].innerHTML = htmlRegistrants;
                    } else {
                        $('#modalRegistrants').find('#ClassRegistrants')[0].innerHTML = mz_mindbody_schedule.get_registrants_error;
                    }
                } // ./ Ajax Success

            }) // End Ajax
                .fail(function (json) {
                    reset_navigation(this, buttons);
                    console.log('fail');
                    console.log(json);
                    $('#modalRegistrants').find('#ClassRegistrants')[0].innerHTML = mz_mindbody_schedule.get_registrants_error;
                }); // End Fail
            return false;
        }); // End click

        /**
         * Staff Modal
         *
         *
         */
        $(document).on('click', "a[data-target=mzStaffScheduleModal]", function (ev) {
            ev.preventDefault();
            var target = $(this).attr("href");
            var staffID = $(this).attr('data-staffID');
            var siteID = $(this).attr('data-siteID');
            var staffName = $(this).attr('data-staffName');
            var nonce = $(this).attr("data-nonce");
            var subText = ($(this).attr("data-sub") !== undefined) ? '<span class="sub-text">' + mz_mindbody_schedule.sub_by_text + '</span>' + ' ' : ' ';
            var popUpContent = '<h3>' + subText + staffName + '</h3><div class="mz-staffInfo" id="StaffInfo"></div>';

            popUpContent += '<i class="fa fa-spinner fa-3x fa-spin" style="position: fixed; top: 50%; left: 50%;"></i>';
            $("#mzStaffScheduleModal").load(target, function () {
                $.colorbox({html: popUpContent, width: "75%", height: "80%", href: target});
                $("#mzStaffScheduleModal").colorbox();
            });
            $.ajax({
                type: "GET",
                dataType: 'json',
                url: mz_mindbody_schedule.ajaxurl,
                data: {action: 'mz_mbo_get_staff', nonce: nonce, staffID: staffID, siteID: siteID},
                success: function (json) {
                    if (json.type == "success") {
                        $('.fa-spinner').remove();
                        $('#StaffInfo').html(json.message);
                    } else {
                        $('#StaffInfo').html('ERROR FINDING STAFF INFO');
                    }
                } // ./ Ajax Success
            }) // End Ajax
            .fail(function (json) {
                $('#StaffInfo').html('ERROR RETURNING STAFF INFO');
            }); // End Fail
        });

        /**
         * Mode Select
         *
         *
         */
        if (mz_mindbody_schedule.mode_select !== '0') {
            if (mz_mindbody_schedule.mode_select == 1) {
                $('.filter-table').last().addClass('mz_hidden');
            } else { // Then assume it's 2
                $('.filter-table').first().addClass('mz_hidden');
            }
            $('#mzScheduleNavHolder').first().append($('<a id="mode-select" class="btn btn-xs mz-mode-select">' + mz_mindbody_schedule.initial + '</a>'));
            $('#mode-select').click(function () {
                $('.mz-schedule-display').each(function (i, item) {
                    $(item).toggleClass('mz_hidden');
                    $(item).toggleClass('mz_schedule_filter');
                });
                $('.mz_grid_date').toggleClass('mz_hidden');
                $('.filter-table').toggleClass('mz_hidden');
                $('#mode-select').text(function (i, text) {
                    return text == mz_mindbody_schedule.initial ? mz_mindbody_schedule.swap : mz_mindbody_schedule.initial;
                });
            });
        } // if mode button = 1

    }); // End document ready
})(jQuery);