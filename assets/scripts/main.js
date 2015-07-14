(function($){
$(document).ready(function($) {
		$("a[data-target=#mzModal]").click(function(ev) {
			ev.preventDefault();
			var target = $(this).attr("href");
			// load the url and show modal on success
			$("#mzModal").load(target, function() { 
				 $("#mzModal").modal({show:true});  
			});
			// kill modal contents on hide
			    $('body').on('hidden.bs.modal', '#mzModal', function () {
			     $(this).removeData('bs.modal');
			   });	
		});
	});
	
$(document).ready(function($) {
		$("a[data-target=#mzModal]").click(function(ev) {
			ev.preventDefault();
			var target = $(this).attr("href");
			// load the url and show modal on success
			$("#mzModal").load(target, function() { 
				 $("#mzModal").modal({show:true});  
			});
			// kill modal contents on hide
			    $('body').on('hidden.bs.modal', '#mzModal', function () {
			     $(this).removeData('bs.modal');
			   });	
		});
	});
	
$(document).ready(function($) {
	var mZ_row_count = 1;
		$('.mz_add_to_class').click(function(){
				var countSpan = $(this).closest(".mz_add_to_class").find(".count");
				var countVal = countSpan.text();
				var nuVal = parseInt(countVal) + mZ_row_count;
				mZ_row_count++;
				countSpan.text(nuVal);
				nonce = $(this).attr("data-nonce");
				clientID = $(this).attr("data-clientID");
				classID = $(this).attr("data-classID");
				$(this).closest(".mz_add_to_class").removeClass('mz_add_to_class');
				$(this).closest("#mz_add_to_class").addClass('mz_add_to_class'+nuVal);
				$(this).closest(".mz_description_holder").find('.visitMBO').removeClass('visitMBO');
				$(this).closest(".mz_description_holder").find("#visitMBO").addClass('visitMBO'+nuVal);
				$('.mz_add_to_class'+nuVal).find(".signup").text("MindBodyOnline...");
				    $.ajax({
					 type : "post",
					 dataType : "json",
					 url : mZ_add_to_classes.ajaxurl,
					 data : {action: 'mz_mbo_add_client_ajax', nonce: nonce, clientID: clientID, classID: classID},
					 success: function(json) {
						if(json.type == "success") {
						   $(".mz_add_to_class"+nuVal).find(".signup").text(json.message);
						   $(".mz_description_holder").find(".visitMBO"+nuVal).removeAttr("style");
						}
						else {
						   $(".mz_add_to_class"+nuVal).find(".signup").text(json.message);
						   $(".mz_description_holder").find(".visitMBO"+nuVal).removeAttr("style");
						}
					 }
				  });  
		    });
		});
$(document).ready(function() {
	var stripeTable = function(table) { //stripe the table (jQuery selector)
            table.find('tr').removeClass('striped').filter(':visible:even').addClass('striped');
        };
        $('table.mz-schedule-filter').filterTable({
            callback: function(term, table) { stripeTable(table); }, //call the striping after every change to the filter term
            placeholder: mz_mindbody_api_i18n.filter_default,
            highlightClass: 'alt',
            inputType: 'search',
            label: mz_mindbody_api_i18n.label,
            quickListClass: 'mz_quick_filter',
            quickList: [mz_mindbody_api_i18n.quick_1, mz_mindbody_api_i18n.quick_2, mz_mindbody_api_i18n.quick_3]
        });
        stripeTable($('table.mz-schedule-filter')); //stripe the table for the first time
	});
})(jQuery);

