<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php?error=login_required');
    exit();
}

// Include necessary files
require_once __DIR__ . '/config/Repository/PDO/PeopleRepository.php';
use App\Repository\PDO\PeopleRepository;

// Create repository instance
$peopleRepository = new PeopleRepository();

// Check if we have a person ID
if (!isset($_GET['id'])) {
    header('Location: /people.php?error=invalid_request');
    exit();
}

$personId = (int)$_GET['id'];
$person = $peopleRepository->find($personId);

if (!$person) {
    header('Location: /people.php?error=person_not_found');
    exit();
}

// Get person's interests
$personInterests = $peopleRepository->getPersonInterests($personId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Person - UrConneX</title>
    
    <!-- Material Design for Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.0/mdb.min.css" rel="stylesheet">
    
    <!-- Custom styles -->
    <link href="styles/main.css" rel="stylesheet">
    <style>
        .person-info dt {
            font-weight: 600;
        }
        
        .person-info dd {
            margin-bottom: 1rem;
        }
        
        .interests-list {
            list-style: none;
            padding-left: 0;
        }
        
        .interests-list li {
            display: inline-block;
            background-color: #e9ecef;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <?php include 'system-includes/navbar.php'; ?>
    
    <!-- Main Content -->
    <div class="container mt-5 pt-5">
        <div class="row mb-4">
            <div class="col">
                <h2>Person Details</h2>
                <p>Viewing information for <?= htmlspecialchars($person['name'] . ' ' . $person['surname']) ?></p>
            </div>
            <div class="col-auto">
                <a href="people.php" class="btn btn-secondary me-2">
                    <i class="fas fa-arrow-left"></i> Back to People
                </a>
                <a href="people_form.php?id=<?= $person['id'] ?>" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Edit
                </a>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <dl class="person-info">
                            <dt>Name</dt>
                            <dd><?= htmlspecialchars($person['name']) ?></dd>
                            
                            <dt>Surname</dt>
                            <dd><?= htmlspecialchars($person['surname']) ?></dd>
                            
                            <dt>South African ID Number</dt>
                            <dd><?= htmlspecialchars($person['id_number']) ?></dd>
                            
                            <dt>Mobile Number</dt>
                            <dd><?= htmlspecialchars($person['mobile_number']) ?></dd>
                        </dl>
                    </div>
                    <div class="col-md-6">
                        <dl class="person-info">
                            <dt>Email Address</dt>
                            <dd><?= htmlspecialchars($person['email']) ?></dd>
                            
                            <dt>Birth Date</dt>
                            <dd><?= date('F j, Y', strtotime($person['birth_date'])) ?></dd>
                            
                            <dt>Language</dt>
                            <dd><?= htmlspecialchars($person['language_name'] ?? 'Not specified') ?></dd>
                            
                            <dt>Interests</dt>
                            <dd>
                                <?php if (count($personInterests) > 0): ?>
                                <ul class="interests-list">
                                    <?php foreach ($personInterests as $interest): ?>
                                    <li><?= htmlspecialchars($interest['name']) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <?php else: ?>
                                <p>No interests specified</p>
                                <?php endif; ?>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Footer -->
    <?php include_once 'footer.php'; ?>
    
    <!-- Material Design for Bootstrap JS -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.0/mdb.min.js"></script>
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>
