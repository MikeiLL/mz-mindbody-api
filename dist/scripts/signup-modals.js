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
/******/ 	return __webpack_require__(__webpack_require__.s = "./scripts/signup-modals.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./scripts/signup-modals.js":
/*!**********************************!*\
  !*** ./scripts/signup-modals.js ***!
  \**********************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


(function ($) {
  $(document).ready(function ($) {
    // Initialize some variables
    var nonce = mz_mindbody_schedule.signup_nonce,
        // Shortcode atts for current page.
    atts = mz_mindbody_schedule.atts,
        // Just one location for use in general MBO site link
    location = atts.locations[0].toString(),
        siteID = atts.account ? atts.account : mz_mindbody_schedule.account;
    /**
     * State will store and track status
     */

    var mz_mbo_state = {
      logged_in: mz_mindbody_schedule.loggedMBO == 1 ? true : false,
      action: undefined,
      target: undefined,
      siteID: undefined,
      nonce: undefined,
      location: undefined,
      classID: undefined,
      className: undefined,
      staffName: undefined,
      classTime: undefined,
      class_title: undefined,
      content: undefined,
      spinner: '<i class="fa fa-spinner fa-3x fa-spin" style="position: fixed; top: 50%; left: 50%;"></i>',
      wrapper: undefined,
      content_wrapper: '<div class="modal__content" id="signupModalContent"></div>',
      footer: '<div class="modal__footer" id="signupModalFooter">\n' + '    <a class="btn btn-primary" data-nonce="' + mz_mindbody_schedule.signup_nonce + '" id="MBOSchedule" target="_blank">My Classes</a>\n' + '    <a href="https://clients.mindbodyonline.com/ws.asp?&amp;sLoc=' + mz_mindbody_schedule.location + '&studioid=' + mz_mindbody_schedule.siteID + '>" class="btn btn-primary btn-xs" id="MBOSite">Manage on Mindbody Site></a>\n' + '    <a class="btn btn-primary btn-xs" id="MBOLogout">Logout</a>\n' + '</div>\n',
      header: undefined,
      signup_button: undefined,
      message: undefined,
      client_first_name: undefined,
      login_form: $('#mzLogInContainer').html(),
      initialize: function initialize(target) {
        this.target = $(target).attr("href");
        this.siteID = $(target).attr('data-siteID');
        this.nonce = $(target).attr("data-nonce");
        this.location = $(target).attr("data-location");
        this.classID = $(target).attr("data-classID");
        this.className = $(target).attr("data-className");
        this.staffName = $(target).attr("data-staffName");
        this.classTime = $(target).attr("data-time");
        this.class_title = '<h2>' + this.className + ' ' + mz_mindbody_schedule.with + ' ' + this.staffName + '</h2><h3>' + this.classTime + '</h3><hr/>';
        this.header = '<div class="modal__header" id="modalHeader"><h1>' + mz_mindbody_schedule.signup_heading + '</h1>' + this.class_title + '</div>';
        this.signup_button = '<button class="btn btn-primary" data-nonce="' + this.nonce + '" data-location="' + this.location + '" data-classID="' + this.classID + '" id="signUpForClass">' + mz_mindbody_schedule.confirm_signup + '</button>';
      }
    };
    /*
     * Define the modal container state which changes depending on login state
     */

    function render_mbo_modal() {
      var message = mz_mbo_state.message ? '<p>' + mz_mbo_state.message + '</p>' : '';
      mz_mbo_state.wrapper = '<div class="modal__wrapper" id="signupModalWrapper">';

      if (mz_mbo_state.logged_in) {
        mz_mbo_state.wrapper += mz_mbo_state.header;
        mz_mbo_state.wrapper += '<div class="modal__content" id="signupModalContent">' + message + mz_mbo_state.signup_button + '</div>';
        mz_mbo_state.wrapper += mz_mbo_state.footer;
      } else {
        mz_mbo_state.wrapper += mz_mbo_state.header;
        mz_mbo_state.wrapper += '<div class="modal__content" id="signupModalContent">' + message + mz_mbo_state.login_form + '</div>';
      }

      mz_mbo_state.wrapper += '</div>';

      if ($('#cboxLoadedContent')) {
        $('#cboxLoadedContent').html(mz_mbo_state.wrapper);
      }

      mz_mbo_state.message = undefined;
    }
    /*
     * Render inner content of modal based on state
     */


    function render_mbo_modal_activity() {
      // Clear content and content wrapper
      mz_mbo_state.content = '';
      $('#signupModalContent').html = '';

      if (mz_mbo_state.action == 'processing') {
        mz_mbo_state.content += mz_mbo_state.spinner;
      } else if (mz_mbo_state.action == 'login_failed') {
        mz_mbo_state.content += mz_mbo_state.message;
        mz_mbo_state.content += mz_mbo_state.login_form;
      } else if (mz_mbo_state.action == 'logout') {
        mz_mbo_state.content += mz_mbo_state.message;
        mz_mbo_state.content += mz_mbo_state.login_form;
        $('#signupModalFooter').remove();
      } else if (mz_mbo_state.action == 'error') {
        mz_mbo_state.content += mz_mbo_state.message;
      } else {
        // login, sign_up_form
        mz_mbo_state.content += mz_mbo_state.message;
      }

      if ($('#signupModalContent')) {
        $('#signupModalContent').html(mz_mbo_state.content);
      }

      mz_mbo_state.message = undefined;
    }
    /**
     * Continually Check if Client is Logged in and Update Status
     */


    setInterval(mz_mbo_check_client_logged, 5000);

    function mz_mbo_check_client_logged() {
      //this will repeat every 5 seconds
      $.ajax({
        dataType: 'json',
        url: mz_mindbody_schedule.ajaxurl,
        data: {
          action: 'mz_check_client_logged',
          nonce: 'mz_check_client_logged'
        },
        success: function success(json) {
          if (json.type == "success") {
            mz_mbo_state.logged_in = json.message == 1 ? true : false;
          }
        } // ./ Ajax Success

      }); // End Ajax
    }
    /**
     * Initial Modal Window to Register for a Class
     *
     * Also leads to options to login and sign-up with MBO
     *
     */


    $(document).on('click', "a[data-target=mzSignUpModal]", function (ev) {
      ev.preventDefault();
      mz_mbo_state.initialize(this);
      render_mbo_modal();
      $("#mzSignUpModal").load(mz_mbo_state.target, function () {
        $.colorbox({
          html: mz_mbo_state.wrapper,
          href: mz_mbo_state.target
        });
        $("#mzSignUpModal").colorbox();
      });
    });
    /**
     * Sign In to MBO
     */

    $(document).on('submit', 'form[id="mzLogIn"]', function (ev) {
      ev.preventDefault();
      var form = $(this);
      var formData = form.serializeArray();
      var result = {};
      $.each($('form').serializeArray(), function () {
        result[this.name] = this.value;
      });
      $.ajax({
        dataType: 'json',
        url: mz_mindbody_schedule.ajaxurl,
        type: form.attr('method'),
        context: this,
        // So we have access to form data within ajax results
        data: {
          action: 'mz_client_log_in',
          form: form.serialize(),
          nonce: result.nonce,
          classID: result.classID,
          staffName: result.staffName,
          classTime: result.classTime,
          location: result.location
        },
        beforeSend: function beforeSend() {
          mz_mbo_state.action = 'processing';
          render_mbo_modal_activity();
        },
        success: function success(json) {
          var formData = $(this).serializeArray();
          var result = {};
          $.each($('form').serializeArray(), function () {
            result[this.name] = this.value;
          });

          if (json.type == "success") {
            mz_mbo_state.logged_in = true;
            mz_mbo_state.action = 'login';
            mz_mbo_state.message = json.message;
            render_mbo_modal();
          } else {
            mz_mbo_state.action = 'login_failed';
            mz_mbo_state.message = json.message;
            render_mbo_modal_activity();
          }
        } // ./ Ajax Success

      }) // End Ajax
      .fail(function (json) {
        mz_mbo_state.message = 'ERROR SIGNING IN';
        render_mbo_modal_activity();
        console.log(json);
      }); // End Fail
    });
    /**
     * Logout of MBO
     *
     *
     */

    $(document).on('click', "#MBOLogout", function (ev) {
      ev.preventDefault();
      var nonce = $(this).attr("data-nonce");
      $.ajax({
        dataType: 'json',
        url: mz_mindbody_schedule.ajaxurl,
        data: {
          action: 'mz_client_log_out',
          nonce: nonce
        },
        beforeSend: function beforeSend() {
          mz_mbo_state.action = 'processing';
          render_mbo_modal_activity();
        },
        success: function success(json) {
          if (json.type == "success") {
            mz_mbo_state.logged_in = false;
            mz_mbo_state.action = 'logout';
            mz_mbo_state.message = json.message;
            render_mbo_modal_activity();
          } else {
            mz_mbo_state.action = 'logout_failed';
            mz_mbo_state.message = json.message;
            render_mbo_modal_activity();
          }
        } // ./ Ajax Success

      }) // End Ajax
      .fail(function (json) {
        mz_mbo_state.message = 'ERROR LOGGING OUT';
        render_mbo_modal_activity();
        console.log(json);
      }); // End Fail
    });
    /**
     * Display MBO Account Registration form within Sign-Up Modal
     *
     *
     */

    $(document).on('click', "a#createMBOAccount", function (ev) {
      ev.preventDefault();
      var target = $(this).attr("href");
      var nonce = $(this).attr("data-nonce");
      var classID = $(this).attr("data-classID");
      $.ajax({
        type: "GET",
        dataType: 'json',
        url: mz_mindbody_schedule.ajaxurl,
        data: {
          action: 'mz_generate_signup_form',
          nonce: nonce,
          classID: classID
        },
        beforeSend: function beforeSend() {
          mz_mbo_state.action = 'processing';
          render_mbo_modal_activity();
        },
        success: function success(json) {
          if (json.type == "success") {
            mz_mbo_state.logged_in = true;
            mz_mbo_state.action = 'sign_up_form';
            mz_mbo_state.message = json.message;
            render_mbo_modal_activity();
          } else {
            mz_mbo_state.action = 'error';
            mz_mbo_state.message = json.message;
            render_mbo_modal_activity();
          }
        } // ./ Ajax Success

      }) // End Ajax
      .fail(function (json) {
        mz_mbo_state.message = 'ERROR GENERATING THE SIGN-UP FORM';
        render_mbo_modal_activity();
        console.log(json);
      }); // End Fail
    });
    /**
     * Create MBO Account and display Confirmation
     *
     *
     */

    $(document).on('submit', 'form[id="mzSignUp"]', function (ev) {
      ev.preventDefault();
      var target = $(this).attr("href");
      var form = $(this);
      var nonce = $(this).attr("data-nonce");
      var classID = $(this).attr("data-classID");
      var formData = form.serializeArray();
      $.ajax({
        type: "GET",
        dataType: 'json',
        url: mz_mindbody_schedule.ajaxurl,
        data: {
          action: 'mz_create_mbo_account',
          nonce: formData.nonce,
          classID: formData.classID,
          form: form.serialize()
        },
        beforeSend: function beforeSend() {
          mz_mbo_state.action = 'processing';
          render_mbo_modal_activity();
        },
        success: function success(json) {
          if (json.type == "success") {
            mz_mbo_state.logged_in = true;
            mz_mbo_state.action = 'login';
            mz_mbo_state.message = json.message;
            render_mbo_modal_activity();
          } else {
            mz_mbo_state.action = 'error';
            mz_mbo_state.message = json.message;
            render_mbo_modal_activity();
          }
        } // ./ Ajax Success

      }) // End Ajax
      .fail(function (json) {
        mz_mbo_state.message = 'ERROR CREATING ACCOUNT';
        render_mbo_modal_activity();
        console.log(json);
      }); // End Fail
    });
    /**
     * Register for a class
     */

    $(document).on('click', '#signUpForClass', function (ev) {
      ev.preventDefault();
      var nonce = $(this).attr("data-nonce");
      $.ajax({
        type: "GET",
        dataType: 'json',
        url: mz_mindbody_schedule.ajaxurl,
        context: this,
        data: {
          action: 'mz_register_for_class',
          nonce: nonce,
          siteID: mz_mbo_state.siteID,
          classID: mz_mbo_state.classID,
          location: mz_mbo_state.location
        },
        beforeSend: function beforeSend() {
          mz_mbo_state.action = 'processing';
          render_mbo_modal_activity();
        },
        success: function success(json) {
          if (json.type == "success") {
            mz_mbo_state.action = 'register';
            mz_mbo_state.message = json.message;
            render_mbo_modal_activity();
          } else {
            mz_mbo_state.action = 'error';
            mz_mbo_state.message = 'ERROR REGISTERING FOR CLASS. ' + json.message;
            render_mbo_modal_activity();
          }
        } // ./ Ajax Success

      }) // End Ajax
      .fail(function (json) {
        mz_mbo_state.message = 'ERROR REGISTERING FOR CLASS';
        render_mbo_modal_activity();
        console.log(json);
      }); // End Fail
    });
    /**
     * Display Client Schedule within Sign-Up Modal
     *
     *
     */

    $(document).on('click', "a#MBOSchedule", function (ev) {
      ev.preventDefault();
      $.ajax({
        type: "GET",
        dataType: 'json',
        url: mz_mindbody_schedule.ajaxurl,
        data: {
          action: 'mz_display_client_schedule',
          nonce: 'mz_display_client_schedule',
          location: mz_mbo_state.location,
          siteID: mz_mbo_state.siteID
        },
        beforeSend: function beforeSend() {
          mz_mbo_state.action = 'processing';
          render_mbo_modal_activity();
        },
        success: function success(json) {
          if (json.type == "success") {
            mz_mbo_state.action = 'display_schedule';
            mz_mbo_state.message = json.message;
            render_mbo_modal_activity();
          } else {
            mz_mbo_state.action = 'error';
            mz_mbo_state.message = 'ERROR RETRIEVING YOUR SCHEDULE. ' + json.message;
            render_mbo_modal_activity();
          }
        } // ./ Ajax Success

      }) // End Ajax
      .fail(function (json) {
        mz_mbo_state.message = 'ERROR RETRIEVING YOUR SCHEDULE';
        render_mbo_modal_activity();
        console.log(json);
      }); // End Fail
    });
  }); // End document ready
})(jQuery);

/***/ })

/******/ });
//# sourceMappingURL=signup-modals.js.map