<?php
/*
Config your database connection in 'dbconn.php', put your csv file to '/data/name_of_your_table' folder, update the '$folder' variable in this file and run.
Program will automatically create and populate a database table according to the *.csv file. Program will remove utf8 BOM and replace most usual special characters
in columns names with an underscore ('_') or nothing(''). If I missed any special characters worth replacing, please look at 'array_to_db_table' function.
*/


require './src/dbconn.php';
require './src/engine.php';		

$folder = 'rselectrical_rolec';
wrapper($folder, $db_conn);

?>