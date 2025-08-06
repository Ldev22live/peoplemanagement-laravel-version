-- Create database for people management system
CREATE DATABASE IF NOT EXISTS people_management;

-- Create user and grant privileges
CREATE USER IF NOT EXISTS 'peopleuser'@'localhost' IDENTIFIED BY 'securepassword';
GRANT ALL PRIVILEGES ON people_management.* TO 'peopleuser'@'localhost';
FLUSH PRIVILEGES;

-- Use the people_management database
USE people_management;

-- Create languages table
CREATE TABLE IF NOT EXISTS languages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
);

-- Insert common languages
INSERT INTO languages (name) VALUES 
('English'),
('Afrikaans'),
('Zulu'),
('Xhosa'),
('Sotho'),
('Tswana'),
('Venda'),
('Tsonga'),
('Swati'),
('Ndebele');

-- Create interests table
CREATE TABLE IF NOT EXISTS interests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
);

-- Insert common interests
INSERT INTO interests (name) VALUES 
('Sports'),
('Music'),
('Art'),
('Reading'),
('Travel'),
('Technology'),
('Cooking'),
('Gaming'),
('Movies'),
('Photography');

-- Create people table
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
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (language_id) REFERENCES languages(id)
);

-- Create people_interests junction table for many-to-many relationship
CREATE TABLE IF NOT EXISTS people_interests (
    person_id INT,
    interest_id INT,
    PRIMARY KEY (person_id, interest_id),
    FOREIGN KEY (person_id) REFERENCES people(id) ON DELETE CASCADE,
    FOREIGN KEY (interest_id) REFERENCES interests(id) ON DELETE CASCADE
);

INSERT INTO users (username, password, email, firstname, lastname, role, created_at, updated_at)
VALUES (
    'test',
    '$2y$10$wHnZrHxFzXnM9qOvZZi6/O7n5xXfMmw6hJvHbFJPG0sKM5uk4ibZq', 
    'test@example.com',
    'Test',
    'User',
    'admin',
    NOW(),
    NOW()
);

