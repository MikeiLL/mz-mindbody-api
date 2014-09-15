(function($){

    // kill modal contents on hide
    $('body').on('hidden.bs.modal', '#mzModal', function () {
      $(this).removeData('bs.modal');
    });

})(jQuery);
