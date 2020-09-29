(function ($) {
    $(document).ready(function($) {

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

        $(document).on('click', "a[data-target=mzModal]", function(e) {
            e.preventDefault();
            var target = $(this).attr("href");
            var staffBio = decodeURIComponent($(this).attr('data-staffBio'));
            var staffName = $(this).attr('data-staffName');
            var siteID = $(this).attr('data-siteID');
            var staffID = $(this).attr('data-staffID');
            var mbo_url_parts = ['http://clients.mindbodyonline.com/ws.asp?studioid=',
                '&stype=-7&sView=week&sTrn='];
            var staffImage = decodeURIComponent($(this).attr('data-staffImage'));
            var popUpContent = '<div class="mz_staffName"><h3>' + staffName + '</h3>';

            popUpContent += '<img class="mz-staffImage" src="' + staffImage + '" />';
            popUpContent += '<div class="mz_staffBio">'  +  staffBio + '</div></div>';
            popUpContent += '<br/><a href="' + mbo_url_parts[0] + siteID + mbo_url_parts[1] + staffID + '" ';
            popUpContent += 'class="btn btn-info mz-btn-info mz-bio-button" target="_blank">See ' + staffName +'&apos;s Schedule</a>';
            // load the url and show modal on success
            $("#mzStaffModal").load(target, function() {
                $.colorbox({html: popUpContent, width:"75%"});
                $("#mzStaffModal").colorbox();
            });
        }); // End click
    }); // End document ready
})(jQuery);