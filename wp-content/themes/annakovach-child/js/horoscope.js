jQuery(document).ready(function (e) {

    //console.log(localStorage.getItem('HoroscopeFormData'));

    //display user info

    let userDetails = JSON.parse(localStorage.getItem('HoroscopeFormData'));
    let monthArr = [ "January", "February", "March",
        "April", "May", "June", "July",
        "August", "September", "October",
        "November", "December"
    ];

    if (userDetails) {
        jQuery('#user-details p').html(
             userDetails.date + " "+ monthArr[userDetails.month-1]+" " + userDetails.year + " &nbsp;&nbsp;"
            + userDetails.hour + ":" + userDetails.min + "" + userDetails.meridiem + "&nbsp;&nbsp;"
            + formatLatitude(userDetails.latitude) + ", " + formatLongitude(userDetails.longitude) + "&nbsp;&nbsp; " + timezoneDeegreeFormat(userDetails.timezone)
        );
        jQuery('#user-heading h3').append('&nbsp;'+userDetails.name);

        getUserDetails(userDetails);

    }
    else {
        window.location = '/free-birth-chart/'
    }


    //common functions

    //format latitude
    function formatLatitude(deg) {
        var str,
            formatDegree;

        if (deg < 0) {
            str = "S";
        }
        else {
            str = "N";
        }

        formatDegree = convertDegMinute(deg, str);

        return formatDegree;

    }

    //format latitude
    function formatLongitude(deg) {
        var str,
            formatDegree;

        if (deg < 0) {
            str = "W";
        }
        else {
            str = "E";
        }

        formatDegree = convertDegMinute(deg, str);

        return formatDegree;
    }

    function timezoneDeegreeFormat($tzone)
    {
        var $hour = parseInt($tzone);
        var $minute = parseInt(($tzone - $hour) * 60);

        if ($hour >= 0) {
            $hour = " &nbsp;+" + zeroPad($hour,2);
        }
        return $hour + ":" + zeroPad($minute,2);

    }


    function convertDegMinute(degree, direction) {
        var deg = zeroPad(parseInt(degree), 2);
        var min = zeroPad(parseInt((degree - deg) * 60), 2);

        return deg + "°" + min + "'" + " " + direction;
    }

    function zeroPad(num, places) {
        var zero = places - num.toString().length + 1;
        return Array(+(zero > 0 && zero)).join('0') + num;
    }


    function getApiData(api, data, showSpinner) {


        if (showSpinner) {
            jQuery(".spinner-container").fadeIn("slow");
        }

        if ((typeof data !== 'object')) {
            data = JSON.parse(data);
        }

        var tob = data.tob.split(':');

        data.day = data.date;
        data.hour = tob[0];
        data.min = tob[1];
        data.lat = data.latitude;
        data.lon = data.longitude;
        data.tzone = data.timezone;
        data.api_name = api;
        data.aspects = "none";


        //alert(JSON.stringify(data));

        var request = jQuery.ajax({

            url: "/wp-content/themes/annakovach-child/api/apiCall.php",
            type: "POST",
            dataType: 'json',
            data: (data)

        });

        return (request.then(function (resp) {
            jQuery(".spinner-container").fadeOut(3000);
            return resp;
        }, function (err) {
            jQuery(".spinner-container").fadeOut(3000);
            //alert(JSON.stringify(err));
        }));

    }

    function getUserDetails(data) {


        getApiData('western_horoscope', data, true).then(function (res) {
            getApiData('wheel_chart/tropical', data, false).then(function (img) {

                getApiData('general_ascendant_report/tropical', data, false).then(function (ascendant) {

                    if(res.status)
                    {
                        let res_cpy = jQuery.extend(true, {}, res);
                        let asc_cpy = jQuery.extend(true, {}, ascendant);
                        res_cpy = JSON.parse(res_cpy.data);
                        asc_cpy = JSON.parse(asc_cpy.data);

                        //console.log((res_cpy));

                        res_cpy['planets'] = makePlanetPositionsData(res_cpy);
                        res_cpy['houses'] = formateCuspsResponse(res_cpy);

                        asc_cpy.report = splitData(asc_cpy.report);

                        //var aspect = formatAspectTable(res_cpy['aspects']);

                        img = JSON.parse(img.data);

                        //console.log(asc_cpy);


                        var chart = img.url;

                        var source = jQuery('#userDetailsTpl').html();
                        var template = Handlebars.compile(source);
                        jQuery('#userDetails').html(template({
                            "natal": {
                                planets: res_cpy['planets'],
                                chart: chart,
                                houses: res_cpy['houses'],
                                aspects_icon: res_cpy['aspects_icon'],
                                aspects: res_cpy['aspects'],
                                ascendant_report : asc_cpy
                            },
                            birth: data,

                        }));
                    }
                    else
                    {
                        localStorage.setItem('HoroscopeFormData','');
                        window.location = '/free-birth-chart/'
                    }

                });

            });
        });


    }

    function formateCuspsResponse(response) {

        let data = jQuery.extend(true, {}, response);
        let res = data['houses'];

        let significance =["House of Self","House of Possessions","House of Communication","House of Home","House of Pleasure","House of Health","House of Partnership","House of Sex","House of Philosophy","House of Social Status","House of Friends","House of the Unconscious"];

        for (let i = 0; i < res.length; i++) {
            res[i].degree = DMS1(fix(30, res[i].degree));

            res[i].house = "House " + res[i].house;
            res[i].significance = significance[i];
        }

        res[0].house = "AC (Ascendant)";
        res[9].house = "MC (Midheaven)";

        return res;
    }

    //Make Planet Positions Data
    function makePlanetPositionsData(response) {

        let res = jQuery.extend(true, {}, response);
        let planets = res.planets;
        let houses = res.houses;

        let predefinedOrder = ['Sun', 'Moon', 'Mercury', 'Venus', 'Mars', 'Jupiter', 'Saturn', 'Uranus', 'Neptune', 'Pluto', 'Part of Fortune', 'Node', 'Chiron', 'Lilith', 'Ascendant'];
        let planetIconArr = ['Sun', 'Moon', 'Mercury', 'Venus', 'Mars', 'Jupiter', 'Saturn', 'Uranus', 'Neptune', 'Pluto', 'Pof', 'Node', 'Chiron', 'Lilith','AC'];


        let ascDetails = {
            "name": "Ascendant",
            "full_degree": houses[0]['degree'],
            "norm_degree": fix(30, houses[0]['degree']),
            "speed": 0,
            "is_retro": "false",
            "sign_id": getSignNumber(houses[0]['degree']) + 1,
            "sign": houses[0]['sign'],
            "house": 1
        };


        planets.push(res['lilith']);

        planets.push(ascDetails);


        var sortedCollection = planets.sort(SortByName);


        for (let j = 0; j < sortedCollection.length; j++) {
            sortedCollection[j].isRetro = (sortedCollection[j].is_retro === 'false' || !sortedCollection[j].is_retro) ? '-' : 'R';
            sortedCollection[j].planet = planetIconArr[j];
            sortedCollection[j].planet_name = predefinedOrder[j];
            sortedCollection[j].sign_name = sortedCollection[j].sign.substring(0, 3);
            sortedCollection[j].degree = DMS1((sortedCollection[j].norm_degree));
            sortedCollection[j].symbol_class = (j === 16 || j === 17) ? true : false;
        }

        return sortedCollection;

    }

    function fix($fixNum, $d) {

        while ($d >= $fixNum) {
            $d -= $fixNum;
        }

        while ($d < 0) {
            $d += $fixNum;
        }

        return $d;
    }


    function DMS1(dec) {

        var $vars = dec.toString().split('.');

        if ($vars[1] === undefined) {
            $vars[1] = '0';
        }

        var $deg = $vars[0],

            $tempma = '0.' + $vars[1];
        $tempma = $tempma * 3600;
        var $min = Math.floor($tempma / 60),
            $sec = Math.round($tempma - ($min * 60));
        //console.log($vars[1]);

        if ($deg < 10) $deg = '0' + $deg;
        if ($min < 10) $min = '0' + $min;
        if ($sec < 10) $sec = '0' + $sec;

        //var resp = $deg+"&nbsp; <i class='icon-planets icon-'" + sign +"></i>"+$min+'"'+ $sec+"'";

        return {deg: $deg, min: $min, sec: $sec};

    }

    function getSignNumber($deg) {
        var num = 0;

        while ($deg > 30.0) {
            $deg -= 30;
            num++;
        }
        if (num === 12) num = 0;
        return num;
    }


    function SortByName(a, b) {
        let predefinedOrder = ['Sun', 'Moon', 'Mercury', 'Venus', 'Mars', 'Jupiter', 'Saturn', 'Uranus', 'Neptune', 'Pluto', 'Part of Fortune', 'Node', 'Chiron', 'Lilith', 'Ascendant'];


        return predefinedOrder.indexOf(a.name) - predefinedOrder.indexOf(b.name);

    }

    function splitData(data) {
        let sentences = data.split('.');
        let sum = 0;
        let lengths = sentences.map(function (v) {
            sum += v.length;
            return v.length
        });
        let n = 0;
        let ts = 0;
        while (ts < sum/2 ) ts += lengths[n++];

        let arr = sentences.filter(function (n) {
            return n != ''
        });

        let html = '<p class="custom-para" >' + arr.slice(0, n - 1).join('.') + '.</p><div class="m-t-30"></div><p class="custom-para" >' + arr.slice(n - 1, sentences.length).join('.') + '</p>';

        return html;
    }

});