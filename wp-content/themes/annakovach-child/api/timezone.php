<?php

include_once "VedicRishiClient.php";

$API = new VedicRishiClient("601280","afb1a30b63587b048436ab04053d4dce");

if(isset($_POST['date']) && isset($_POST['latitude']) && isset($_POST['longitude']))
{
    $data = array(
        "date"=>  $_POST['date'],
        "latitude"=>$_POST['latitude'],
        "longitude"=>$_POST['longitude']
    );
    $response = $API->timezone($data,'timezone_with_dst');

    echo ($response);
}
else
{
    echo json_encode(array("status"=>false,"response"=>"Some error occurred!!"));
}

