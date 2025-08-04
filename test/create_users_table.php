<?php
// Include Database
require_once __DIR__ . '/config/Database.php';

use App\Database;

// Create Database instance
$db = Database::getInstance();

// SQL to create users table
$createUsersTableSQL = "
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    firstname VARCHAR(50) NOT NULL,
    lastname VARCHAR(50) NOT NULL,
    role VARCHAR(20) DEFAULT 'user',
    registered DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_login DATETIME DEFAULT NULL,
    profilepic MEDIUMBLOB DEFAULT NULL
)";

// Execute the SQL
try {
    $db->query($createUsersTableSQL);
    echo "Users table created successfully!<br>";
    echo "<a href='create_test_user.php'>Create Test User</a>";
} catch (Exception $e) {
    echo "Error creating users table: " . $e->getMessage() . "<br>";
}
?>
