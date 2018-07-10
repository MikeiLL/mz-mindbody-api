(function ($) {
    $(document).ready(function ($) {

        // Initialize some variables
        var nonce = mz_mindbody_schedule.nonce,
            atts = mz_mindbody_schedule.atts,
            // Just one location for use in general MBO site link
            location = atts.locations[0].toString(),
            siteID = atts.account ? atts.account : mz_mindbody_schedule.account,
            spinner = '<i class="fa fa-spinner fa-3x fa-spin" style="position: fixed; top: 50%; left: 50%;"></i>';

        /**
         * Initial Modal Window to Register for a Class
         *
         * Also leads to options to login and sign-up with MBO
         *
         */
        $(document).on('click', "a[data-target=mzSignUpModal]", function (ev) {
            ev.preventDefault();

            var target = $(this).attr("href");
            var siteID = $(this).attr('data-siteID');
            var nonce = $(this).attr("data-nonce");
            var location = $(this).attr("data-location");
            var classID = $(this).attr("data-classID");
            var className = $(this).attr("data-className");
            var staffName = $(this).attr("data-staffName");
            var classTime = $(this).attr("data-time");
            var popUpContent = '<div class="modal__header"><h3>' + mz_mindbody_schedule.your_account + '</h3></div>';

            var class_registration_content = classTime + ' ' + className + ' ' + mz_mindbody_schedule.staff_preposition + ' ' + staffName;

            if ( $('[data-loggedMBO]').attr("data-loggedMBO") == 0 ) { // Not signed in

                var login_form = get_login_form({
                    classID: classID,
                    className: className,
                    staffName: staffName,
                    classTime: classTime,
                    location: location
                });

                class_registration_content += login_form.html();

            } else { // We are signed in

                var logged_in = true;

                class_registration_content += '<hr/>';

                class_registration_content += '<button class="btn btn-primary" data-location="'+location+'" data-siteID="siteID" data-nonce="'+nonce+'" data-classID="'+classID+'" id="signUpForClass">' + mz_mindbody_schedule.confirm_signup + '</button>';

            }

            popUpContent += '<div class="mz-classRegister" id="classRegister">' + class_registration_content + '<div id="registrationStatus"></div></div>';

            $("#mzSignUpModal").load(target, function () {
                $.colorbox({html: popUpContent,  href: target});
                $("#mzSignUpModal").colorbox();
            });

        });

        /**
         * Register for a class
         */
        $(document).on('click', '#signUpForClass', function (ev) {
            ev.preventDefault();
            var siteID = $(this).attr('data-siteID');
            var nonce = $(this).attr("data-nonce");
            var location = $(this).attr("data-location");
            var classID = $(this).attr("data-classID");
            //$('#ClassRegister').html(spinner);
            $(this).prop("disabled",true).after('<i class="fa fa-spinner fa-3x fa-spin" style="position: fixed; top: 50%; left: 50%;"></i>');
            $.ajax({
                type: "GET",
                dataType: 'json',
                url: mz_mindbody_schedule.ajaxurl,
                context: this,
                data: {
                    action: 'mz_register_for_class',
                    nonce: nonce,
                    siteID: siteID,
                    classID: classID,
                    location: location
                },
                success: function (json) {
                    $(this).remove();
                    $('.fa-spinner').remove();
                    if (json.type == "success") {
                        $('#registrationStatus').html(json.message);
                    } else {
                        $('#registrationStatus').html('ERROR REGISTERING FOR CLASS. ' + json.message);
                        console.log(json);
                    }
                } // ./ Ajax Success
            }) // End Ajax
                .fail(function (json) {
                    $('#registrationStatus').html('ERROR REGISTERING FOR CLASS. ' + json.message);
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
            var result = { };
            $.each($('form').serializeArray(), function() {
                result[this.name] = this.value;
            });
            $('#classRegister').html(spinner);

            $.ajax({
                dataType: 'json',
                url: mz_mindbody_schedule.ajaxurl,
                type: form.attr('method'),
                context: this, // So we have access to form data within ajax results
                data: {action: 'mz_client_log_in',
                        form: form.serialize(),
                        nonce: result.nonce,
                        classID: result.classID,
                        staffName: result.staffName,
                        classTime: result.classTime,
                        location: result.location},
                success: function(json) {
                    var formData = $(this).serializeArray();
                    var result = { };
                    $.each($('form').serializeArray(), function() {
                        result[this.name] = this.value;
                    });

                    var class_registration_content = result.classTime + ' ' + result.className + ' ' + mz_mindbody_schedule.staff_preposition + ' ' + result.staffName;

                    if (json.type == "success") {

                        $('#classRegister').html(json.message);

                        // Store in DOM that we are logged in now.
                        $('[data-loggedMBO]').attr("data-loggedMBO", 1);

                        class_registration_content += '<hr/>';

                        class_registration_content += '<button class="btn btn-primary" data-nonce="'+result.nonce+'" data-location="'+result.location+'" data-classID="'+result.classID+'" id="signUpForClass">' + mz_mindbody_schedule.confirm_signup + '</button>';

                        class_registration_content += '<div class="modal__footer btn-group" class="signupModalFooter">\n' +
                            '    <a class="btn btn-primary" data-nonce="'+mz_mindbody_schedule.signup_nonce+'" id="MBOSchedule" target="_blank">My Classes</a>\n' +
                            '    <a href="https://clients.mindbodyonline.com/ws.asp?&amp;sLoc='+mz_mindbody_schedule.location+'&studioid='+mz_mindbody_schedule.siteID+'>" class="btn btn-primary btn-xs" id="MBOSite">Manage on Mindbody Site></a>\n' +
                            '    <a class="btn btn-primary btn-xs" id="MBOLogout">Logout</a>\n' +
                            '</div>\n';

                    } else {

                        // Login failed.

                        var login_form = get_login_form({
                            classID: result.classID,
                            className: result.className,
                            staffName: result.staffName,
                            classTime: result.classTime,
                            location: location
                        });

                        class_registration_content += '<hr/>' + json.message + login_form.html();

                    }
                    // Update the modal content
                    $('#classRegister').html(class_registration_content);

                } // ./ Ajax Success
            }) // End Ajax
                .fail(function (json) {
                    $('#classRegister').html('ERROR SIGNING IN');
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
            $('#classRegister').html(spinner);
            $.ajax({
                type: "GET",
                dataType: 'json',
                url: mz_mindbody_schedule.ajaxurl,
                data: {action: 'mz_create_mbo_account', nonce: formData.nonce, classID: formData.classID, form: form.serialize()},
                success: function (json) {
                    if (json.type == "success") {
                        $('.fa-spinner').remove();
                        $('#classRegister').html(json.message);
                        // Store in DOM that we are logged in now.
                        $('[data-loggedMBO]').attr("data-loggedMBO", 1);
                    } else {
                        $('#classRegister').html('ERROR CREATING ACCOUNT');
                        console.log(json);
                    }
                } // ./ Ajax Success
            }) // End Ajax
                .fail(function (json) {
                    $('#classRegister').html('ERROR CREATING ACCOUNT');
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
            $('#classRegister').html(spinner);
            $.ajax({
                type: "GET",
                dataType: 'json',
                url: mz_mindbody_schedule.ajaxurl,
                data: {action: 'mz_generate_signup_form', nonce: nonce, classID: classID},
                success: function (json) {
                    if (json.type == "success") {
                        $('.fa-spinner').remove();
                        $('#classRegister').html(json.message);
                    } else {
                        $('#classRegister').html('ERROR GENERATING SIGN-UP FORM');
                        console.log(json);
                    }
                } // ./ Ajax Success
            }) // End Ajax
                .fail(function (json) {
                    $('#classRegister').html('ERROR GENERATING THE SIGN-UP FORM');
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
            $('#AddedToClass').html(spinner);
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
         * Logout of MBO
         *
         *
         */
        $(document).on('click', "#MBOLogout", function (ev) {
            ev.preventDefault();

            $('#classRegister').html(spinner);

            $.ajax({
                dataType: 'json',
                url: mz_mindbody_schedule.ajaxurl,
                data: {action: 'mz_client_log_out'},
                success: function(json) {
                    if (json.type == "success") {
                        $('.signupModalFooter').remove();
                        $('#classRegister').html(json.message);
                        // Store in DOM that we are logged out now.
                        $('[data-loggedMBO]').attr("data-loggedMBO", 0);
                    } else {
                        $('#classRegister').html('ERROR LOGGING OUT');
                        console.log(json);
                    }
                } // ./ Ajax Success
            }) // End Ajax
                .fail(function (json) {
                    $('#classRegister').html('ERROR LOGGING OUT');
                    console.log(json);
                }); // End Fail
        });

        /**
         * Get Login Form
         *
         * Pull the Login Form from the HTML DOM
         */
        function get_login_form(additional_data){
            Object.keys(additional_data).forEach(function(key) {
                $('#mzLogIn').append('<input type="hidden" name="' + key + '" value="' + additional_data[key] + '" />');
            });
            return $('#mzLogInContainer');
        }

        function logout(){
            $.ajax({
                dataType: 'json',
                url: mz_mindbody_schedule.ajaxurl,
                data: {action: 'mz_client_log_out'},
                success: function(json) {
                    if (json.type == "success") {
                        // Store in DOM that we are logged out now.
                        $('[data-loggedMBO]').attr("data-loggedMBO", 0);
                    } else {
                        console.log(json);
                    }
                } // ./ Ajax Success
            }) // End Ajax
                .fail(function (json) {
                    console.log(json);
                }); // End Fail
        }

        /**
         * Toggle Visibility
         * source: https://css-tricks.com/snippets/javascript/showhide-element/
         */
        function toggle_visibility(id) {
            var e = document.getElementById(id);
            if (e === null){
                return false;
            }
            if(e.style.display == 'block')
                e.style.display = 'none';
            else
                e.style.display = 'block';
        }

    }); // End document ready
})(jQuery);