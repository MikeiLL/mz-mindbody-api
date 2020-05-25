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
/******/ 	return __webpack_require__(__webpack_require__.s = "./scripts/staff_popup.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./scripts/staff_popup.js":
/*!********************************!*\
  !*** ./scripts/staff_popup.js ***!
  \********************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


(function ($) {
  $(document).ready(function ($) {
    // Some colorbox global settings
    $.colorbox.settings.width = $(window).innerWidth() <= 500 ? '95%' : '75%';
    $.colorbox.settings.height = '75%';
    /** Colorbox resize function
     * source: https://github.com/jackmoore/colorbox/issues/158
     */

    var resizeTimer;

    function resizeColorBox() {
      if (resizeTimer) clearTimeout(resizeTimer);
      resizeTimer = setTimeout(function () {
        if (jQuery('#cboxOverlay').is(':visible')) {
          jQuery.colorbox.resize({
            width: '90%',
            height: '90%'
          });
        }
      }, 300);
    } // Resize Colorbox when resizing window or changing mobile device orientation


    $(window).resize(resizeColorBox);
    window.addEventListener("orientationchange", resizeColorBox, false);
    $("a[data-target=#mzStaffModal]").click(function (ev) {
      ev.preventDefault();
      var target = $(this).attr("href");
      var staffBio = decodeURIComponent($(this).attr('data-staffBio'));
      var staffName = $(this).attr('data-staffName');
      var siteID = $(this).attr('data-siteID');
      var staffID = $(this).attr('data-staffID');
      var mbo_url_parts = ['http://clients.mindbodyonline.com/ws.asp?studioid=', '&stype=-7&sView=week&sTrn='];
      var staffImage = decodeURIComponent($(this).attr('data-staffImage'));
      var popUpContent = '<div class="mz_staffName"><h3>' + staffName + '</h3>';
      popUpContent += '<img class="mz-staffImage" src="' + staffImage + '" />';
      popUpContent += '<div class="mz_staffBio">' + staffBio + '</div></div>';
      popUpContent += '<br/><a href="' + mbo_url_parts[0] + siteID + mbo_url_parts[1] + staffID + '" ';
      popUpContent += 'class="btn btn-info mz-btn-info mz-bio-button" target="_blank">See ' + staffName + '&apos;s Schedule</a>'; // load the url and show modal on success

      $("#mzStaffModal").load(target, function () {
        $.colorbox({
          html: popUpContent,
          width: "75%"
        });
        $("#mzStaffModal").colorbox();
      });
    }); // End click
  }); // End document ready
})(jQuery);

/***/ })

/******/ });
//# sourceMappingURL=staff_popup.js.map