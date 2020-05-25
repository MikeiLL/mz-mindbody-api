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
eval("\n\n(function ($) {\n  'use strict';\n\n  $(document).ready(function ($) {\n    // Initialize some variables\n    var nonce = mz_mindbody_schedule.nonce,\n        atts = mz_mindbody_schedule.atts;\n    /**\n     * Clear Transients\n     *\n     *\n     */\n\n    $('#mzClearTransients').on('click', function (e) {\n      e.preventDefault();\n      $.ajax({\n        type: \"post\",\n        dataType: \"json\",\n        context: this,\n        url: mz_mindbody_schedule.ajaxurl,\n        data: {\n          action: 'mz_mbo_clear_transients',\n          nonce: nonce\n        },\n        success: function success(json) {\n          if (json.type == \"success\") {\n            alert(json.message);\n          } else {\n            alert('Something went wrong.');\n          }\n        }\n      }) // End ajax\n      .fail(function (json) {\n        console.log('fail');\n        console.log(json);\n        alert('Something went wrong.');\n      });\n    }); // End Clear Transients\n\n    /**\n     * Test Credentials\n     *\n     *\n     */\n\n    $('#mzTestCredentials').on('click', function (e) {\n      e.preventDefault();\n      var self = $(this);\n      self.addClass('disabled');\n      self.after('<img id=\"class_owners_spinner\" src=\"' + mz_mindbody_schedule.spinner + '\"/>');\n      $.ajax({\n        type: \"post\",\n        dataType: \"json\",\n        context: this,\n        url: mz_mindbody_schedule.ajaxurl,\n        data: {\n          action: 'mz_mbo_test_credentials',\n          nonce: nonce\n        },\n        success: function success(json) {\n          if (json.type == \"success\") {\n            self.removeClass('disabled');\n            $('#class_owners_spinner').remove();\n            $('#displayTest').html(json.message);\n          } else {\n            self.removeClass('disabled');\n            $('#class_owners_spinner').remove();\n            alert('Something went wrong.');\n          }\n        }\n      }) // End ajax\n      .fail(function (json) {\n        self.removeClass('disabled');\n        $('#class_owners_spinner').remove();\n        console.log('fail');\n        console.log(json);\n        alert('Something went wrong.');\n      });\n    }); // End Clear Transients\n\n    /**\n     * Test Credentials\n     *\n     *\n     */\n\n    $('#mzTestCredentialsV5').on('click', function (e) {\n      e.preventDefault();\n      var self = $(this);\n      self.addClass('disabled');\n      self.after('<img id=\"class_owners_spinner\" src=\"' + mz_mindbody_schedule.spinner + '\"/>');\n      $.ajax({\n        type: \"post\",\n        dataType: \"json\",\n        context: this,\n        url: mz_mindbody_schedule.ajaxurl,\n        data: {\n          action: 'mz_mbo_test_credentials_v5',\n          nonce: nonce\n        },\n        success: function success(json) {\n          if (json.type == \"success\") {\n            self.removeClass('disabled');\n            $('#class_owners_spinner').remove();\n            $('#displayTestV5').html(json.message);\n          } else {\n            self.removeClass('disabled');\n            $('#class_owners_spinner').remove();\n            alert('Something went wrong.');\n          }\n        }\n      }) // End ajax\n      .fail(function (json) {\n        self.removeClass('disabled');\n        $('#class_owners_spinner').remove();\n        console.log('fail');\n        console.log(json);\n        alert('Something went wrong.');\n      });\n    }); // End Clear Transients\n\n    /**\n     * Reset Class Owners\n     *\n     * Call the mz_deduce_class_owners method of the Retrieve_Class_Owners class\n     * via Ajax.\n     *\n     * This function is used by the Admin Options Advanced section\n     * to call the php function that resets the transient holding the\n     * array of probable \"owners\" of various classes, used to display\n     * who a substitute is substituting for.\n     *\n     * We log the matrix into the browser console.\n     *\n     */\n\n    $(\"a.class_owners\").on('click', function (ev) {\n      var self = $(this);\n      self.addClass('disabled');\n      self.after('<img id=\"class_owners_spinner\" src=\"' + mz_mindbody_schedule.spinner + '\"/>');\n      $.ajax({\n        type: \"post\",\n        dataType: \"json\",\n        context: this,\n        url: mz_mindbody_schedule.ajaxurl,\n        data: {\n          action: 'mz_deduce_class_owners',\n          nonce: nonce\n        },\n        success: function success(json) {\n          self.removeClass('disabled');\n          $('#class_owners_spinner').remove();\n\n          if (json.type == \"success\") {\n            console.log(json.message);\n            alert('Class Owners Matrix Reset');\n          } else {\n            console.log(json);\n            alert('Something went wrong.');\n          }\n        }\n      }) // End ajax\n      .fail(function (json) {\n        self.removeClass('disabled');\n        $('#class_owners_spinner').remove();\n        console.log('fail');\n        console.log(json);\n        alert('Something went wrong.');\n      });\n      return false;\n    });\n  }); // End document ready\n})(jQuery);\n\n//# sourceURL=webpack:///./scripts/admin.js?");

/***/ })

/******/ });