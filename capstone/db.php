<?php
$host = "localhost";
$user = "root";  // Change if using an actual database server
$pass = "";
$dbname = "loan_management";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
