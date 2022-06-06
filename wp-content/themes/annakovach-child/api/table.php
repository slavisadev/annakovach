<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$conn = mysqli_connect('localhost', 'root', 'asdISUD76dd'); // MAMP defaults
mysqli_select_db($conn, 'httpsann');

$table = "CREATE TABLE `wp_love_forecast_user_details` (
  `user_id` int(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `day` varchar(10) NOT NULL,
  `month` varchar(10) NOT NULL,
  `year` varchar(10) NOT NULL,
  `hour` varchar(10) NOT NULL,
  `hour_12` varchar(10) NOT NULL,
  `min` varchar(10) NOT NULL,
  `lat` varchar(30) NOT NULL,
  `lon` varchar(30) NOT NULL,
  `tzone` varchar(10) NOT NULL,
  `meridiem` varchar(10) NOT NULL,
  `place` varchar(200) NOT NULL,
  `order_status` varchar(20) NOT NULL,
  `email_status` varchar(20) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;";


$table1 = "DROP TABLE wp_love_forecast_user_details";

$table2 = "ALTER TABLE `wp_love_forecast_user_details`
  ADD PRIMARY KEY (`user_id`)";

$table3 = "ALTER TABLE `wp_love_forecast_user_details`
  MODIFY `user_id` int(50) NOT NULL AUTO_INCREMENT";

if (mysqli_query($conn, $table3)) {

    echo "success";

} else {

    echo "ERROR: Could not able to execute. " . mysqli_error($conn);

}