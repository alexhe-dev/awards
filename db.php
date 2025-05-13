<?php

// Enable error output
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Connect to MySQL
$servername = "localhost";
$username = "root";
$password = "Alamusi1218";
$dbname = "GP25";

$conn = new mysqli($servername, $username, $password, $dbname);

if($conn->connect_error){
    die("Connection failed: " . $conn->connect_error);
}
