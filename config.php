<?php
$host = "localhost";
$user = "root"; // default XAMPP username
$password = ""; // default XAMPP password is empty
$database = "your_database_name"; // replace with your DB name

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// echo "Connected successfully"; // optional for testing
?>