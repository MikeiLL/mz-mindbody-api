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
/******/ 	return __webpack_require__(__webpack_require__.s = "./scripts/admin.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./scripts/admin.js":
/*!**************************!*\
  !*** ./scripts/admin.js ***!
  \**************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


(function ($) {
  'use strict';

  $(document).ready(function ($) {
    // Initialize some variables
    var mz_admin_nonce = mz_mindbody_schedule.admin_nonce,
        clear_transients_nonce = mz_mindbody_schedule.clear_transients_nonce,
        get_save_token_nonce = mz_mindbody_schedule.get_save_token_nonce,
        test_credentials_nonce = mz_mindbody_schedule.test_credentials_nonce,
        test_credentials_v5_nonce = mz_mindbody_schedule.test_credentials_v5_nonce,
        deduce_class_owners_nonce = mz_mindbody_schedule.deduce_class_owners_nonce,
        cancel_excess_api_alerts = mz_mindbody_schedule.cancel_excess_api_alerts,
        atts = mz_mindbody_schedule.atts;
    /**
     * Clear Transients
     *
     *
     */

    $('#mzClearTransients').on('click', function (e) {
      e.preventDefault();
      $.ajax({
        type: "post",
        dataType: "json",
        context: this,
        url: mz_mindbody_schedule.ajaxurl,
        data: {
          action: 'mz_mbo_clear_transients',
          nonce: clear_transients_nonce
        },
        success: function success(json) {
          if (json.type == "success") {
            alert(json.message);
          } else {
            alert('Something went wrong.');
          }
        }
      }) // End ajax
      .fail(function (json) {
        console.log('fail');
        console.log(json);
        alert('Something went wrong.');
      });
    }); // End Clear Transients

    /**
    * Cancel API Excess Alerts
    *
    *
    */

    $('#mzCancelAPIExcessAlerts').on('click', function (e) {
      e.preventDefault();
      $.ajax({
        type: "post",
        dataType: "json",
        context: this,
        url: mz_mindbody_schedule.ajaxurl,
        data: {
          action: 'mz_mbo_excess_api_alerts',
          nonce: cancel_excess_api_alerts
        },
        success: function success(json) {
          if (json.type == "success") {
            alert(json.message);
          } else {
            alert('Something went wrong.');
          }
        }
      }) // End ajax
      .fail(function (json) {
        console.log('fail');
        console.log(json);
        alert('Something went wrong.');
      });
    }); // End Clear Transients

    /**
     * Update Site Token
     *
     *
     */

    $('#mzUpdateSiteToken').on('click', function (e) {
      e.preventDefault();
      $.ajax({
        type: "post",
        dataType: "json",
        context: this,
        url: mz_mindbody_schedule.ajaxurl,
        data: {
          action: 'mz_mbo_get_and_save_staff_token_token',
          nonce: get_save_token_nonce
        },
        success: function success(json) {
          if (json.type == "success") {
            alert(" New token retrieved and saved: " + json.message);
          } else {
            alert('Something went wrong.');
          }
        }
      }) // End ajax
      .fail(function (json) {
        console.log('fail');
        console.log(json);
        alert('Something went wrong.');
      });
    }); // End Clear Transients

    /**
     * Test Credentials
     *
     *
     */

    $('#mzTestCredentials').on('click', function (e) {
      e.preventDefault();
      var self = $(this);
      self.addClass('disabled');
      self.after('<img id="class_owners_spinner" src="' + mz_mindbody_schedule.spinner + '"/>');
      $.ajax({
        type: "post",
        dataType: "json",
        context: this,
        url: mz_mindbody_schedule.ajaxurl,
        data: {
          action: 'mz_mbo_test_credentials',
          nonce: test_credentials_nonce
        },
        success: function success(json) {
          if (json.type == "success") {
            self.removeClass('disabled');
            $('#class_owners_spinner').remove();
            $('#displayTest').html(json.message);
          } else {
            self.removeClass('disabled');
            $('#class_owners_spinner').remove();
            alert('Something went wrong.');
          }
        }
      }) // End ajax
      .fail(function (json) {
        self.removeClass('disabled');
        $('#class_owners_spinner').remove();
        console.log('fail');
        console.log(json);
        alert('Something went wrong.');
      });
    }); // End Clear Transients

    /**
     * Test Credentials
     *
     *
     */

    $('#mzTestCredentialsV5').on('click', function (e) {
      e.preventDefault();
      var self = $(this);
      self.addClass('disabled');
      self.after('<img id="class_owners_spinner" src="' + mz_mindbody_schedule.spinner + '"/>');
      $.ajax({
        type: "post",
        dataType: "json",
        context: this,
        url: mz_mindbody_schedule.ajaxurl,
        data: {
          action: 'mz_mbo_test_credentials_v5',
          nonce: test_credentials_v5_nonce
        },
        success: function success(json) {
          if (json.type == "success") {
            self.removeClass('disabled');
            $('#class_owners_spinner').remove();
            $('#displayTestV5').html(json.message);
          } else {
            self.removeClass('disabled');
            $('#class_owners_spinner').remove();
            alert('Something went wrong.');
          }
        }
      }) // End ajax
      .fail(function (json) {
        self.removeClass('disabled');
        $('#class_owners_spinner').remove();
        console.log('fail');
        console.log(json);
        alert('Something went wrong.');
      });
    }); // End Clear Transients

    /**
     * Reset Class Owners
     *
     * Call the mz_deduce_class_owners method of the RetrieveClassOwners class
     * via Ajax.
     *
     * This function is used by the Admin Options Advanced section
     * to call the php function that resets the transient holding the
     * array of probable "owners" of various classes, used to display
     * who a substitute is substituting for.
     *
     * We log the matrix into the browser console.
     *
     */

    $("a.class_owners").on('click', function (ev) {
      var self = $(this);
      self.addClass('disabled');
      self.after('<img id="class_owners_spinner" src="' + mz_mindbody_schedule.spinner + '"/>');
      $.ajax({
        type: "post",
        dataType: "json",
        context: this,
        url: mz_mindbody_schedule.ajaxurl,
        data: {
          action: 'mz_deduce_class_owners',
          nonce: deduce_class_owners_nonce
        },
        success: function success(json) {
          self.removeClass('disabled');
          $('#class_owners_spinner').remove();

          if (json.type == "success") {
            console.log(json.message);
            alert('Class Owners Matrix Reset');
          } else {
            console.log(json);
            alert('Something went wrong.');
          }
        }
      }) // End ajax
      .fail(function (json) {
        self.removeClass('disabled');
        $('#class_owners_spinner').remove();
        console.log('fail');
        console.log(json);
        alert('Something went wrong.');
      });
      return false;
    });
  }); // End document ready
})(jQuery);

/***/ })

/******/ });
//# sourceMappingURL=admin.js.map