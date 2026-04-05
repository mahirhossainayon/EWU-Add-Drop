<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "ewu_add_drop";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>