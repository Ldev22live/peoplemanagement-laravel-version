<?php
// Include Database and UserRepository
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/config/Repository/PDO/UserRepository.php';

// Import required classes
use App\Repository\PDO\UserRepository;

// Create UserRepository instance
$userRepository = new UserRepository();

// Check if the test user already exists
$existingUser = $userRepository->findOneBy(['username' => 'test']);

if ($existingUser) {
    echo "Test user already exists!<br>";
    echo "Username: test<br>";
    echo "Password: test<br>";
} else {
    // Create test user data
    $userData = [
        'username' => 'test',
        'password' => password_hash('test', PASSWORD_DEFAULT),
        'email' => 'test@example.com',
        'firstname' => 'Test',
        'lastname' => 'User',
        'role' => 'admin' // Give admin role to access all features
    ];

    // Save the user to the database
    $userId = $userRepository->save($userData);

    if ($userId) {
        echo "Test user created successfully!<br>";
        echo "Username: test<br>";
        echo "Password: test<br>";
        echo "User ID: " . $userId . "<br>";
    } else {
        echo "Failed to create test user.<br>";
    }
}

echo "<br><a href='login.php'>Go to Login Page</a>";
?>
