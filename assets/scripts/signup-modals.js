(function ($) {
    $(document).ready(function ($) {

        // Initialize some variables
        var nonce = mz_mindbody_schedule.signup_nonce,
            // Shortcode atts for current page.
            atts = mz_mindbody_schedule.atts,
            // Just one location for use in general MBO site link
            location = atts.locations[0].toString(),
            siteID = atts.account ? atts.account : mz_mindbody_schedule.account;

        var mz_mbo_state = {

            logged_in: (mz_mindbody_schedule.loggedMBO == '1') ? true : false,
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
            spinner: '<i class="fa fa-spinner fa-3x fa-spin" style="position: fixed; top: 50%; left: 50%;"></i>',
            wrapper: undefined,
            content_wrapper: '<div class="modal__content btn-group" class="signupModalContent"></div>',
            footer: '<div class="modal__footer btn-group" class="signupModalFooter">\n' +
            '    <a class="btn btn-primary" data-nonce="'+mz_mindbody_schedule.signup_nonce+'" id="MBOSchedule" target="_blank">My Classes</a>\n' +
            '    <a href="https://clients.mindbodyonline.com/ws.asp?&amp;sLoc='+mz_mindbody_schedule.location+'&studioid='+mz_mindbody_schedule.siteID+'>" class="btn btn-primary btn-xs" id="MBOSite">Manage on Mindbody Site></a>\n' +
            '    <a class="btn btn-primary btn-xs" id="MBOLogout">Logout</a>\n' +
            '</div>\n',
            header: '<div class="modal__header" id="modalHeader"><h1>'+mz_mindbody_schedule.your_account+'</h1></div>',
            signup_button: undefined,

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
                this.class_title = this.classTime + ' ' + this.className + ' ' + mz_mindbody_schedule.staff_preposition + ' ' + this.staffName + '<hr/>';
                this.signup_button = '<button class="btn btn-primary" data-nonce="'+this.nonce+'" data-location="'+this.location+'" data-classID="'+this.classID+'" id="signUpForClass">' + mz_mindbody_schedule.confirm_signup + '</button>';
            }
        };

        function render_mbo_modal(){
            mz_mbo_state.wrapper = '';
            if (mz_mbo_state.logged_in){
                mz_mbo_state.wrapper += mz_mbo_state.header;
                mz_mbo_state.wrapper += mz_mbo_state.class_title;
                mz_mbo_state.wrapper += mz_mbo_state.login_form;
                mz_mbo_state.wrapper += '<div class="modal__content" id="signupModalContent">'+mz_mbo_state.signup_button+'</div>';
            } else {
                mz_mbo_state.wrapper += mz_mbo_state.header;
                mz_mbo_state.wrapper += mz_mbo_state.class_title;
                mz_mbo_state.wrapper += '<div class="modal__content" id="signupModalContent">'+mz_mbo_state.login_form+'</div>';
            }
            if ($('#classRegister')) {
                $('#classRegister').html(mz_mbo_state.wrapper);
            }
        }

        function render_mbo_modal_activity($message){
            // Clear content and content wrapper
            mz_mbo_state.content = '';
            $('#signupModalContent').html = '';
            if (mz_mbo_state.action == 'processing'){
                mz_mbo_state.content += mz_mbo_state.spinner;
                mz_mbo_state.content += "peace me";
            } else if (mz_mbo_state.action == 'login') {
                mz_mbo_state.content += $message;
            } else if (mz_mbo_state.action == 'login_failed') {
                mz_mbo_state.content += $message;
                mz_mbo_state.wrapper += mz_mbo_state.login_form;
            }
            if ($('#signupModalContent')) {
                $('#signupModalContent').html(mz_mbo_state.content);
            }
        }

        /**
         * Initial Modal Window to Register for a Class
         *
         * Also leads to options to login and sign-up with MBO
         *
         */
        $(document).on('click', "a[data-target=mzSignUpModal]", function (ev) {
            ev.preventDefault();

            mz_mbo_state.initialize(this);

            render_mbo_modal();

            $("#mzSignUpModal").load(mz_mbo_state.target, function () {
                $.colorbox({html: mz_mbo_state.wrapper,  href: mz_mbo_state.target});
                $("#mzSignUpModal").colorbox();
            });

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

                beforeSend: function() {
                    // setting a timeout
                    console.log('before send');
                    mz_mbo_state.action = 'processing';
                    render_mbo_modal_activity();
                },
                success: function(json) {
                    var formData = $(this).serializeArray();
                    var result = { };
                    $.each($('form').serializeArray(), function() {
                        result[this.name] = this.value;
                    });

                    var class_registration_content = result.classTime + ' ' + result.className + ' ' + mz_mindbody_schedule.staff_preposition + ' ' + result.staffName;

                    if (json.type == "success") {

                        mz_mbo_state.logged_in = true;

                        mz_mbo_state.action = 'login';

                        // Update the modal content

                        render_mbo_modal_activity(json.message);

                    } else {

                        mz_mbo_state.action = 'login_failed';

                        render_mbo_modal_activity(json.message);

                    }

                } // ./ Ajax Success
            }) // End Ajax
                .fail(function (json) {
                    $('#classRegister').html('ERROR SIGNING IN');
                    console.log(json);
                }); // End Fail

        });

    }); // End document ready
})(jQuery);