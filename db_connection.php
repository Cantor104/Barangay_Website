<?php
$host = "localhost";
$user = "root";     
$pass = "";         
$db   = "barangay_bims";  

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Set charset
mysqli_set_charset($conn, "utf8");
?>
