<?php
// Define database connection variables
$host = "localhost";
$user = "username";
$password = "password";
$dbname = "database_name";

// Create a new MySQLi object and connect to the database
$conn = new mysqli($host, $user, $password, $dbname);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
