function maximumNumberForAGivenMonth(month, year) {
  return new Date(year, month, 0).getDate();
}

jQuery(document).ready(function ($) {

  $.ajax({
    url: moonsign_ajax_obj.ajaxurl, // or example_ajax_obj.ajaxurl if using on frontend
    data: {
      'action': 'moonsign_ajax_request'
    },
    success: function (data) {
      // This outputs the result of the ajax request
      console.log(data);
    },
    error: function (errorThrown) {
      console.log(errorThrown);
    }
  });

  $(document).on('click', '.moonsign-submit', function () {

    let value_d = $('[name="moonsign-d"]');
    let value_m = $('[name="moonsign-m"]');
    let value_y = $('[name="moonsign-y"]');
    let value_h = $('[name="moonsign-h"]');
    let value_i = $('[name="moonsign-i"]');
    let value_zone = $('[name="moonsign-zone"]');

    if (
      value_d.val() === null ||
      value_m.val() === null ||
      value_y.val() === null ||
      value_h.val() === 'NA' ||
      value_i.val() === 'NA' ||
      value_zone.val() === 'NA'
    ) {
      $('.warning-moonsign').remove();
      $('.col-auto-full').prepend('<div class="warning-moonsign" style="padding:10px 0 20px;color:red;font-family:arial">Please check that you have filled all the fields and selected timezone.</div>');
      return false;
    }
    $('.warning-moonsign').remove();

    let hourValue = parseInt(value_h.val());
    let dayValue = parseInt(value_d.val());
    let monthValue = parseInt(value_m.val());
    let yearValue = parseInt(value_y.val());
    let zoneValue = parseInt(value_zone.val());

    hourValue = hourValue + zoneValue

    if (hourValue > 24) {
      hourValue = hourValue - 24;
      dayValue++;
      if (dayValue > maximumNumberForAGivenMonth(monthValue, yearValue)) {
        dayValue = 1;
        monthValue++;
        if (monthValue > 12) {
          monthValue = 1;
          yearValue++;
        }
      }
    } else if (hourValue < 0) {
      hourValue = 24 - hourValue;
      dayValue--;
      if (dayValue === 0) {
        dayValue = maximumNumberForAGivenMonth(monthValue, yearValue);
        monthValue--;
        if (monthValue === 0) {
          monthValue = 12;
          yearValue--;
        }
      }
    }

    let objectToSend = {
      day: dayValue,
      month: monthValue,
      year: yearValue,
      hour: hourValue,
      minute: value_i.val(),
    };

    $.ajax({
      async: true,
      type: 'POST',
      dataType: 'json',
      url: moonsign_ajax_obj.moonsign_api_url,
      data: objectToSend,
    }).then(function (data) {
      if (data) {
        var sign = data.sign.toLowerCase();

        if (redirect) {
          window.location = moonsign_ajax_obj.signs[sign];
        } else {
          $.ajax({
            url: moonsign_ajax_obj.ajaxurl, // or example_ajax_obj.ajaxurl if using on frontend
            data: {
              action: 'moonsign_ajax_request',
              sign: sign
            },
            success: function (data) {
              $("form.moonsign-form").append(`<div class="moonsign-content-hidden">${data}</div>`);
              $('.moonsign-content-hidden').fadeIn();
            },
            error: function (errorThrown) {
              console.log(errorThrown);
            }
          });
        }
      }
    });

  });

});