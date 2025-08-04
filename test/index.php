<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?error=login_required');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - People Management System</title>
    
    <!-- Material Design for Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.0/mdb.min.css" rel="stylesheet">
    
    <!-- Custom styles -->
    <link href="../styles/main.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <?php include 'system-includes/navigation.php'; ?>
    
    <!-- Main Content -->
    <div class="container mt-5 pt-5">
        <div class="row mb-4">
            <div class="col">
                <h2>Welcome to People Management System</h2>
                <p>Use the navigation menu to manage people records</p>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-8 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">People Management</h5>
                        <p class="card-text">View, add, edit, and delete people records in the system.</p>
                        <a href="people.php" class="btn btn-primary">Go to People</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Material Design for Bootstrap JS -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.0/mdb.min.js"></script>
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>
