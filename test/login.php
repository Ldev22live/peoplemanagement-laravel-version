<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - People Management System</title>
    
    <!-- Material Design for Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.0/mdb.min.css" rel="stylesheet">
    
    <!-- Custom styles -->
    <link href="../styles/main.css" rel="stylesheet">
    <style>
        body {
            background-color: #a8daee;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
    </style>
</head>
<body>
    <!-- Login Content -->
    <div class="content-wrapper">
        <div class="container login-container mt-5 pt-5">
            <div class="card login-card">
                <div class="card-body p-4">
                    <h3 class="text-center mb-5">Welcome Back!</h3>
                    
                    <form action="processes/handle_login.php" method="post">
                        <!-- Username/Email input -->
                        <div class="form-outline mb-4">
                            <input type="text" id="username" name="username" class="form-control" required />
                            <label class="form-label" for="username">Username or Email</label>
                        </div>
                        
                        <!-- Password input -->
                        <div class="form-outline mb-4">
                            <input type="password" id="password" name="password" class="form-control" required />
                            <label class="form-label" for="password">Password</label>
                        </div>
                        
                        <!-- Remember me checkbox -->
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" value="" id="remember-me" />
                            <label class="form-check-label" for="remember-me">Remember me</label>
                        </div>
                        
                        <!-- Submit button -->
                        <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                    </form>
                    <p>Follow these steps to setup application</p>
                    <a href="create_people_table.php" class="btn btn-secondary btn-block">1. Create People Table</a>
                    <br/>
                    <a href="create_users_table.php" class="btn btn-secondary btn-block">2. Create Users Table</a>
                    <br/>
                    <a href="create_test_user.php" class="btn btn-secondary btn-block">3. Create Test User</a>
                    <p><b>Sign with username: test and password: test</b></p>
                    <br/>
                    
                </div>
            </div>
        </div>
    </div>

    <!-- Material Design for Bootstrap JS -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.0/mdb.min.js"></script>
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <!-- Custom scripts -->
    <script src="../scripts/main.js"></script>
</body>
</html>
