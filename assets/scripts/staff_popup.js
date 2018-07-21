(function ($) {
    $(document).ready(function($) {
        $("a[data-target=#mzStaffModal]").click(function(ev) {
            ev.preventDefault();
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