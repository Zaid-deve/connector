<?php

$db_server = 'localhost';
$db_name = 'connector';
$db_user = 'root';
$db_pass = '';

$db = "mysql:host=$db_server;dbname=$db_name";
$conn = new PDO($db, $db_user);

if (!$conn) {
    die("Failed To Connect Server [Error:" . mysqli_connect_errno() . "]");
}

?>