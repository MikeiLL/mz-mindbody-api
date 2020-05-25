import './signup-modals';
(function ($) {
    $(document).ready(function ($) {

        // Initialize some variables
        var nonce = mz_mindbody_schedule.nonce,
            atts = mz_mindbody_schedule.atts,
            container = $('#mzScheduleDisplay'),
            htmlRegistrants = '',
            // TODO use Ajax event handlers to globally handle loader spinners: https://stackoverflow.com/a/40513161/2223106
            spinner = '<i class="fa fa-spinner fa-3x fa-spin" style="position: fixed; top: 50%; left: 50%;"></i>';

        // Some colorbox global settings
        $.colorbox.settings.width  = ($(window).innerWidth() <= 500) ? '95%' : '75%';
        $.colorbox.settings.height = '75%';

        /** Colorbox resize function
         * source: https://github.com/jackmoore/colorbox/issues/158
         */
        var resizeTimer;
        function resizeColorBox()
        {
            if (resizeTimer) clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                if (jQuery('#cboxOverlay').is(':visible')) {
                    jQuery.colorbox.resize({width:'90%', height:'90%'});
                }
            }, 300);
        }

        // Resize Colorbox when resizing window or changing mobile device orientation
        $(window).resize(resizeColorBox);
        window.addEventListener("orientationchange", resizeColorBox, false);

        // Run our Init function
        stripe_and_filter();

        /**
         * Navigate Schedule
         *
         *
         */
        $('#mzScheduleNavHolder .following, #mzScheduleNavHolder .previous').on('click', function (e) {
            e.preventDefault();
            container.children().each( function (e){
                $(this).html('');
            });
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
                        if (json.grid && json.horizontal) {
                            document.getElementById("gridDisplay").innerHTML = json.grid;
                            document.getElementById("horizontalDisplay").innerHTML = json.horizontal;
                        } else if (json.grid) {
                            document.getElementById("gridDisplay").innerHTML = json.grid;
                        } else {
                            document.getElementById("horizontalDisplay").innerHTML = json.horizontal;
                        }
                        stripe_and_filter();
                    } else {
                        mz_reset_navigation(this, buttons);
                        container.toggleClass('loader');
                        container.html(json.message);
                        stripe_and_filter();
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
         * Class Description Modal
         *
         *
         */
        $(document).on('click', "a[data-target=mzModal]", function (e) {
            e.preventDefault();
            var target = $(this).attr("href"),
                staffName = this.getAttribute('data-staffName'),
                classDescription = decodeURIComponent(this.getAttribute('data-classDescription')),
                popUpContent = '<h3>' + this.innerHTML + ' ' + mz_mindbody_schedule.with + ' ' + staffName + '</h3>';
            var subText = ($(this).attr("data-sub") !== undefined) ? '<span class="sub-text">' + mz_mindbody_schedule.sub_by_text + '</span>' + ' ' : ' ';

            popUpContent += '<div class="mz-staffInfo" id="StaffInfo">' + classDescription + '</div>';

            // load the url and show modal on success
            $("#mzModal").load(target, function () {
                $.colorbox({html: popUpContent, href: target});
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
            var subText = ($(this).attr("data-sub") !== undefined) ? '<span class="sub-text">' + mz_mindbody_schedule.sub_by_text + '</span>' + ' ' : ' ';
            popUpContent += '<h3>' + className + '</h3>';
            popUpContent += '<h4>' + mz_mindbody_schedule.with + ' ' + staffName + '</h4>';

            if (typeof staffImage != 'undefined') {
                popUpContent += '<img class="mz-staffImage" src="' + staffImage + '" />';
            }

            var htmlClassDescription = '<div class="mz_modalClassDescription">';
            htmlClassDescription += "<div class='class-description'>" + decodeURIComponent(classDescription) + "</div></div>";
            popUpContent += htmlClassDescription;
            popUpContent += '</div>';

            popUpContent += '<h3>' + mz_mindbody_schedule.registrants_header + '</h3>';
            popUpContent += '<div id="modalRegistrants"><div id="ClassRegistrants" style="min-height:90px;">';
            popUpContent += spinner;
            $("#registrantModal").load(target, function () {
                $.colorbox({html: popUpContent, href: target});
                $("#registrantModal").colorbox();
            });
            $.ajax({
                type: "GET",
                dataType: 'json',
                url: mz_mindbody_schedule.ajaxurl,
                data: {action: 'mz_mbo_get_registrants', nonce: nonce, classID: classID},
                success: function (json) {
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
                    mz_reset_navigation(this, buttons);
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
            var subText = ($(this).attr("data-sub") !== undefined) ? ' <span class="sub-text">(' + mz_mindbody_schedule.sub_by_text + ' ' + $(this).attr("data-sub") + ') </span>' + ' ' : ' ';
            var popUpContent = '<h3>' + staffName + ' ' + subText + '</h3><div class="mz-staffInfo" id="StaffInfo"></div>';

            popUpContent += '<i class="fa fa-spinner fa-3x fa-spin" style="position: fixed; top: 50%; left: 50%;"></i>';
            $("#mzStaffScheduleModal").load(target, function () {
                $.colorbox({html: popUpContent, href: target});
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
         * Display either grid or horizontal schedule depending on user input from button
         */
        if (mz_mindbody_schedule.mode_select !== '0') {
            $('#mzScheduleNavHolder').first().append($('<a id="mode-select" class="btn btn-xs mz-mode-select">' + mz_mindbody_schedule.initial + '</a>'));
            $('#mode-select').click(function () {
                $('.mz-schedule-display').each(function (i, item) {
                    $(item).toggleClass('mz_hidden');
                    $(item).toggleClass('mz_schedule_filter');
                });
                stripe_and_filter();
                $('#mode-select').text(function (i, text) {
                    return text == mz_mindbody_schedule.initial ? mz_mindbody_schedule.swap : mz_mindbody_schedule.initial;
                });
            });
        } // if mode button = 1

        /**
         * Stripe the Table and if Filter is enabled, init the filter.
         */
        function stripe_and_filter() {
            /*
             * Filter Table Init
             *
             */
            var stripeTable = function (table) { //stripe the table (jQuery selector)
                table.find('tr').removeClass('striped').filter(':visible:even').addClass('striped');
            };

            if ($('table.mz-schedule-filter').length) {
                $('table.mz-schedule-filter').filterTable({
                    callback: function (term, table) {
                        stripeTable(table);
                    }, // call the striping after every change to the filter term
                    placeholder: mz_mindbody_schedule.filter_default,
                    highlightClass: 'alt',
                    inputType: 'search',
                    label: mz_mindbody_schedule.label,
                    selector: mz_mindbody_schedule.selector,
                    quickListClass: 'mz_quick_filter',
                    quickList: [mz_mindbody_schedule.quick_1, mz_mindbody_schedule.quick_2, mz_mindbody_schedule.quick_3],
                    locations: mz_mindbody_schedule.Locations_dict
                });
                stripeTable($('table.mz-schedule-filter')); //stripe the table for the first time
            } else {
                // No filter
                stripeTable($('table.mz-schedule-table')); //stripe the table for the first time
            }

            /**
             * Disable sign-up buttons that occur prior to present time
             */
            $('.mz_date_display').each(function(key, value){
                if(this.dataset.time){
                    // Get rid of the T and replace - with / for Safari
                    if (Date.parse(this.dataset.time.replace(/-/g, '/').replace(/T/g, ' ')) < Date.now()) {
                        $(this).find('a').addClass('disabled');
                    }
                }
            });

            /**
             * Disable grid sign-up buttons that occur prior to present time
             */
            $('.grid-sign-up-button').each(function(key, value){
                if(this.dataset.time){
                    // Get rid of the T and replace - with / for Safari
                    if (Date.parse(this.dataset.time.replace(/-/g, '/').replace(/T/g, ' ')) < Date.now()) {
                        $(this).addClass('disabled');
                    }
                }
            });

            /**
             * Loop through and display sub-details
             */
            $("a[data-target=mzStaffScheduleModal]").each( function(key, value){
                if (this.dataset.sub && !this.dataset.marked_as_sub){
                    $(this).after('&nbsp;<a href="#" title="' + mz_mindbody_schedule.sub_by_text + ' ' + this.dataset.sub + '" style="text-decoration:none;" onclick="return false"><svg height="20" width="20">' +
                        '<circle cx="10" cy="10" r="8" stroke="black" stroke-width="1" fill="white" />' +
                        '<text x="50%" y="50%" text-anchor="middle" fill="black" font-size="15px" font-family="Arial" dy=".25em">s</text>' +
                    '</svg></a>');
                    // Only do this once
                    this.dataset.marked_as_sub = true;
                }
            });

        }

    }); // End document ready
})(jQuery);