!function(e){var n={};function s(o){if(n[o])return n[o].exports;var t=n[o]={i:o,l:!1,exports:{}};return e[o].call(t.exports,t,t.exports,s),t.l=!0,t.exports}s.m=e,s.c=n,s.d=function(e,n,o){s.o(e,n)||Object.defineProperty(e,n,{enumerable:!0,get:o})},s.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},s.t=function(e,n){if(1&n&&(e=s(e)),8&n)return e;if(4&n&&"object"==typeof e&&e&&e.__esModule)return e;var o=Object.create(null);if(s.r(o),Object.defineProperty(o,"default",{enumerable:!0,value:e}),2&n&&"string"!=typeof e)for(var t in e)s.d(o,t,function(n){return e[n]}.bind(null,t));return o},s.n=function(e){var n=e&&e.__esModule?function(){return e.default}:function(){return e};return s.d(n,"a",n),n},s.o=function(e,n){return Object.prototype.hasOwnProperty.call(e,n)},s.p="",s(s.s=6)}({6:function(e,n,s){"use strict";jQuery(document).ready((function(e){var n=mz_mindbody_schedule.nonce;mz_mindbody_schedule.atts,e("#mzClearTransients").on("click",(function(s){s.preventDefault(),e.ajax({type:"post",dataType:"json",context:this,url:mz_mindbody_schedule.ajaxurl,data:{action:"mz_mbo_clear_transients",nonce:n},success:function(e){"success"==e.type?alert(e.message):alert("Something went wrong.")}}).fail((function(e){console.log("fail"),console.log(e),alert("Something went wrong.")}))})),e("#mzTestCredentials").on("click",(function(s){s.preventDefault();var o=e(this);o.addClass("disabled"),o.after('<img id="class_owners_spinner" src="'+mz_mindbody_schedule.spinner+'"/>'),e.ajax({type:"post",dataType:"json",context:this,url:mz_mindbody_schedule.ajaxurl,data:{action:"mz_mbo_test_credentials",nonce:n},success:function(n){"success"==n.type?(o.removeClass("disabled"),e("#class_owners_spinner").remove(),e("#displayTest").html(n.message)):(o.removeClass("disabled"),e("#class_owners_spinner").remove(),alert("Something went wrong."))}}).fail((function(n){o.removeClass("disabled"),e("#class_owners_spinner").remove(),console.log("fail"),console.log(n),alert("Something went wrong.")}))})),e("#mzTestCredentialsV5").on("click",(function(s){s.preventDefault();var o=e(this);o.addClass("disabled"),o.after('<img id="class_owners_spinner" src="'+mz_mindbody_schedule.spinner+'"/>'),e.ajax({type:"post",dataType:"json",context:this,url:mz_mindbody_schedule.ajaxurl,data:{action:"mz_mbo_test_credentials_v5",nonce:n},success:function(n){"success"==n.type?(o.removeClass("disabled"),e("#class_owners_spinner").remove(),e("#displayTestV5").html(n.message)):(o.removeClass("disabled"),e("#class_owners_spinner").remove(),alert("Something went wrong."))}}).fail((function(n){o.removeClass("disabled"),e("#class_owners_spinner").remove(),console.log("fail"),console.log(n),alert("Something went wrong.")}))})),e("a.class_owners").on("click",(function(s){var o=e(this);return o.addClass("disabled"),o.after('<img id="class_owners_spinner" src="'+mz_mindbody_schedule.spinner+'"/>'),e.ajax({type:"post",dataType:"json",context:this,url:mz_mindbody_schedule.ajaxurl,data:{action:"mz_deduce_class_owners",nonce:n},success:function(n){o.removeClass("disabled"),e("#class_owners_spinner").remove(),"success"==n.type?(console.log(n.message),alert("Class Owners Matrix Reset")):(console.log(n),alert("Something went wrong."))}}).fail((function(n){o.removeClass("disabled"),e("#class_owners_spinner").remove(),console.log("fail"),console.log(n),alert("Something went wrong.")})),!1}))}))}});
//# sourceMappingURL=admin.js.map