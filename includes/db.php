<?php

$host = 'localhost';
$dbname = 'recipe_app';
$username = 'recipe_app';
$password = 'password';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);



    require_once('includes/db.php');

    try {
        $conn->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(255) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            name VARCHAR(255) NOT NULL,
            age INT(11) UNSIGNED NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            profile_pic VARCHAR(65535)
            
        );
    ");

        $conn->exec("
        CREATE TABLE IF NOT EXISTS recipes (
            id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id INT(11) UNSIGNED NOT NULL,
            name VARCHAR(255) NOT NULL,
            description TEXT NOT NULL,
            instructions TEXT NOT NULL,
            image VARCHAR(255),
            video VARCHAR(255),
            ingredients VARCHAR(255) NOT NULL,
            rating INT(11) UNSIGNED,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id)
        );
    ");

        $conn->exec("
        CREATE TABLE IF NOT EXISTS ingredients (
            id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL UNIQUE
        );
    ");

        $conn->exec("
        CREATE TABLE IF NOT EXISTS recipe_ingredients (
            recipe_id INT(11) UNSIGNED NOT NULL,
            ingredient_id INT(11) UNSIGNED NOT NULL,
            FOREIGN KEY (recipe_id) REFERENCES recipes(id),
            FOREIGN KEY (ingredient_id) REFERENCES ingredients(id)
        );
    ");
    } catch (PDOException $e) {
        echo "Error creating tables: " . $e->getMessage();
        die();
    }
} catch (PDOException $e) {
    // echo "Connection failed: " . $e->getMessage();
    die();
}
