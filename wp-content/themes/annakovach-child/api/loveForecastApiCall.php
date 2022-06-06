<?php
//Start the session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'VedicRishiClient.php';
include 'orm.php';

$userId = "601280";
$apiKey = "afb1a30b63587b048436ab04053d4dce";

$vedicRishi = new VedicRishiClient($userId,$apiKey,'en');

if(isset($_SESSION['l_user_id']) && $_SESSION['l_user_id'] !='')
{

    $resourceName = $_POST['api_name'];

    $row = ORM::for_table('wp_love_forecast_user_details')->where('user_id',$_SESSION['l_user_id'])->find_one();

    if($row)
    {

        $responseData = $vedicRishi->call($resourceName,$row->name, $row->day, $row->month, $row->year, $row->hour, $row->min, $row->lat, $row->lon, $row->tzone);

        echo json_encode(array("status"=>true,"data"=>$responseData));

    }
    else
    {
        echo json_encode(array("status"=>false,"error"=>"Data not found!"));
    }


}
else
{
    echo json_encode(array("status"=>false,"error"=>"Incorrect request data.Please check your request data!"));
}
