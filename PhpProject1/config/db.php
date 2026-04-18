<?php

$host = "localhost";
$user = "root";
$password = "root";
$database = "it329_recipes-9";
$port = 8889;

$conn = mysqli_connect($host, $user, $password, $database, $port);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

?>