<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?error=login_required');
    exit();
}

// Include necessary files
require_once __DIR__ . '/config/Repository/PDO/PeopleRepository.php';
use App\Repository\PDO\PeopleRepository;

// Create repository instance
$peopleRepository = new PeopleRepository();

// Get all languages and interests
$languages = $peopleRepository->getAllLanguages();
$interests = $peopleRepository->getAllInterests();

// Check if we're editing an existing person
$isEditing = false;
$person = [
    'id' => '',
    'name' => '',
    'surname' => '',
    'id_number' => '',
    'mobile_number' => '',
    'email' => '',
    'birth_date' => '',
    'language_id' => '',
];
$personInterests = [];

if (isset($_GET['id'])) {
    $personId = (int)$_GET['id'];
    $personData = $peopleRepository->find($personId);
    
    if ($personData) {
        $isEditing = true;
        $person = $personData;
        
        // Get person's interests
        $personInterests = array_column($peopleRepository->getPersonInterests($personId), 'id');
    } else {
        header('Location: /people.php?error=person_not_found');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $isEditing ? 'Edit' : 'Add' ?> Person - UrConneX</title>
    
    <!-- Material Design for Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.0/mdb.min.css" rel="stylesheet">
    
    <!-- Custom styles -->
    <link href="styles/main.css" rel="stylesheet">
    <style>
        .form-check-input[type=checkbox] {
            margin-right: 0.5rem;
        }
        
        .interests-container {
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #ccc;
            border-radius: 0.25rem;
            padding: 0.5rem;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <?php include 'system-includes/navigation.php'; ?>
    
    <!-- Main Content -->
    <div class="container mt-5 pt-5">
        <div class="row mb-4">
            <div class="col">
                <h2><?= $isEditing ? 'Edit' : 'Add' ?> Person</h2>
                <p><?= $isEditing ? 'Update' : 'Create a new' ?> person in the system</p>
            </div>
            <div class="col-auto">
                <a href="people.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to People
                </a>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body">
                <form id="personForm" action="processes/handle_people.php" method="post" class="needs-validation" novalidate>
                    <input type="hidden" name="action" value="<?= $isEditing ? 'update' : 'create' ?>">
                    <?php if ($isEditing): ?>
                    <input type="hidden" name="id" value="<?= $person['id'] ?>">
                    <?php endif; ?>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-outline mb-4">
                                <input type="text" id="name" name="name" class="form-control" value="<?= htmlspecialchars($person['name']) ?>" required />
                                <label class="form-label" for="name">Name</label>
                                <div class="invalid-feedback">Please enter a name.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-outline mb-4">
                                <input type="text" id="surname" name="surname" class="form-control" value="<?= htmlspecialchars($person['surname']) ?>" required />
                                <label class="form-label" for="surname">Surname</label>
                                <div class="invalid-feedback">Please enter a surname.</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-outline mb-4">
                                <input type="text" id="id_number" name="id_number" class="form-control" value="<?= htmlspecialchars($person['id_number']) ?>" 
                                       pattern="[0-9]{13}" <?= $isEditing ? 'readonly' : 'required' ?> />
                                <label class="form-label" for="id_number">South African ID Number</label>
                                <div class="invalid-feedback">Please enter a valid 13-digit South African ID number.</div>
                                <small class="form-text text-muted">Format: 13 digits (YYMMDDSSSS08C)</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-outline mb-4">
                                <input type="tel" id="mobile_number" name="mobile_number" class="form-control" value="<?= htmlspecialchars($person['mobile_number']) ?>" 
                                       pattern="[0-9]{10}" required />
                                <label class="form-label" for="mobile_number">Mobile Number</label>
                                <div class="invalid-feedback">Please enter a valid 10-digit mobile number.</div>
                                <small class="form-text text-muted">Format: 10 digits (e.g., 0712345678)</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-outline mb-4">
                                <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($person['email']) ?>" required />
                                <label class="form-label" for="email">Email Address</label>
                                <div class="invalid-feedback">Please enter a valid email address.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-outline mb-4">
                                <input type="date" id="birth_date" name="birth_date" class="form-control" 
                                       value="<?= $person['birth_date'] ? date('Y-m-d', strtotime($person['birth_date'])) : '' ?>" required />
                                <label class="form-label" for="birth_date">Birth Date</label>
                                <div class="invalid-feedback">Please select a birth date.</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Language</label>
                            <select class="form-select" name="language_id" required>
                                <option value="" disabled <?= empty($person['language_id']) ? 'selected' : '' ?>>Select a language</option>
                                <?php foreach ($languages as $language): ?>
                                <option value="<?= $language['id'] ?>" <?= $person['language_id'] == $language['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($language['name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Please select a language.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Interests</label>
                            <div class="interests-container">
                                <?php foreach ($interests as $interest): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="interests[]" 
                                           value="<?= $interest['id'] ?>" id="interest_<?= $interest['id'] ?>"
                                           <?= in_array($interest['id'], $personInterests) ? 'checked' : '' ?> />
                                    <label class="form-check-label" for="interest_<?= $interest['id'] ?>">
                                        <?= htmlspecialchars($interest['name']) ?>
                                    </label>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="invalid-feedback">Please select at least one interest.</div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary me-2" onclick="window.location.href='people.php'">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <?= $isEditing ? 'Update' : 'Save' ?> Person
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Include Footer -->
    <?php include_once __DIR__ . '/footer.php'; ?>
    
    <!-- Material Design for Bootstrap JS -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.0/mdb.min.js"></script>
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    
    <script>
        // Form validation
        (function() {
            'use strict';
            
            // Fetch all forms we want to apply custom validation to
            var forms = document.querySelectorAll('.needs-validation');
            
            // Loop over them and prevent submission
            Array.prototype.slice.call(forms).forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    // Check if at least one interest is selected
                    var interestChecked = false;
                    var interestCheckboxes = form.querySelectorAll('input[name="interests[]"]');
                    
                    for (var i = 0; i < interestCheckboxes.length; i++) {
                        if (interestCheckboxes[i].checked) {
                            interestChecked = true;
                            break;
                        }
                    }
                    
                    if (!interestChecked) {
                        event.preventDefault();
                        event.stopPropagation();
                        document.querySelector('.interests-container').classList.add('is-invalid');
                    } else {
                        document.querySelector('.interests-container').classList.remove('is-invalid');
                    }
                    
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    
                    form.classList.add('was-validated');
                }, false);
            });
        })();
    </script>
</body>
</html>
