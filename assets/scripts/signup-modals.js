(function ($) {
    $(document).ready(function ($) {

        // Initialize some variables
        var nonce = mz_mindbody_schedule.login_nonce,
            // Shortcode atts for current page.
            atts = mz_mindbody_schedule.atts,
            // Just one location for use in general MBO site link
            location = atts.locations[0].toString(),
            siteID = atts.account ? atts.account : mz_mindbody_schedule.account;

        window.addEventListener('authenticated', function (event) {
            console.log("classid", mz_mbo_state.classID);
        });
        window.addEventListener('need_to_register', function (event) {
            document.getElementById('studio_registration_form').showModal();
        });

		/**
		 * State will store and track status
		 */
        var mz_mbo_state = {

            logged_in: (mz_mindbody_schedule.loggedMBO == 1) ? true : false,
            action: undefined,
            target: undefined,
            siteID: undefined,
            nonce: undefined,
            location: undefined,
            classID: undefined,
            className: undefined,
            staffName: undefined,
            classTime: undefined,
            class_title: undefined,
            content: undefined,
            spinner: '<div class="d-flex justify-content-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div>',
            wrapper: undefined,
            content_wrapper: '<div class="modal__content" id="signupModalContent"></div>',
            footer: '<div class="modal__footer" id="signupModalFooter">\n' +
            '    <a class="btn btn-primary" data-nonce="'+mz_mindbody_schedule.signup_nonce+'" id="MBOSchedule" target="_blank">My Classes</a>\n' +
            '    <a href="https://clients.mindbodyonline.com/ws.asp?&amp;sLoc='+mz_mindbody_schedule.location+'&studioid='+mz_mindbody_schedule.siteID+'>" class="btn btn-primary btn-xs" id="MBOSite">Manage on Mindbody Site></a>\n' +
            '    <a class="btn btn-primary btn-xs" id="MBOLogout">Logout</a>\n' +
            '</div>\n',
            header: undefined,
            signup_button: undefined,
            message: undefined,
            client_first_name: undefined,

            login_form: $('#mzLogInContainer').html(),

            initialize: function(target){
                this.target = $(target).attr("href");
                this.siteID = $(target).attr('data-siteID');
                this.nonce = $(target).attr("data-nonce");
                this.location = $(target).attr("data-location");
                this.classID = $(target).attr("data-classID");
                this.className = $(target).attr("data-className");
                this.staffName = $(target).attr("data-staffName");
                this.classTime = $(target).attr("data-time");
                this.class_title = '<h2>' + this.className + ' ' + mz_mindbody_schedule.with + ' ' + this.staffName + '</h2><h3>' + this.classTime + '</h3><hr/>';
                this.header = '<div class="modal__header" id="modalHeader"><h1>'+mz_mindbody_schedule.signup_heading+'</h1>'+this.class_title+'</div>';
                this.signup_button = '<button class="btn btn-primary" data-nonce="'+this.nonce+'" data-location="'+this.location+'" data-classID="'+this.classID+'" id="signUpForClass">' + mz_mindbody_schedule.confirm_signup + '</button>';
            }
        };

		/*
		 * Define the modal container state which changes depending on login state
		 */
        function render_mbo_modal(){
            var message = (mz_mbo_state.message ? '<p>'+mz_mbo_state.message+'</p>' : '');
            mz_mbo_state.wrapper = '<div class="modal__wrapper" id="signupModalWrapper">';
            if (mz_mbo_state.logged_in){
                mz_mbo_state.wrapper += mz_mbo_state.header;
                mz_mbo_state.wrapper += '<div class="modal__content" id="signupModalContent">'+message+mz_mbo_state.signup_button+'</div>';
                mz_mbo_state.wrapper += mz_mbo_state.footer;
            } else {
                mz_mbo_state.wrapper += mz_mbo_state.header;
                mz_mbo_state.wrapper += '<div class="modal__content" id="signupModalContent">'+message+mz_mbo_state.login_form+'</div>';
            }
            mz_mbo_state.wrapper += '</div>';
            if ($('#cboxLoadedContent')) {
                $('#cboxLoadedContent').html(mz_mbo_state.wrapper);
            }
            mz_mbo_state.message = undefined;
        }

		/*
		 * Render inner content of modal based on state
		 */
        function render_mbo_modal_activity(){
            // Clear content and content wrapper
            mz_mbo_state.content = '';
            $('#signupModalContent').html = '';
            if (mz_mbo_state.action == 'processing'){
                mz_mbo_state.content += mz_mbo_state.spinner;
            } else if (mz_mbo_state.action == 'login_failed') {
                mz_mbo_state.content += mz_mbo_state.message;
                mz_mbo_state.content += mz_mbo_state.login_form;
            } else if (mz_mbo_state.action == 'logout') {
                mz_mbo_state.content += mz_mbo_state.message;
                mz_mbo_state.content += mz_mbo_state.login_form;
                $('#signupModalFooter').remove();
            } else if (mz_mbo_state.action == 'error') {
                mz_mbo_state.content += mz_mbo_state.message;
            } else {
                // login, sign_up_form
                mz_mbo_state.content += mz_mbo_state.message;
            }
            if ($('#signupModalContent')) {
                $('#signupModalContent').html(mz_mbo_state.content);
            }
            mz_mbo_state.message = undefined;
        }

        /**
         * Continually Check if Client is Logged in and Update Status
         */
        // TODO reinstate: setInterval(mz_mbo_check_client_logged, 5000);

        function mz_mbo_check_client_logged( )
        {
            //this will repeat every 5 seconds
            $.ajax({
                dataType: 'json',
                url: mz_mindbody_schedule.ajaxurl,
                data: {action: 'mz_check_client_logged', nonce: 'mz_check_client_logged'},
                success: function(json) {
                    if (json.type == "success") {
                        mz_mbo_state.logged_in = (json.message == 1 ? true : false);
                    }
                } // ./ Ajax Success
            }); // End Ajax
        }

        /**
         * Initial Modal Window to Register for a Class
         *
         * Also leads to options to login and sign-up with MBO
         *
         */
        $(document).on('click', "a[data-target=mzSignUpModal]", function (ev) {
            ev.preventDefault();
            window.open(mz_mindbody_schedule.mbo_oauth_url, '_blank', 'location=yes,height=570,width=520,scrollbars=yes,status=yes');
            mz_mbo_state.classID = ev.target.dataset['classid'];
            /* mz_mbo_state.initialize(this);
            if (mz_mbo_state.logged_in) {
                render_mbo_modal();
            } else {
                // open window with Oauth login
                // make local ajax call to get details
                $.ajax({
                    dataType: 'json',
                    url: mz_mindbody_schedule.ajaxurl,
                    data: {
                        action: 'mz_client_log_in',
                        nonce: 'mz_signup_nonce',
                        },
                    success: function(json) {
                        console.log({"success": json});
                    } // ./ Ajax Success
                }) // End Ajax
                    .fail(function (json) {
                        console.log(json);
                    }); // End Fail
            }
            $("#mzSignUpModal").load(mz_mbo_state.target, function () {
                $.colorbox({html: mz_mbo_state.wrapper,  href: mz_mbo_state.target});
                $("#mzSignUpModal").colorbox();
            }); */

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



        });

        /**
         * Logout of MBO
         *
         *
         */
        $(document).on('click', "#MBOLogout", function (ev) {
            ev.preventDefault();
            var nonce = $(this).attr("data-nonce");

            $.ajax({
                dataType: 'json',
                url: mz_mindbody_schedule.ajaxurl,
                data: {action: 'mz_client_logout', nonce: nonce},
                beforeSend: function() {
                    mz_mbo_state.action = 'processing';
                    render_mbo_modal_activity();
                },
                success: function(json) {
                    if (json.type == "success") {
                        mz_mbo_state.logged_in = false;
                        mz_mbo_state.action = 'logout';
                        mz_mbo_state.message = json.message;
                        render_mbo_modal_activity();
                    } else {
                        mz_mbo_state.action = 'logout_failed';
                        mz_mbo_state.message = json.message;
                        render_mbo_modal_activity();
                    }
                } // ./ Ajax Success
            }) // End Ajax
                .fail(function (json) {
                    mz_mbo_state.message = 'ERROR LOGGING OUT';
                    render_mbo_modal_activity();
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
            $.ajax({
                type: "GET",
                dataType: 'json',
                url: mz_mindbody_schedule.ajaxurl,
                data: {action: 'mz_generate_signup_form', nonce: nonce, classID: classID},
                beforeSend: function() {
                    mz_mbo_state.action = 'processing';
                    render_mbo_modal_activity();
                },
                success: function(json) {
                    if (json.type == "success") {
                        mz_mbo_state.logged_in = true;
                        mz_mbo_state.action = 'sign_up_form';
                        mz_mbo_state.message = json.message;
                        render_mbo_modal_activity();
                    } else {
                        mz_mbo_state.action = 'error';
                        mz_mbo_state.message = json.message;
                        render_mbo_modal_activity();
                    }
                } // ./ Ajax Success
            }) // End Ajax
                .fail(function (json) {
                    mz_mbo_state.message = 'ERROR GENERATING THE SIGN-UP FORM';
                    render_mbo_modal_activity();
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
            $.ajax({
                type: "GET",
                dataType: 'json',
                url: mz_mindbody_schedule.ajaxurl,
                data: {action: 'mz_create_mbo_account', nonce: formData.nonce, classID: formData.classID, form: form.serialize()},
                beforeSend: function() {
                    mz_mbo_state.action = 'processing';
                    render_mbo_modal_activity();
                },
                success: function (json) {
                    if (json.type == "success") {
                        mz_mbo_state.logged_in = true;
                        mz_mbo_state.action = 'login';
                        mz_mbo_state.message = json.message;
                        render_mbo_modal_activity();
                    } else {
                        mz_mbo_state.action = 'error';
                        mz_mbo_state.message = json.message;
                        render_mbo_modal_activity();
                    }
                } // ./ Ajax Success
            }) // End Ajax
                .fail(function (json) {
                    mz_mbo_state.message = 'ERROR CREATING ACCOUNT';
                    render_mbo_modal_activity();
                    console.log(json);
                }); // End Fail

        });

        /**
         * Register for a class
         */
        $(document).on('click', '#signUpForClass', function (ev) {
            ev.preventDefault();

            var nonce = $(this).attr("data-nonce");

            $.ajax({
                type: "GET",
                dataType: 'json',
                url: mz_mindbody_schedule.ajaxurl,
                context: this,
                data: {
                    action: 'mz_register_for_class',
                    nonce: nonce,
                    siteID: mz_mbo_state.siteID,
                    classID: mz_mbo_state.classID,
                    location: mz_mbo_state.location
                },
                beforeSend: function() {
                    mz_mbo_state.action = 'processing';
                    render_mbo_modal_activity();
                },
                success: function (json) {
                    if (json.type == "success") {
                        mz_mbo_state.action = 'register';
                        mz_mbo_state.message = json.message;
                        render_mbo_modal_activity();
                    } else {
                        mz_mbo_state.action = 'error';
                        mz_mbo_state.message = 'ERROR REGISTERING FOR CLASS. ' + json.message;
                        render_mbo_modal_activity();
                    }
                } // ./ Ajax Success
            }) // End Ajax
                .fail(function (json) {
                    mz_mbo_state.message = 'ERROR REGISTERING FOR CLASS';
                    render_mbo_modal_activity();
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
            $.ajax({
                type: "GET",
                dataType: 'json',
                url: mz_mindbody_schedule.ajaxurl,
                data: {action: 'mz_display_client_schedule', nonce: 'mz_display_client_schedule', location: mz_mbo_state.location, siteID: mz_mbo_state.siteID},
                beforeSend: function() {
                    mz_mbo_state.action = 'processing';
                    render_mbo_modal_activity();
                },
                success: function (json) {
                    if (json.type == "success") {
                        mz_mbo_state.action = 'display_schedule';
                        mz_mbo_state.message = json.message;
                        render_mbo_modal_activity();
                    } else {
                        mz_mbo_state.action = 'error';
                        mz_mbo_state.message = 'ERROR RETRIEVING YOUR SCHEDULE. ' + json.message;
                        render_mbo_modal_activity();
                    }
                } // ./ Ajax Success
            }) // End Ajax
                .fail(function (json) {
                    mz_mbo_state.message = 'ERROR RETRIEVING YOUR SCHEDULE';
                    render_mbo_modal_activity();
                    console.log(json);
                }); // End Fail

        });

    }); // End document ready
})(jQuery);
