<?php
$host = "localhost";
$user = "root";
$password = "";
$db = "account";

// Create a connection to the database
$conn = mysqli_connect($host, $user, $password, $db);
if (!$conn) { // Corrected the check to !$conn
    die("Connection error: " . mysqli_connect_error());
}
?>
