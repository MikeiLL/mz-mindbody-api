(function ($) {
    $(document).ready(function ($) {

        // Initialize some variables
        var nonce = mz_mindbody_schedule.nonce,
            atts = mz_mindbody_schedule.atts,
            // Just one location for use in general MBO site link
            location = atts.locations[0].toString(),
            siteID = atts.account ? atts.account : mz_mindbody_schedule.account;

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
                data: {action: 'mz_register_for_class', nonce: nonce, siteID: siteID, classID: classID, location: location},
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
                data: {action: 'mz_display_client_schedule', nonce: nonce, location: location, siteID: siteID},
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

    }); // End document ready
})(jQuery);