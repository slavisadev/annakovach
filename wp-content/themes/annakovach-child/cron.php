<?php


include_once 'api/cronEmail.php';

$sendMail = new SendCronMail();

$number_of_user = ORM::for_table('wp_user_details')->where("order_status","success")->find_many();

for($i=0; $i<count($number_of_user);$i++)
{

    $sendMail->countUser($number_of_user[$i]['user_id']);

}

