(function ($) {
    'use strict';
    $(document).ready(function ($) {

        // Initialize some variables
        var nonce = mz_mindbody_schedule.nonce,
            atts = mz_mindbody_schedule.atts;

        /**
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
                        alert(json.message);
                    } else {
                        alert('Something went wrong.');
                    }
                }
            }) // End ajax
                .fail(function (json) {
                    console.log('fail');
                    console.log(json);
                    alert('Something went wrong.');
                });
        }); // End Clear Transients

        /**
         * Update Site Token
         *
         *
         */
        $('#mzUpdateSiteToken').on('click', function (e) {
            e.preventDefault();
            $.ajax({
                type: "post",
                dataType: "json",
                context: this,
                url: mz_mindbody_schedule.ajaxurl,
                data: {action: 'mz_mbo_get_and_save_token', nonce: nonce},
                success: function (json) {
                    if (json.type == "success") {
                        alert(" New token retrieved and saved: " + json.message);
                    } else {
                        alert('Something went wrong.');
                    }
                }
            }) // End ajax
                .fail(function (json) {
                    console.log('fail');
                    console.log(json);
                    alert('Something went wrong.');
                });
        }); // End Clear Transients

        /**
         * Test Credentials
         *
         *
         */
        $('#mzTestCredentials').on('click', function (e) {
            e.preventDefault();
            var self = $(this);
            self.addClass('disabled');
            self.after('<img id="class_owners_spinner" src="' + mz_mindbody_schedule.spinner + '"/>');
            $.ajax({
                type: "post",
                dataType: "json",
                context: this,
                url: mz_mindbody_schedule.ajaxurl,
                data: {action: 'mz_mbo_test_credentials', nonce: nonce},
                success: function (json) {
                    if (json.type == "success") {
                        self.removeClass('disabled');
                        $('#class_owners_spinner').remove();
                        $('#displayTest').html(json.message);
                    } else {
                        self.removeClass('disabled');
                        $('#class_owners_spinner').remove();
                        alert('Something went wrong.');
                    }
                }
            }) // End ajax
                .fail(function (json) {
                    self.removeClass('disabled');
                    $('#class_owners_spinner').remove();
                    console.log('fail');
                    console.log(json);
                    alert('Something went wrong.');
                });
        }); // End Clear Transients

        /**
         * Test Credentials
         *
         *
         */
        $('#mzTestCredentialsV5').on('click', function (e) {
            e.preventDefault();
            var self = $(this);
            self.addClass('disabled');
            self.after('<img id="class_owners_spinner" src="' + mz_mindbody_schedule.spinner + '"/>');
            $.ajax({
                type: "post",
                dataType: "json",
                context: this,
                url: mz_mindbody_schedule.ajaxurl,
                data: {action: 'mz_mbo_test_credentials_v5', nonce: nonce},
                success: function (json) {
                    if (json.type == "success") {
                        self.removeClass('disabled');
                        $('#class_owners_spinner').remove();
                        $('#displayTestV5').html(json.message);
                    } else {
                        self.removeClass('disabled');
                        $('#class_owners_spinner').remove();
                        alert('Something went wrong.');
                    }
                }
            }) // End ajax
                .fail(function (json) {
                    self.removeClass('disabled');
                    $('#class_owners_spinner').remove();
                    console.log('fail');
                    console.log(json);
                    alert('Something went wrong.');
                });
        }); // End Clear Transients

        /**
         * Reset Class Owners
         *
         * Call the mz_deduce_class_owners method of the Retrieve_Class_Owners class
         * via Ajax.
         *
         * This function is used by the Admin Options Advanced section
         * to call the php function that resets the transient holding the
         * array of probable "owners" of various classes, used to display
         * who a substitute is substituting for.
         *
         * We log the matrix into the browser console.
         *
         */

        $("a.class_owners").on('click', function (ev) {

            var self = $(this);

            self.addClass('disabled');
            self.after('<img id="class_owners_spinner" src="' + mz_mindbody_schedule.spinner + '"/>');

            $.ajax({
                type: "post",
                dataType: "json",
                context: this,
                url: mz_mindbody_schedule.ajaxurl,
                data: {action: 'mz_deduce_class_owners', nonce: nonce},
                success: function (json) {
                    self.removeClass('disabled');
                    $('#class_owners_spinner').remove();
                    if (json.type == "success") {
                        console.log(json.message);
                        alert('Class Owners Matrix Reset');
                    } else {
                        console.log(json);
                        alert('Something went wrong.');
                    }
                }
            }) // End ajax
                .fail(function (json) {
                    self.removeClass('disabled');
                    $('#class_owners_spinner').remove();
                    console.log('fail');
                    console.log(json);
                    alert('Something went wrong.');
                });

            return false;

        });

    }); // End document ready
})(jQuery);
