+function(t){"use strict";function e(e){return this.each(function(){var s=t(this),o=s.data("bs.button"),i="object"==typeof e&&e;o||s.data("bs.button",o=new n(this,i)),"toggle"==e?o.toggle():e&&o.setState(e)})}var n=function(e,s){this.$element=t(e),this.options=t.extend({},n.DEFAULTS,s),this.isLoading=!1};n.VERSION="3.3.7",n.DEFAULTS={loadingText:"loading..."},n.prototype.setState=function(e){var n="disabled",s=this.$element,o=s.is("input")?"val":"html",i=s.data();e+="Text",null==i.resetText&&s.data("resetText",s[o]()),setTimeout(t.proxy(function(){s[o](null==i[e]?this.options[e]:i[e]),"loadingText"==e?(this.isLoading=!0,s.addClass(n).attr(n,n).prop(n,!0)):this.isLoading&&(this.isLoading=!1,s.removeClass(n).removeAttr(n).prop(n,!1))},this),0)},n.prototype.toggle=function(){var t=!0,e=this.$element.closest('[data-toggle="buttons"]');if(e.length){var n=this.$element.find("input");"radio"==n.prop("type")?(n.prop("checked")&&(t=!1),e.find(".active").removeClass("active"),this.$element.addClass("active")):"checkbox"==n.prop("type")&&(n.prop("checked")!==this.$element.hasClass("active")&&(t=!1),this.$element.toggleClass("active")),n.prop("checked",this.$element.hasClass("active")),t&&n.trigger("change")}else this.$element.attr("aria-pressed",!this.$element.hasClass("active")),this.$element.toggleClass("active")};var s=t.fn.button;t.fn.button=e,t.fn.button.Constructor=n,t.fn.button.noConflict=function(){return t.fn.button=s,this},t(document).on("click.bs.button.data-api",'[data-toggle^="button"]',function(n){var s=t(n.target).closest(".btn");e.call(s,"toggle"),t(n.target).is('input[type="radio"], input[type="checkbox"]')||(n.preventDefault(),s.is("input,button")?s.trigger("focus"):s.find("input:visible,button:visible").first().trigger("focus"))}).on("focus.bs.button.data-api blur.bs.button.data-api",'[data-toggle^="button"]',function(e){t(e.target).closest(".btn").toggleClass("focus",/^focus(in)?$/.test(e.type))})}(jQuery),function(t){"use strict";t(document).ready(function(t){var e=mz_mindbody_schedule.nonce;mz_mindbody_schedule.atts,t("#mzClearTransients");t("#mzClearTransients").on("click",function(n){n.preventDefault(),t.ajax({type:"post",dataType:"json",context:this,url:mz_mindbody_schedule.ajaxurl,data:{action:"mz_mbo_clear_transients",nonce:e},success:function(t){"success"==t.type?alert("Transients cleared."):alert("Something went wrong.")}}).fail(function(t){console.log("fail"),console.log(t),alert("Something went wrong.")})})})}(jQuery);
//# sourceMappingURL=admin.js.map
