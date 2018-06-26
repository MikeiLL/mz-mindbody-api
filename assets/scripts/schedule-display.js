(function ($) {
    $(document).ready(function ($) {

        // Initialize some variables
        var nonce = mz_mindbody_schedule.nonce,
            atts = mz_mindbody_schedule.atts,
            container = $('#mzScheduleDisplay');

        // Run our Init function
        stripe_and_filter();

        /*
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
                        reset_navigation(this, buttons);
                        container.toggleClass('loader');
                        container.html(json.message);
                        stripe_and_filter();
                    }
                }
            })
                .fail(function (json) {
                    reset_navigation(this, buttons);
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
            var subText = ($(this).attr("data-sub") !== undefined) ? '<span class="sub-text">' + mz_mindbody_schedule.sub_by_text + '</span>' + ' ' : ' ';

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
            var subText = ($(this).attr("data-sub") !== undefined) ? '<span class="sub-text">' + mz_mindbody_schedule.sub_by_text + '</span>' + ' ' : ' ';
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
            var subText = ($(this).attr("data-sub") !== undefined) ? ' <span class="sub-text">(' + mz_mindbody_schedule.sub_by_text + ' ' + $(this).attr("data-sub") + ') </span>' + ' ' : ' ';
            var popUpContent = '<h3>' + staffName + ' ' + subText + '</h3><div class="mz-staffInfo" id="StaffInfo"></div>';

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
         * Modal Window to Register for a Class
         *
         * Also leads to options to login and sign-up with MBO
         *
         */
        $(document).on('click', "a[data-target=mzSignUpModal]", function (ev) {
            ev.preventDefault();
            var target = $(this).attr("href");
            var siteID = $(this).attr('data-siteID');
            var nonce = $(this).attr("data-nonce");
            var classID = $(this).attr("data-classID");
            var popUpContent = '<div class="modal__header"><h3>' + mz_mindbody_schedule.your_account + '</h3></div><div class="mz-classRegister" id="ClassRegister"></div>';
            popUpContent += '<i class="fa fa-spinner fa-3x fa-spin" style="position: fixed; top: 50%; left: 50%;"></i>';
            $("#mzSignUpModal").load(target, function () {
                $.colorbox({html: popUpContent, width: "75%", height: "80%", href: target});
                $("#mzSignUpModal").colorbox();
            });

            $.ajax({
                type: "GET",
                dataType: 'json',
                url: mz_mindbody_schedule.ajaxurl,
                data: {action: 'mz_register_for_class', nonce: nonce, siteID: siteID, classID: classID},
                success: function (json) {
                    if (json.type == "success") {
                        $('.fa-spinner').remove();
                        $('#ClassRegister').html(json.message);
                        // $('#ClassRegister').append('<div class="modal-footer"><a class="btn btn-primary" id="MBOLogout">Logout</a></div>');
                    } else {
                        $('#ClassRegister').html('ERROR REGISTERING FOR CLASS');
                        console.log(json);
                    }
                } // ./ Ajax Success
            }) // End Ajax
                .fail(function (json) {
                    $('#ClassRegister').html('ERROR REGISTERING FOR CLASS');
                    console.log(json);
                }); // End Fail

        });

        /**
         * Display MBO Account Registration form within Sign-Up Modal
         *
         *
         */
        $(document).on('click', "a#createMBOAccount", function (ev) {
            ev.preventDefault();
            var target = $(this).attr("href");
            var nonce = $(this).attr("data-nonce");
            var classID = $(this).attr("data-classID");
            $('#ClassRegister').html('<i class="fa fa-spinner fa-3x fa-spin" style="position: fixed; top: 50%; left: 50%;"></i>');
            $.ajax({
                type: "GET",
                dataType: 'json',
                url: mz_mindbody_schedule.ajaxurl,
                data: {action: 'mz_generate_signup_form', nonce: nonce, classID: classID},
                success: function (json) {
                    if (json.type == "success") {
                        $('.fa-spinner').remove();
                        $('#ClassRegister').html(json.message);
                    } else {
                        $('#ClassRegister').html('ERROR GENERATING SIGN-UP FORM');
                        console.log(json);
                    }
                } // ./ Ajax Success
            }) // End Ajax
                .fail(function (json) {
                    $('#ClassRegister').html('ERROR GENERATING THE SIGN-UP FORM');
                    console.log(json);
                }); // End Fail

        });

        /**
         * Display Client Schedule within Sign-Up Modal
         *
         *
         */
        $(document).on('click', "a#MBOSchedule", function (ev) {
            ev.preventDefault();
            var nonce = $(this).attr("data-nonce");
            $('#AddedToClass').html('<i class="fa fa-spinner fa-3x fa-spin" style="position: fixed; top: 50%; left: 50%;"></i>');
            $.ajax({
                type: "GET",
                dataType: 'json',
                url: mz_mindbody_schedule.ajaxurl,
                data: {action: 'mz_display_client_schedule', nonce: nonce},
                success: function (json) {
                    if (json.type == "success") {
                        $('#AddedToClass').html(json.message);
                    } else {
                        $('#AddedToClass').html('ERROR RETRIEVING YOUR SCHEDULE');
                        console.log(json);
                    }
                } // ./ Ajax Success
            }) // End Ajax
                .fail(function (json) {
                    $('#AddedToClass').html('ERROR RETRIEVING YOUR SCHEDULE');
                    console.log(json);
                }); // End Fail

        });

        /**
         * Create MBO Account and display Confirmation
         *
         *
         */
        $(document).on('submit', 'form[id="mzSignUp"]', function (ev) {
            ev.preventDefault();
            var target = $(this).attr("href");
            var form = $(this);
            var nonce = $(this).attr("data-nonce");
            var classID = $(this).attr("data-classID");
            var formData = form.serializeArray();
            $('#ClassRegister').html('<i class="fa fa-spinner fa-3x fa-spin" style="position: fixed; top: 50%; left: 50%;"></i>');
            $.ajax({
                type: "GET",
                dataType: 'json',
                url: mz_mindbody_schedule.ajaxurl,
                data: {action: 'mz_create_mbo_account', nonce: formData.nonce, classID: formData.classID, form: form.serialize()},
                success: function (json) {
                    if (json.type == "success") {
                        $('.fa-spinner').remove();
                        $('#ClassRegister').html(json.message);
                    } else {
                        $('#ClassRegister').html('ERROR CREATING ACCOUNT');
                        console.log(json);
                    }
                } // ./ Ajax Success
            }) // End Ajax
                .fail(function (json) {
                    $('#ClassRegister').html('ERROR CREATING ACCOUNT');
                    console.log(json);
                }); // End Fail

        });

        /**
         * Sign In to MBO
         */
        $(document).on('submit', 'form[id="mzLogIn"]', function (ev) {
            ev.preventDefault();
            var form = $(this);
            var formData = form.serializeArray();

            $('#ClassRegister').html('<i class="fa fa-spinner fa-3x fa-spin" style="position: fixed; top: 50%; left: 50%;"></i>');
            $.ajax({
                dataType: 'json',
                url: mz_mindbody_schedule.ajaxurl,
                type: form.attr('method'),
                data: {action: 'mz_client_log_in', form: form.serialize(), nonce: formData.nonce, classID: formData.classID},
                success: function(json) {
                    if (json.type == "success") {
                        $('#ClassRegister').html(json.message);
                    } else {
                        $('#ClassRegister').html('ERROR REGISTERING FOR CLASS');
                        console.log(json);
                    }
                } // ./ Ajax Success
            }) // End Ajax
                .fail(function (json) {
                    $('#ClassRegister').html('ERROR REGISTERING FOR CLASS');
                    console.log(json);
                }); // End Fail

        });

        /**
         * Logout of MBO
         *
         *
         */
        $(document).on('click', "#MBOLogout", function (ev) {
            ev.preventDefault();

            $('#ClassRegister').html('<i class="fa fa-spinner fa-3x fa-spin" style="position: fixed; top: 50%; left: 50%;"></i>');

            $.ajax({
                dataType: 'json',
                url: mz_mindbody_schedule.ajaxurl,
                data: {action: 'mz_client_log_out'},
                success: function(json) {
                    if (json.type == "success") {
                        $('#ClassRegister').html(json.message);
                    } else {
                        $('#ClassRegister').html('ERROR LOGGING OUT');
                        console.log(json);
                    }
                } // ./ Ajax Success
            }) // End Ajax
                .fail(function (json) {
                    $('#ClassRegister').html('ERROR LOGGING OUT');
                    console.log(json);
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