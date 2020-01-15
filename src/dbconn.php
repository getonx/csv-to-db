<?php

$db_host = '';
$db_user = '';
$db_pass = '';
$db_name = '';
$db_conn = new PDO("mysql:host={$db_host};dbname={$db_name}", $db_user, $db_pass, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
$db_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

?>