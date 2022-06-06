(function ($) {
  var $ = jQuery;
  var ajax_url = globalWpJavascriptObject.ajax_url;
  window.connector = {
    getData: function (methodType, route, dataType, dataBlock, callbackFunction, context, headers) {
      if (route === '') {
        route = ajax_url;
      }
      var reqObject = {
        async: true,
        type: methodType,
        dataType: dataType,
        url: route,
        data: dataBlock,
      };
      if (headers !== null) {
        reqObject.beforeSend = function (request) {
          request.setRequestHeader('Authorization', headers);
        };
      }
      $.ajax(reqObject).then(function (data) {
        if (callbackFunction !== null && context !== null) {
          callbackFunction(context, data);
        }
        return data;
      });
    },
  };
})(jQuery);
jQuery('.datepicker').datetimepicker({
  timepicker: false,
  format: 'd.m.Y',
});
jQuery('.timepicker').datetimepicker({
  datepicker: false,
  format: 'H:i',
});
jQuery('form#special-offer').validate({
  ignore: ':hidden:not(.acceptance)',
  rules: {
    life_year: {
      required: true,
      digits: true,
    },
    select_gender: 'required',
    life_email: {
      required: true,
      email: true,
    },
    her_email: {
      required: true,
      email: true,
    },
    her_name: 'required',
    her_date: 'required',
    her_place: 'required',
    her_time: 'required',
    his_name: 'required',
    his_date: 'required',
    his_place: 'required',
    his_time: 'required',
    info_one: 'required',
    info_two: 'required',
  },
  submitHandler: function (form) {
    var dataToSend = 'action=special_offer&' + jQuery('#special-offer').serialize();

    function callBackFunction(context, response) {
      if (response === 200) {
        window.location = globalWpJavascriptObject.purchase_link;
      }
    }

    window.connector.getData('POST', '', 'json', dataToSend, callBackFunction, '');
  },
});
