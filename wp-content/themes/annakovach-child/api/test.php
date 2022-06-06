<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once 'cronEmail.php';

///$row = ORM::for_table('wp_user_details')->where('user_id', 1)->find_one();

$sendMail = new SendCronMail();

$sendMail->countUser(1);