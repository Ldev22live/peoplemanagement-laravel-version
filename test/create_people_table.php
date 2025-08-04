<?php
require_once __DIR__ . '/config/Database.php';

use App\Database;

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    // Create languages table if it doesn't exist
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS languages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(50) NOT NULL UNIQUE
        )
    ");
    
    // Insert some common languages
    $languages = ['English', 'Afrikaans', 'Zulu', 'Xhosa', 'Sotho', 'Tswana', 'Venda', 'Tsonga', 'Swati', 'Ndebele'];
    
    foreach ($languages as $language) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO languages (name) VALUES (:name)");
        $stmt->execute(['name' => $language]);
    }
    
    // Create interests table if it doesn't exist
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS interests (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(50) NOT NULL UNIQUE
        )
    ");
    
    // Insert some common interests
    $interests = ['Sports', 'Music', 'Art', 'Reading', 'Travel', 'Technology', 'Cooking', 'Gaming', 'Movies', 'Photography'];
    
    foreach ($interests as $interest) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO interests (name) VALUES (:name)");
        $stmt->execute(['name' => $interest]);
    }
    
    // Create people table if it doesn't exist
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS people (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            surname VARCHAR(100) NOT NULL,
            id_number VARCHAR(13) NOT NULL UNIQUE,
            mobile_number VARCHAR(20) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            birth_date DATE NOT NULL,
            language_id INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (language_id) REFERENCES languages(id)
        )
    ");
    
    // Create people_interests junction table for many-to-many relationship
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS people_interests (
            person_id INT,
            interest_id INT,
            PRIMARY KEY (person_id, interest_id),
            FOREIGN KEY (person_id) REFERENCES people(id) ON DELETE CASCADE,
            FOREIGN KEY (interest_id) REFERENCES interests(id) ON DELETE CASCADE
        )
    ");
    
    echo "Tables created successfully!";
} catch (PDOException $e) {
    die("Error creating tables: " . $e->getMessage());
}
