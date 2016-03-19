(function($) {
	$(document).ready(function($) {
    setTimeout(checkVariableValue, 500);
    function checkVariableValue() {
			 $.ajax({
				type : "post",
				dataType : "json",
				url: mZ_check_session_logged.ajaxurl,
				data : {action: 'mz_mbo_check_session_logged'},
				success: function(json) {
					if (json.logged_in == '1') {
						$('.signup').text(mZ_check_session_logged.signup);
						$('.mz_add_to_class').prop('title', mZ_check_session_logged.signup);
						$('.mz_add_to_class').prop('id', 'mz_login');
						$('.mz_add_to_class').removeAttr("href");
					} else {
						$('.signup').text(mZ_check_session_logged.login);
						$('.mz_add_to_class').prop('title', mZ_check_session_logged.login);
					}
				} // End Ajax success
      }); // End Ajax
    } // End checkVariableValue
	});
})( jQuery );

/*

<a id="mz_add_to_class" class="mz_add_to_class btn" data-staffname="Staff" data-clientid="100015631" data-classid="19417" data-classname="Yoga" data-nonce="6a110c776c" target="_parent" title="Sign-up" link="">

*/

/* <a id="mz_login" class="mz_add_to_class btn" data-staffname="Linda Murray" data-clientid="" data-classid="22164" data-classname="Conscious Hot Yoga Flow" data-nonce="76593fe928" target="_parent" title="Sign-up" href="http://www.uruyoga.com/login"> */