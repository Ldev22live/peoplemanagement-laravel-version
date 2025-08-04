<?php
session_start();

// Include Database and UserRepository
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../config/Repository/PDO/UserRepository.php';

// Import required classes
use App\Repository\PDO\UserRepository;

// Get form data
$usernameOrEmail = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

// Validate input
if (empty($usernameOrEmail) || empty($password)) {
    header('Location: /test/login.php?error=empty_fields');
    exit();
}

try {
    // Create UserRepository instance
    $userRepository = new UserRepository();
    
    // Find user by username or email using PDO
    $user = $userRepository->findOneBy(['username' => $usernameOrEmail]) ?? 
            $userRepository->findOneBy(['email' => $usernameOrEmail]);
    
    if ($user) {
        // Check if password is correct
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['userid'] = $user['id']; // Adding userid for compatibility with admin.php
            $_SESSION['first_name'] = $user['firstname'];
            $_SESSION['last_name'] = $user['lastname'];
            $_SESSION['email'] = $user['email'];
            
            // Check if user has admin role and redirect accordingly
            if (isset($user['role']) && $user['role'] === 'admin') {
                $_SESSION['is_admin'] = true;
                header('Location: /test/index.php?login=success');
            } else {
                $_SESSION['is_admin'] = false;
                header('Location: /test/people.php?login=success');
            }
            exit();
        } else {
            // Password is incorrect
            header('Location: /test/login.php?error=invalid_password');
            exit();
        }
    } else {
        // User does not exist
        header('Location: /test/login.php?error=user_not_found');
        exit();
    }
} catch (Exception $e) {
    // Log the error for debugging
    error_log('Login error: ' . $e->getMessage());
    
    // Database error
    header('Location: /test/login.php?error=database_error');
    exit();
}
