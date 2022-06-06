/*! Thrive Leads - The ultimate Lead Capture solution for wordpress - 2022-01-25
* https://thrivethemes.com 
* Copyright (c) 2022 * Thrive Themes */

var ThriveLeads=ThriveLeads||{};ThriveLeads.objects={},ThriveLeads.const=ThriveLeadsConst,ThriveLeads.ajaxModal=function(a){if(!a.view&&!a.onLoad)throw new Error("missing view constructor or onLoad property");TVE_Dash.showLoader();var b=_.extend({type:"get"},a);jQuery.ajax(b).done(function(b){if("function"==typeof a.onLoad)return a.onLoad.call(null);var c=TVE_Dash._instantiate(a.view,a);c.template=_.template(b),c.render().open(a)}).always(function(){TVE_Dash.hideLoader()})},ThriveLeads.resize_thickbox=function(){var a,b=90*jQuery(window).height()/100,c=jQuery("#TB_window"),d=c.find("#TB_ajaxContent"),e=40;d.children().each(function(){var a=jQuery(this);if(a.is("script")||a.is("style")||!a.is(":visible"))return!0;e+=a.outerHeight(!0)}),e=Math.min(e,b-100),a=e+100,d.css("max-height",b-100+"px").animate({height:e},200),c.animate({top:"50%",marginTop:-a/2,height:e+100},200)},ThriveLeads.roundNumber=function(a,b){var c=Math.pow(10,b);return Math.round(a*c)/c},ThriveLeads.conversion_rate=function(a,b,c){if(a=parseInt(a),b=parseInt(b),!a||isNaN(a)||!b||isNaN(b))return"N/A";c=void 0===c?"%":"";var d=ThriveLeads.roundNumber(b/a*100,3).toFixed(2);return d+(c&&!isNaN(d)?" "+c:"")},ThriveLeads.addMessage=function(a,b){ThriveLeads.objects.messages.add(a),b&&b.call(null)},ThriveLeads.addErrorMessage=function(a,b){var c=new ThriveLeads.models.Message({status:"error",text:a});return ThriveLeads.addMessage(c,b)},ThriveLeads.addSuccessMessage=function(a,b){var c=new ThriveLeads.model.Message({status:"success",text:a});return ThriveLeads.addMessage(c,b)},ThriveLeads.addFailCallback=function(a,b){a.fail(function(c){ThriveLeads.addErrorMessage(c.responseText,ThriveLeads.displayMessages),TVE_Dash.hideLoader(),a.failed=!0,void 0!==b&&b.close&&b.close()})},ThriveLeads.displayMessages=function(){ThriveLeads.objects.messages.each(function(a){TVE_Dash["success"===a.get("status")?"success":"err"](a.get("text"))}),ThriveLeads.objects.messages.reset()},ThriveLeads.errorHandler=function(a,b,c,d){ThriveLeads.addMessage({text:c.responseText,status:"error"}),ThriveLeads.router.navigate(a,{trigger:!0})},ThriveLeads.bindZClip=function(a){TVE_Dash.bindZClip(a)},ThriveLeads.ajaxurl=function(a){return a&&a.length?(a=a.replace(/^(\?|&)/,""),ajaxurl+(-1!==ajaxurl.indexOf("?")?"&":"?")+a):ajaxurl},ThriveLeads.validateInputField=function(a){a.removeClass("tvd-invalid"),""==a.val()&&a.addClass("tvd-invalid")};