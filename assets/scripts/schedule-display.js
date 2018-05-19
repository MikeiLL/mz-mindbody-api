(function($) {
  $(document).ready(function($) {
  
  	// Initialize some variables
    var nonce = mz_mindbody_schedule.nonce,
    atts = mz_mindbody_schedule.atts,
    container = $('#mzScheduleDisplay');

      /*
       * Navigate Schedule
       *
       *
       */
    $('#mzScheduleNavHolder .following, #mzScheduleNavHolder .previous').on('click',function(e) {
        e.preventDefault();
        container.html("");
        container.toggleClass('loader');
        var buttons = [].slice.call(document.getElementById('mzScheduleNavHolder').children);
        // Update attributes
        var offset = atts.offset = this.dataset.offset;
        // Update nav link "offset" data attribute
        if (this.className == 'following') {
            buttons.forEach( function(button) {
                button.setAttribute('data-offset', parseInt(button.getAttribute('data-offset')) + parseInt(1));
            });
        } else if (this.className == 'previous') {
            buttons.forEach( function(button) {
                button.setAttribute('data-offset', button.getAttribute('data-offset') - 1);
            });
        }
        $.ajax({
        type : "post",
        dataType : "json",
        context : this,
        url : mz_mindbody_schedule.ajaxurl,
        data : {action: 'mz_display_schedule', nonce: nonce, atts: atts},
        success: function(json) {
                if(json.type == "success") {
                    container.toggleClass('loader');
                    container.html(json.message);
                } else {
                    reset_navigation(this);
                    container.toggleClass('loader');
                    container.html(json.message);
                }
            }
        })
        .fail( function( json ) {
            reset_navigation(this);
            console.log('fail');
            console.log(json);
            container.toggleClass('loader');
            container.html('Sorry but there was an error retrieving schedule.');
        }); // End Ajax
    }); // End click navigation
    
      function reset_navigation(el){
          // Reset nav link "offset" data attribute
          if (el.className == 'previous') {
              buttons.forEach( function(button) {
                  button.setAttribute('data-offset', parseInt(button.getAttribute('data-offset')) + parseInt(1));
              });
          } else if (el.className == 'following') {
              buttons.forEach( function(button) {
                  button.setAttribute('data-offset', button.getAttribute('data-offset') - 1);
              });
          }
      }
      
      

      /*
       * Class Description Modal
       *
       *
       */
      $(document).on('click', "a[data-target=mzModal]", function(ev) {
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
			
			/*
			 * Filter Table Init
			 *
			 */
			var stripeTable = function(table) { //stripe the table (jQuery selector)
						table.find('tr').removeClass('striped').filter(':visible:even').addClass('striped');
					};

			$('table.mz-schedule-filter').filterTable({
				callback: function(term, table) { stripeTable(table); }, //call the striping after every change to the filter term
				placeholder: mz_mindbody_schedule.filter_default,
				highlightClass: 'alt',
				inputType: 'search',
				label: mz_mindbody_schedule.label,
				selector: mz_mindbody_schedule.selector,
				quickListClass: 'mz_quick_filter',
				quickList: [mz_mindbody_schedule.quick_1, mz_mindbody_schedule.quick_2, mz_mindbody_schedule.quick_3],
				locations: mz_mindbody_schedule.Locations_dict
			});
			stripeTable($('table.mz-schedule-filter')); //stripe the table for the first time

      /**
       * Show Registrants
       *
       *
       */
      // $("a[data-target=registrantModal]").click(function(ev) {
      //     ev.preventDefault();
      //     var target = $(this).attr("href");
      //     var classDescription = $(this).attr('data-classDescription');
      //     var staffName = $(this).attr('data-staffName');
      //     var staffImage = $(this).attr('data-staffImage');
      //     var className = $(this).attr("data-className");
      //     var classID = $(this).attr("data-classID");
      //     var nonce = $(this).attr("data-nonce");
      //     var popUpContent = '<div class="mz-classInfo">';
      //     popUpContent += '<h3>' + className + '</h3>';
      //     popUpContent += '<h4>' + mZ_get_registrants.staff_preposition + ' ' + staffName + '</h4>';
// 
      //     if (typeof staffImage != 'undefined') {
      //         popUpContent += '<img class="mz-staffImage" src="' + staffImage + '" />';
      //     }
// 
      //     var htmlClassDescription = '<div class="mz_modalClassDescription">';
      //     htmlClassDescription += "<div class='class-description'>"  +  decodeURIComponent(classDescription) + "</div></div>";
      //     popUpContent += htmlClassDescription;
      //     popUpContent += '</div>';
// 
      //     popUpContent += '<h3>' + mZ_get_registrants.registrants_header + '</h3>';
      //     popUpContent += '<div id="modalRegistrants"><div id="ClassRegistrants" style="min-height:90px;">';
      //     popUpContent += '<i class="fa fa-spinner fa-3x fa-spin"></i></div></div>';
      //     $("#registrantModal").load(target, function() {
      //         $.colorbox({html: popUpContent, width:"75%", height:"80%", href:"inc/modal_descriptions.php"});
      //         $("#registrantModal").colorbox();
      //     });
      //     $.ajax({
      //         type: "GET",
      //         dataType: 'json',
      //         url : mZ_get_registrants.ajaxurl,
      //         data : {action: 'mz_mbo_get_registrants', nonce: nonce, classID: classID},
      //         success: function(json) {
      //             if(json.type == "success") {
      //                 htmlRegistrants = '<ul class="mz-classRegistrants">';
      //                 if ( $.isArray(json.message)  ) {
      //                     json.message.forEach( function(name) {
      //                         htmlRegistrants += '<li>' + name.replace('_', ' ') + '</li>';
      //                     });
      //                 } else {
      //                     htmlRegistrants += '<li>' + json.message + '</li>';
      //                 }
      //                 htmlRegistrants += '</ul>';
      //                 $('#modalRegistrants').find('#ClassRegistrants')[0].innerHTML = htmlRegistrants;
      //             }else{
      //                 $('#modalRegistrants').find('#class-description-modal-body')[0].innerHTML = mZ_get_registrants.get_registrants_error;
      //             }
      //         } // ./ Ajax Success
      //     }); // ./Ajax
      // }); // End click
      
    	/**
       * Staff Modal
       *
       *
       */
      $("a[data-target=mzStaffModal]").click(function(ev) {
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
		});
			
		  /**
       * Mode Select
       *
       *
       */
		if (mz_mindbody_schedule.mode_select !== '0') {
			if (mz_mindbody_schedule.mode_select == 1) {
				$('.filter-table').last().addClass('mz_hidden');
			} else { // Then assume it's 2
				$('.filter-table').first().addClass('mz_hidden');
				}
			$('.mz_schedule_nav_holder').first().append($('<a id="mode-select" class="btn btn-xs mz-mode-select">'+ mz_mindbody_schedule.initial +'</a>'));
			$('#mode-select').click(function(){
				$('.mz-schedule-display').each(function(i, item) {
					$(item).toggleClass('mz_hidden');
					$(item).toggleClass('mz_schedule_filter');
					});
				$('.mz_grid_date').toggleClass('mz_hidden');
				$('.filter-table').toggleClass('mz_hidden');
				$('#mode-select').text(function(i, text) {
					return text == mz_mindbody_schedule.initial ? mz_mindbody_schedule.swap : mz_mindbody_schedule.initial;
					});
			});
		} // if mode button = 1 
      
  }); // End document ready
})( jQuery );