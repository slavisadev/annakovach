jQuery(document).ready(function ($) {

    function zeroPad(num, places) {
        let zero = places - num.toString().length + 1;
        return Array(+(zero > 0 && zero)).join("0") + num;
    }

    $(document).on('change','input[name="isFreeChartChecked"]',function() {
        if(!this.checked) {
            //Do stuff
            jQuery('.custom-btn-pink').removeAttr('id');
            jQuery('.custom-btn-pink').attr('disabled','disabled');
            jQuery('.custom-btn-pink').addClass('disabled-btn');
        }
        else {
            jQuery('.custom-btn-pink').attr('id','submitHoroscopeForm');
            jQuery('.custom-btn-pink').removeAttr('disabled');
            jQuery('.custom-btn-pink').removeClass('disabled-btn');
        }
    });




    let today = new Date();

    //dob
    defaultDob(1, 31, today.getDate(), document.getElementById('birthDay'));
    defaultDob(1, 12, today.getMonth() + 1, document.getElementById('birthMonth'),true);
    defaultDob(1920, today.getFullYear(), today.getFullYear(), document.getElementById('birthYear'));

    //tob
    defaultDob(1, 12, today.getHours(), document.getElementById('birthHour'));
    defaultDob(0, 59, today.getMinutes(), document.getElementById('birthMinute'));

    let validateEmail = function($email) {
        let emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
        if( !emailReg.test( $email ) ) {
            return false;
        } else {
            return true;
        }
    };

    function defaultDob(from, to, check, id,isForonth) {

        removeOptions(id);
        let mnth = ['January','February','March','April','May','June','July','August','September','October','November','December'];
        for (let i = from; i <= to; i++) {
            let opt = document.createElement('option');
            opt.value = zeroPad(i, 2);
            opt.innerHTML = isForonth?mnth[i-1]:zeroPad(i, 2);
            if (i === check) {
                opt.selected = true;
            }

            id.appendChild(opt);
        }
    }

    function removeOptions(selectbox)
    {
        var i;
        for(i = selectbox.options.length - 1 ; i >= 0 ; i--)
        {
            selectbox.remove(i);
        }
    }



    function validateHoroscopeForm() {

        let date = jQuery("#birthDay").val(),
            name = jQuery("#birthName").val(),
            email = jQuery("#emailId").val(),
            month = jQuery("#birthMonth").val(),
            year = jQuery("#birthYear").val(),
            hour = jQuery("#birthHour").val(),
            minute = jQuery("#birthMinute").val(),
            meridiem = jQuery("#meridianOfBirth").val(),
            tzone = jQuery("#timezone").val(),
            lat = jQuery("#latitude").val(),
            lon = jQuery("#longitude").val(),
            place = jQuery("#place").val(),
            isFor = jQuery("#isFor").val();

        if (name === '') {
            return {status: false, message: "Please enter valid name."};
        }
        if (email === '' || !validateEmail(email)) {
            return {status: false, message: "Please enter valid email."};
        }
        else if (tzone === '' || lat === '' || lon === '') {
            return {status: false, message: "Please enter valid birth place."};
        }
        else {
            let hh, tob;

            hour = Number(hour);
            if (meridiem === "PM" && hour < 12) hh = parseInt(hour) + 12;
            if (meridiem === "AM" && hour === 12) hh = parseInt(hour) - 12;
            if (meridiem === "PM" && hour === 12) hh = Number(hour);
            if (meridiem === "AM" && hour < 12) hh = Number(hour);

            tob = hh + ":" + minute;


            isFor = isFor !=='' || typeof isFor !=='undefined' || isFor ==='true' ? isFor:'horoscope';

            return {
                isFor: isFor,
                status: true,
                email: email,
                name: name,
                date: date,
                month: month,
                year: year,
                tob: tob,
                hour: hour,
                min: minute,
                timezone: tzone,
                latitude: lat,
                longitude: lon,
                place: place,
                meridiem: meridiem
            };
        }
    }


    jQuery("#submitHoroscopeForm").click(function (event) {

        jQuery("#horoscopeFormError").fadeOut(100);
        event.preventDefault();
        let response = validateHoroscopeForm();

        jQuery(".spinner-container1").fadeIn();

        //console.log(JSON.stringify(response));
        if(response.status)
        {
            //alert(JSON.stringify(response));
            let res = $.ajax({
                url: "/wp-content/themes/annakovach-child/api/setRequestData.php",
                method: "POST",
                dataType: "json",
                data:(response)
            });

            res.then(function(a) {

                if (response.isFor !== 'horoscope') {
                    localStorage.setItem('LoveForecastFormData', JSON.stringify(response));
                }
                else
                {
                    localStorage.setItem('HoroscopeFormData', JSON.stringify(response));
                }

                $("#horoscopeForm").submit();

            }, function(a) {
                jQuery(".spinner-container1").fadeOut(100);
                $("#numerologyFormError").html('Some Error Occluded Please Try Again Later.').fadeIn(200);
            });
        }
        else {
            jQuery(".spinner-container1").fadeOut(100);
            jQuery("#horoscopeFormError").fadeIn(200).html(response.message);
        }

    });

    google.maps.event.addDomListener(window, 'load', function () {

        function eventListenerForMaps(domId, latId, longId, tzone) {
            let places = new google.maps.places.Autocomplete(document.getElementById(domId));

            google.maps.event.addListener(places, 'place_changed', function () {


                let dt = jQuery("#birthDay").val();
                let mt = jQuery("#birthMonth").val();
                let yy = jQuery("#birthYear").val();

                let place = places.getPlace(),
                    timestamp = new Date("" + mt + "/" + dt + "/" + yy),
                    tmsp = Math.floor(timestamp.getTime() / 1000),
                    latitude = place.geometry.location.lat(),
                    longitude = place.geometry.location.lng(),
                    country = place.formatted_address,
                    timezone = 5.5;

                var dataToBeSent = {
                    latitude: latitude,
                    longitude: longitude,
                    date: mt + "-" + dt + "-" + yy
                };

                jQuery.post("/wp-content/themes/annakovach-child/api/timezone.php", dataToBeSent, function (data, textStatus) {
                    //jQuery('#submit-hr-form').prop('disabled', false);
                    //jQuery('#timezoneSpinner').fadeOut();
                    timezone = data.timezone;
                    jQuery(tzone).val(timezone);
                }, "json");

                jQuery(latId).val(latitude);
                jQuery(longId).val(longitude);

            });
        }

        jQuery('#place').on('keyup', function () {
            eventListenerForMaps('place', '#latitude', '#longitude', '#timezone');
        });

    });


});
