!function(e){var t={};function n(a){if(t[a])return t[a].exports;var o=t[a]={i:a,l:!1,exports:{}};return e[a].call(o.exports,o,o.exports,n),o.l=!0,o.exports}n.m=e,n.c=t,n.d=function(e,t,a){n.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:a})},n.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},n.t=function(e,t){if(1&t&&(e=n(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var a=Object.create(null);if(n.r(a),Object.defineProperty(a,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var o in e)n.d(a,o,function(t){return e[t]}.bind(null,o));return a},n.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return n.d(t,"a",t),t},n.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},n.p="",n(n.s=14)}({14:function(e,t,n){"use strict";jQuery(document).ready((function(e){mz_mindbody_schedule.signup_nonce;var t=mz_mindbody_schedule.atts,n=(t.locations[0].toString(),t.account?t.account:mz_mindbody_schedule.account,{logged_in:1==mz_mindbody_schedule.loggedMBO,action:void 0,target:void 0,siteID:void 0,nonce:void 0,location:void 0,classID:void 0,className:void 0,staffName:void 0,classTime:void 0,class_title:void 0,content:void 0,spinner:'<div class="d-flex justify-content-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div>',wrapper:void 0,content_wrapper:'<div class="modal__content" id="signupModalContent"></div>',footer:'<div class="modal__footer" id="signupModalFooter">\n    <a class="btn btn-primary" data-nonce="'+mz_mindbody_schedule.signup_nonce+'" id="MBOSchedule" target="_blank">My Classes</a>\n    <a href="https://clients.mindbodyonline.com/ws.asp?&amp;sLoc='+mz_mindbody_schedule.location+"&studioid="+mz_mindbody_schedule.siteID+'>" class="btn btn-primary btn-xs" id="MBOSite">Manage on Mindbody Site></a>\n    <a class="btn btn-primary btn-xs" id="MBOLogout">Logout</a>\n</div>\n',header:void 0,signup_button:void 0,message:void 0,client_first_name:void 0,login_form:e("#mzLogInContainer").html(),initialize:function(t){this.target=e(t).attr("href"),this.siteID=e(t).attr("data-siteID"),this.nonce=e(t).attr("data-nonce"),this.location=e(t).attr("data-location"),this.classID=e(t).attr("data-classID"),this.className=e(t).attr("data-className"),this.staffName=e(t).attr("data-staffName"),this.classTime=e(t).attr("data-time"),this.class_title="<h2>"+this.className+" "+mz_mindbody_schedule.with+" "+this.staffName+"</h2><h3>"+this.classTime+"</h3><hr/>",this.header='<div class="modal__header" id="modalHeader"><h1>'+mz_mindbody_schedule.signup_heading+"</h1>"+this.class_title+"</div>",this.signup_button='<button class="btn btn-primary" data-nonce="'+this.nonce+'" data-location="'+this.location+'" data-classID="'+this.classID+'" id="signUpForClass">'+mz_mindbody_schedule.confirm_signup+"</button>"}});function a(){var t=n.message?"<p>"+n.message+"</p>":"";n.wrapper='<div class="modal__wrapper" id="signupModalWrapper">',n.logged_in?(n.wrapper+=n.header,n.wrapper+='<div class="modal__content" id="signupModalContent">'+t+n.signup_button+"</div>",n.wrapper+=n.footer):(n.wrapper+=n.header,n.wrapper+='<div class="modal__content" id="signupModalContent">'+t+n.login_form+"</div>"),n.wrapper+="</div>",e("#cboxLoadedContent")&&e("#cboxLoadedContent").html(n.wrapper),n.message=void 0}function o(){n.content="",e("#signupModalContent").html="","processing"==n.action?n.content+=n.spinner:"login_failed"==n.action?(n.content+=n.message,n.content+=n.login_form):"logout"==n.action?(n.content+=n.message,n.content+=n.login_form,e("#signupModalFooter").remove()):(n.action,n.content+=n.message),e("#signupModalContent")&&e("#signupModalContent").html(n.content),n.message=void 0}setInterval((function(){e.ajax({dataType:"json",url:mz_mindbody_schedule.ajaxurl,data:{action:"mz_check_client_logged",nonce:"mz_check_client_logged"},success:function(e){"success"==e.type&&(n.logged_in=1==e.message)}})}),5e3),e(document).on("click","a[data-target=mzSignUpModal]",(function(t){t.preventDefault(),n.initialize(this),a(),e("#mzSignUpModal").load(n.target,(function(){e.colorbox({html:n.wrapper,href:n.target}),e("#mzSignUpModal").colorbox()}))})),e(document).on("submit",'form[id="mzLogIn"]',(function(t){t.preventDefault();var s=e(this),i=(s.serializeArray(),{});e.each(e("form").serializeArray(),(function(){i[this.name]=this.value})),e.ajax({dataType:"json",url:mz_mindbody_schedule.ajaxurl,type:s.attr("method"),context:this,data:{action:"mz_client_log_in",form:s.serialize(),nonce:i.nonce,classID:i.classID,staffName:i.staffName,classTime:i.classTime,location:i.location},beforeSend:function(){n.action="processing",o()},success:function(t){e(this).serializeArray();var s={};e.each(e("form").serializeArray(),(function(){s[this.name]=this.value})),"success"==t.type?(n.logged_in=!0,n.action="login",n.message=t.message,a()):(n.action="login_failed",n.message=t.message,o())}}).fail((function(e){n.message="ERROR SIGNING IN",o(),console.log(e)}))})),e(document).on("click","#MBOLogout",(function(t){t.preventDefault();var a=e(this).attr("data-nonce");e.ajax({dataType:"json",url:mz_mindbody_schedule.ajaxurl,data:{action:"mz_client_log_out",nonce:a},beforeSend:function(){n.action="processing",o()},success:function(e){"success"==e.type?(n.logged_in=!1,n.action="logout",n.message=e.message,o()):(n.action="logout_failed",n.message=e.message,o())}}).fail((function(e){n.message="ERROR LOGGING OUT",o(),console.log(e)}))})),e(document).on("click","a#createMBOAccount",(function(t){t.preventDefault(),e(this).attr("href");var a=e(this).attr("data-nonce"),s=e(this).attr("data-classID");e.ajax({type:"GET",dataType:"json",url:mz_mindbody_schedule.ajaxurl,data:{action:"mz_generate_signup_form",nonce:a,classID:s},beforeSend:function(){n.action="processing",o()},success:function(e){"success"==e.type?(n.logged_in=!0,n.action="sign_up_form",n.message=e.message,o()):(n.action="error",n.message=e.message,o())}}).fail((function(e){n.message="ERROR GENERATING THE SIGN-UP FORM",o(),console.log(e)}))})),e(document).on("submit",'form[id="mzSignUp"]',(function(t){t.preventDefault(),e(this).attr("href");var a=e(this),s=(e(this).attr("data-nonce"),e(this).attr("data-classID"),a.serializeArray());e.ajax({type:"GET",dataType:"json",url:mz_mindbody_schedule.ajaxurl,data:{action:"mz_create_mbo_account",nonce:s.nonce,classID:s.classID,form:a.serialize()},beforeSend:function(){n.action="processing",o()},success:function(e){"success"==e.type?(n.logged_in=!0,n.action="login",n.message=e.message,o()):(n.action="error",n.message=e.message,o())}}).fail((function(e){n.message="ERROR CREATING ACCOUNT",o(),console.log(e)}))})),e(document).on("click","#signUpForClass",(function(t){t.preventDefault();var a=e(this).attr("data-nonce");e.ajax({type:"GET",dataType:"json",url:mz_mindbody_schedule.ajaxurl,context:this,data:{action:"mz_register_for_class",nonce:a,siteID:n.siteID,classID:n.classID,location:n.location},beforeSend:function(){n.action="processing",o()},success:function(e){"success"==e.type?(n.action="register",n.message=e.message,o()):(n.action="error",n.message="ERROR REGISTERING FOR CLASS. "+e.message,o())}}).fail((function(e){n.message="ERROR REGISTERING FOR CLASS",o(),console.log(e)}))})),e(document).on("click","a#MBOSchedule",(function(t){t.preventDefault(),e.ajax({type:"GET",dataType:"json",url:mz_mindbody_schedule.ajaxurl,data:{action:"mz_display_client_schedule",nonce:"mz_display_client_schedule",location:n.location,siteID:n.siteID},beforeSend:function(){n.action="processing",o()},success:function(e){"success"==e.type?(n.action="display_schedule",n.message=e.message,o()):(n.action="error",n.message="ERROR RETRIEVING YOUR SCHEDULE. "+e.message,o())}}).fail((function(e){n.message="ERROR RETRIEVING YOUR SCHEDULE",o(),console.log(e)}))}))}))}});
//# sourceMappingURL=signup-modals.js.map