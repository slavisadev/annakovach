<?php
/**
 *  @author Chandan Tiwari
 *  @copyright Vedic Rishi Astro Pvt Ltd
 *
 * ORM configuration file for MySql database
 *
 */

require 'idiorm.php';


// For localhost
ORM::configure('mysql:host=localhost;dbname=httpsann');

ORM::configure('username', 'root');

ORM::configure('password', 'asdISUD76dd');


ORM::configure('return_result_sets', true); // returns result sets

// set the primary key id on per table basis
//ORM::configure('id_column', 'user_id');
ORM::configure('id_column_overrides', array(

   "wp_user_details"=>"user_id"

));
?>