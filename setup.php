<?php
// Define database connection variables
$host = "localhost";
$user = "root";
$password = "";
$dbname = "recipe_app";

// Create a new MySQLi object and connect to the database
$conn = new mysqli($host, $user, $password);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create the database if it doesn't already exist
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === FALSE) {
    die("Error creating database: " . $conn->error);
}

// Select the database
$conn->select_db($dbname);

// Create the "recipes" table
$sql = "CREATE TABLE IF NOT EXISTS recipes (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    instructions TEXT NOT NULL
)";
if ($conn->query($sql) === FALSE) {
    die("Error creating table: " . $conn->error);
}

// Create the "ingredients" table
$sql = "CREATE TABLE IF NOT EXISTS ingredients (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
)";
if ($conn->query($sql) === FALSE) {
    die("Error creating table: " . $conn->error);
}

// Create the "recipe_ingredients" table
$sql = "CREATE TABLE IF NOT EXISTS recipe_ingredients (
    recipe_id INT(11) UNSIGNED,
    ingredient_id INT(11) UNSIGNED,
    FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE,
    FOREIGN KEY (ingredient_id) REFERENCES ingredients(id) ON DELETE CASCADE
)";
if ($conn->query($sql) === FALSE) {
    die("Error creating table: " . $conn->error);
}

// Close the database connection
$conn->close();

echo "Database setup complete!";
?>
