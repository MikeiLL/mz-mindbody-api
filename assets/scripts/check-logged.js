(function ($) {
    $(document).ready(function ($) {
        checkVariableValue();

        function checkVariableValue() {
            $.ajax({
                type: "post",
                dataType: "json",
                url: mZ_check_session_logged.ajaxurl,
                data: {action: 'mz_mbo_check_session_logged'},
                success: function (json) {
                    if (json.logged_in == '1') {
                        $('.signup').text(mZ_check_session_logged.signup);
                        $('.mz_add_to_class').prop('title', mZ_check_session_logged.signup);
                        $('.mz_add_to_class').prop('id', 'mz_add_to_class');
                        $('.mz_add_to_class').removeAttr("href");
                    } else {
                        $('.signup').text(mZ_check_session_logged.login);
                        $('.mz_add_to_class').prop('title', mZ_check_session_logged.login_title);
                    }
                } // End Ajax success
            }); // End Ajax
        } // End checkVariableValue
    });
})(jQuery);