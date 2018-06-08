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
                        alert('Transients cleared.');
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
         * Reset Class Owners
         *
         * Call the mz_deduce_class_owners method of the Retrieve_Class_Owners class
         * via Ajax.
         *
         * We log the matrix into the browser console.
         *
         */
        function init_class_owners_reset() {

            $("a.class_owners").on('click', function (ev) {

                $.ajax({
                    type: "post",
                    dataType: "json",
                    context: this,
                    url: mz_mindbody_schedule.ajaxurl,
                    data: {action: 'mz_deduce_class_owners', nonce: nonce},
                    success: function (json) {
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
                        console.log('fail');
                        console.log(json);
                        alert('Something went wrong.');
                    });

                //var t = this.title || this.innerHTML || this.href;

                //tb_show(t, 'admin-ajax.php?action=mz_deduce_class_owners');

                //this.blur();

                return false;

            });
        }
        init_class_owners_reset();

    }); // End document ready
})(jQuery);
