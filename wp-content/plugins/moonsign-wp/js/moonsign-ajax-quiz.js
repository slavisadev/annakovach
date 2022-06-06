// jQuery(document).ready(function ($) {

//   let answers = [];

//   $(document).on('click', '.moonsign-quiz-single-answer', function () {

//     answers.push({
//       answer: $(this).find('em').text().trim(),
//       points: $(this).find('.moonsign-quiz-points').val(),
//       tags: $(this).find('.moonsign-quiz-tags').val(),
//     });

//     $(this).closest('.moonsign-quiz-options-holder').hide();
//     $(this).closest('.moonsign-quiz-options-holder').next().show();

//   });

//   $(document).on('mouseover', '.left-side', function () {
//     $('#aweberError').fadeOut();
//     $('#aweberError').remove();
//   });

//   $(document).on('click', '.aweber_subscribe', function () {

//     $(this).attr('disabled', 'disabled')

//     let results_page = $('#results_page').val();

//     let objectToSend = {
//       answers: answers,
//       aweber_list: $('#aweber_list').val(),
//       aweber_name: $('#aweber_name').val(),
//       aweber_email: $('#aweber_email').val(),
//       action: 'moonsign_aweber_subscribe'
//     };

//     $.ajax({
//       type: 'POST',
//       dataType: 'json',
//       url: moonsign_ajax_obj.ajaxurl,
//       data: objectToSend,
//       success: function (data) {
//         if(data.message === 'Subscriber already subscribed') {
//           $('.aweber_subscribe').parent().append('<div id="aweberError">There has been an error. You might be already subscribed.</div>');
//           $('.aweber_subscribe').removeAttr('disabled')
//         } else {
//           window.location.href = results_page;
//         }
//       },
//       error: function (errorThrown) {
//         console.log(errorThrown);
//         $('.aweber_subscribe').parent().append('<div id="aweberError">There has been an error. You might be already subscribed.</div>');
//       }
//     });

//   });

// });