(function( $ ) {
	'use strict';
	$(document).ready(function($) {
	alert("hello");

	// Initialize some variables
	var nonce = mz_mindbody_schedule.nonce,
	atts = mz_mindbody_schedule.atts,
	container = $('#mzClearTransients');

	/*
	* Clear Transients
	*
	*
	*/
	$('#mzClearTransients').on('click',function(e) {
		e.preventDefault();
		alert("hi");
		
		}); // End click
	}); // End document ready
})( jQuery );
