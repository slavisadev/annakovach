jQuery('.datepicker').datetimepicker({
    timepicker: false,
    format: 'd.m.Y',
    yearRange: '1920:2018'
});

jQuery('.timepicker').datetimepicker({
    datepicker: false,
    format: 'H:i'
});

jQuery('form#special-offer').validate({
    ignore: ':hidden:not(.acceptance)',
    rules: {
        life_year: {
            required: true,
            digits: true
        },
        select_gender: 'required',
        life_email: {
            required: true,
            email: true
        },
        her_email: {
            required: true,
            email: true
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
        info_two: 'required'
    },
    submitHandler: function (form) {
        var dataToSend = 'action=special_offer&' + jQuery('#special-offer').serialize();

        function callBackFunction(context, response) {
            if (response === 200) {
                window.location = 'http://1.annakovach.pay.clickbank.net/';
            }
        }

        window.connector.getData('POST', '', 'json', dataToSend, callBackFunction, '');
    }
});