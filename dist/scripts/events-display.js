/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "./scripts/events-display.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./scripts/events-display.js":
/*!***********************************!*\
  !*** ./scripts/events-display.js ***!
  \***********************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


(function ($) {
  $(document).ready(function ($) {
    // Initialize some variables
    var spinner = '<i class="fa fa-spinner fa-3x fa-spin" style="position: fixed; top: 50%; left: 50%;"></i>',
        container = $("#mzEventsDisplay"),
        atts = mz_mindbody_schedule.atts; // TODO use Ajax event handlers to globally handle loader spinners: https://stackoverflow.com/a/40513161/2223106

    /**
     * Navigate Schedule
     *
     *
     */

    $('#mzEventsNavHolder .following, #mzEventsNavHolder .previous').on('click', function (e) {
      e.preventDefault();
      container.children().each(function (e) {
        $(this).html('');
      });
      container.toggleClass('loader');
      var buttons = [].slice.call(document.getElementById('mzEventsNavHolder').children); // Update attributes

      var offset = atts.offset = this.dataset.offset; // Update nav link "offset" data attribute

      if (this.className == 'following') {
        buttons.forEach(function (button) {
          button.setAttribute('data-offset', parseInt(button.getAttribute('data-offset')) + parseInt(1));
        });
      } else if (this.className == 'previous') {
        buttons.forEach(function (button) {
          button.setAttribute('data-offset', button.getAttribute('data-offset') - 1);
        });
      }

      $.ajax({
        type: "post",
        dataType: "json",
        context: this,
        url: mz_mindbody_schedule.ajaxurl,
        data: {
          action: 'mz_display_events',
          nonce: mz_mindbody_schedule.nonce,
          atts: atts
        },
        success: function success(json) {
          if (json.type == "success") {
            container.toggleClass('loader');
            document.getElementById("mzEventsDisplay").innerHTML = json.message;
            console.log(json);
            document.getElementById("eventsDateRangeDisplay").innerHTML = json.date_range;
            console.log(json.date_range);
          } else {
            mz_reset_navigation(this, buttons);
            container.toggleClass('loader');
            container.html(json.message);
          }
        }
      }).fail(function (json) {
        mz_reset_navigation(this, buttons);
        container.toggleClass('loader');
        container.html('Sorry but there was an error retrieving schedule.');
      }); // End Ajax
    }); // End click navigation

    function mz_reset_navigation(el, buttons) {
      // Reset nav link "offset" data attribute
      if (el.className == 'previous') {
        buttons.forEach(function (button) {
          button.setAttribute('data-offset', parseInt(button.getAttribute('data-offset')) + parseInt(1));
        });
      } else if (el.className == 'following') {
        buttons.forEach(function (button) {
          button.setAttribute('data-offset', button.getAttribute('data-offset') - 1);
        });
      }
    }
    /**
     * Event Description Modal
     *
     *
     */


    $(document).on('click', "a[data-target=mzDescriptionModal]", function (e) {
      e.preventDefault();
      var target = $(this).attr("href"),
          staffName = this.getAttribute('data-staffName'),
          eventImage = this.getAttribute('data-eventImage'),
          classDescription = decodeURIComponent(this.getAttribute('data-classDescription')),
          popUpContent = '<h3>' + this.innerHTML + ' ' + mz_mindbody_schedule.with + ' ' + staffName + '</h3>';
      popUpContent += '<div class="mz-classInfo" id="ClassInfo">';
      popUpContent += '<p><img src="' + eventImage + '" class="mz_modal_event_image_body">' + classDescription + '</p>';
      popUpContent += '</div>'; // load the url and show modal on success

      $("#mzModal").load(target, function () {
        $.colorbox({
          html: popUpContent,
          href: target
        });
        $("#mzModal").colorbox();
      });
      return false;
    });
    /**
     * Staff Modal
     *
     *
     */

    $(document).on('click', "a[data-target=mzStaffScheduleModal]", function (ev) {
      ev.preventDefault();
      var target = $(this).attr("href");
      var staffName = $(this).attr('data-staffName');
      var staffBio = decodeURIComponent($(this).attr('data-staffBio'));
      var staffImage = $(this).attr('data-staffImage');
      var popUpContent = '<h3>' + staffName + '</h3><div class="mz-staffInfo" id="StaffInfo">';
      popUpContent += '<p><img src="' + staffImage + '" class="mz_modal_staff_image_body">' + staffBio + '</p>';
      popUpContent += '</div>';
      $("#mzModal").load(target, function () {
        $.colorbox({
          html: popUpContent,
          href: target
        });
        $("#mzModal").colorbox();
      });
    });
    /**
     * Location Filter
     *
     * Hide or Display events based on location when buttons clicked
     */

    $(document).on('click', ".filter_btn", function (ev) {
      ev.preventDefault();
      $('#locations_filter').children('a').removeClass('active');

      if (this.dataset.location === 'all') {
        $('.mz_full_listing_event').hide();
        $('.mz_full_listing_event').show(1000);
      } else {
        $('.mz_full_listing_event').hide();
        $('.' + this.dataset.location).show(1000);
      }

      $(this).toggleClass('active');
    });
  }); // End document ready
})(jQuery);

/***/ })

/******/ });
//# sourceMappingURL=events-display.js.map