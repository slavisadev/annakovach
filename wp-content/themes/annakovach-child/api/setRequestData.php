<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include "orm.php";

if (isset($_POST['isFor']) && $_POST['isFor'] == 'horoscope') {


    $tob = explode(':',$_POST['tob']);
    $data = array(
        "email" => $_POST['email'],
        "name" => $_POST['name'],
        "day" => $_POST['date'],
        "month" => $_POST['month'],
        "year" => $_POST['year'],
        "hour" => $tob[0],
        "hour_12" => $_POST['hour'],
        "min" => $_POST['min'],
        "lat" => $_POST['latitude'],
        "lon" => $_POST['longitude'],
        "tzone" => $_POST['timezone'],
        "meridiem" => $_POST['meridiem'],
        "place" =>$_POST['place'],
        "order_status" => "process",
        "email_status" => "process",
    );

    $userId = saveUserDataInDatabase($data);

    if($userId)
    {
        /*Set Horoscope Request Data*/
        $_SESSION['h_user_id'] = $userId;
        $_SESSION['h_email'] = $_POST['email'];
        $_SESSION['h_name'] = $_POST['name'];
        $_SESSION['h_day'] = $_POST['date'];
        $_SESSION['h_month'] = $_POST['month'];
        $_SESSION['h_year'] = $_POST['year'];
        $_SESSION['h_hour'] = $_POST['hour'];
        $_SESSION['h_minute'] = $_POST['min'];
        $_SESSION['h_tob'] = $_POST['tob'];
        $_SESSION['h_latitude'] = $_POST['latitude'];
        $_SESSION['h_longitude'] = $_POST['longitude'];
        $_SESSION['h_timezone'] = $_POST['timezone'];
        $_SESSION['h_meridiem'] = $_POST['meridiem'];
        $_SESSION['h_place'] = $_POST['place'];

        echo true;
    }
    else
    {
        echo false;
    }



}
elseif (isset($_POST['isFor'])  && $_POST['isFor'] == 'love-reading') {

    $tob = explode(':',$_POST['tob']);
    $data = array(
        "email" => $_POST['email'],
        "name" => $_POST['name'],
        "day" => $_POST['date'],
        "month" => $_POST['month'],
        "year" => $_POST['year'],
        "hour" => $tob[0],
        "hour_12" => $_POST['hour'],
        "min" => $_POST['min'],
        "lat" => $_POST['latitude'],
        "lon" => $_POST['longitude'],
        "tzone" => $_POST['timezone'],
        "meridiem" => $_POST['meridiem'],
        "place" =>$_POST['place'],
        "order_status" => "process",
        "email_status" => "process",
    );

    $userId = saveLoveForecastUserDataInDatabase($data);

    if($userId)
    {
        /*Set Horoscope Request Data*/
        $_SESSION['l_user_id'] = $userId;
        $_SESSION['l_email'] = $_POST['email'];
        $_SESSION['l_name'] = $_POST['name'];
        $_SESSION['l_day'] = $_POST['date'];
        $_SESSION['l_month'] = $_POST['month'];
        $_SESSION['l_year'] = $_POST['year'];
        $_SESSION['l_hour'] = $_POST['hour'];
        $_SESSION['l_minute'] = $_POST['min'];
        $_SESSION['l_tob'] = $_POST['tob'];
        $_SESSION['l_latitude'] = $_POST['latitude'];
        $_SESSION['l_longitude'] = $_POST['longitude'];
        $_SESSION['l_timezone'] = $_POST['timezone'];
        $_SESSION['l_meridiem'] = $_POST['meridiem'];
        $_SESSION['l_place'] = $_POST['place'];

        echo true;
    }
    else
    {
        echo false;
    }



} else {
    echo false;
}


function saveUserDataInDatabase(array $data)
{

    $userEntityRow = ORM::for_table('wp_user_details')->create();
    $userEntityRow->set($data);
    $userEntityRow->save();
    return $userEntityRow->id();

}


function saveLoveForecastUserDataInDatabase(array $data)
{

    $userEntityRow = ORM::for_table('wp_love_forecast_user_details')->create();
    $userEntityRow->set($data);
    $userEntityRow->save();
    return $userEntityRow->id();

}