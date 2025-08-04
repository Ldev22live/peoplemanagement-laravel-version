<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php?error=login_required');
    exit();
}

// Include necessary files
require_once __DIR__ . '/../config/Repository/PDO/PeopleRepository.php';
require_once __DIR__ . '/../config/EmailService.php';

use App\Repository\PDO\PeopleRepository;
use App\EmailService;

// Create repository and email service instances
$peopleRepository = new PeopleRepository();
$emailService = new EmailService();

// Get the action from the form
$action = isset($_POST['action']) ? $_POST['action'] : '';

switch ($action) {
    case 'create':
        handleCreate();
        break;
    case 'update':
        handleUpdate();
        break;
    case 'delete':
        handleDelete();
        break;
    default:
        header('Location: /people.php?error=invalid_action');
        exit();
}

function handleCreate() {
    global $peopleRepository, $emailService;
    
    // Get form data
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $surname = isset($_POST['surname']) ? trim($_POST['surname']) : '';
    $idNumber = isset($_POST['id_number']) ? trim($_POST['id_number']) : '';
    $mobileNumber = isset($_POST['mobile_number']) ? trim($_POST['mobile_number']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $birthDate = isset($_POST['birth_date']) ? trim($_POST['birth_date']) : '';
    $languageId = isset($_POST['language_id']) ? (int)$_POST['language_id'] : null;
    $interests = isset($_POST['interests']) ? $_POST['interests'] : [];
    
    // Validate required fields
    if (empty($name) || empty($surname) || empty($idNumber) || empty($mobileNumber) || 
        empty($email) || empty($birthDate) || empty($languageId) || empty($interests)) {
        header('Location: /people_form.php?error=missing_fields');
        exit();
    }
    
    // Validate ID number
    if (!$peopleRepository->validateIdNumber($idNumber)) {
        header('Location: /people_form.php?error=invalid_id');
        exit();
    }
    
    // Check if ID number already exists
    $existingPersonWithId = $peopleRepository->findOneBy(['id_number' => $idNumber]);
    if ($existingPersonWithId) {
        header('Location: /people_form.php?error=id_exists');
        exit();
    }
    
    // Check if email already exists
    $existingPersonWithEmail = $peopleRepository->findOneBy(['email' => $email]);
    if ($existingPersonWithEmail) {
        header('Location: /people_form.php?error=email_exists');
        exit();
    }
    
    // Prepare person data
    $personData = [
        'name' => $name,
        'surname' => $surname,
        'id_number' => $idNumber,
        'mobile_number' => $mobileNumber,
        'email' => $email,
        'birth_date' => $birthDate,
        'language_id' => $languageId,
        'interests' => $interests
    ];
    
    try {
        // Save person to database
        $personId = $peopleRepository->save($personData);
        
        if ($personId) {
            // Get the complete person data for email
            $person = $peopleRepository->find($personId);
            
            // Send notification email
            $emailService->sendPersonRegistrationEmail($person);
            
            header('Location: /people.php?success=created');
        } else {
            header('Location: /people_form.php?error=save_failed');
        }
    } catch (\Exception $e) {
        header('Location: /people_form.php?error=' . urlencode($e->getMessage()));
    }
    
    exit();
}

function handleUpdate() {
    global $peopleRepository;
    
    // Get form data
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $surname = isset($_POST['surname']) ? trim($_POST['surname']) : '';
    $mobileNumber = isset($_POST['mobile_number']) ? trim($_POST['mobile_number']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $birthDate = isset($_POST['birth_date']) ? trim($_POST['birth_date']) : '';
    $languageId = isset($_POST['language_id']) ? (int)$_POST['language_id'] : null;
    $interests = isset($_POST['interests']) ? $_POST['interests'] : [];
    
    // Validate required fields
    if ($id <= 0 || empty($name) || empty($surname) || empty($mobileNumber) || 
        empty($email) || empty($birthDate) || empty($languageId) || empty($interests)) {
        header('Location: /people_form.php?id=' . $id . '&error=missing_fields');
        exit();
    }
    
    // Check if person exists
    $existingPerson = $peopleRepository->find($id);
    if (!$existingPerson) {
        header('Location: /people.php?error=person_not_found');
        exit();
    }
    
    // Check if email already exists (for another person)
    $existingPersonWithEmail = $peopleRepository->findOneBy(['email' => $email]);
    if ($existingPersonWithEmail && $existingPersonWithEmail['id'] != $id) {
        header('Location: /people_form.php?id=' . $id . '&error=email_exists');
        exit();
    }
    
    // Prepare person data
    $personData = [
        'id' => $id,
        'name' => $name,
        'surname' => $surname,
        'mobile_number' => $mobileNumber,
        'email' => $email,
        'birth_date' => $birthDate,
        'language_id' => $languageId,
        'interests' => $interests
    ];
    
    try {
        // Update person in database
        $peopleRepository->save($personData);
        header('Location: /people.php?success=updated');
    } catch (\Exception $e) {
        header('Location: /people_form.php?id=' . $id . '&error=' . urlencode($e->getMessage()));
    }
    
    exit();
}

function handleDelete() {
    global $peopleRepository;
    
    // Get person ID
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    
    if ($id <= 0) {
        header('Location: /people.php?error=invalid_id');
        exit();
    }
    
    // Check if person exists
    $existingPerson = $peopleRepository->find($id);
    if (!$existingPerson) {
        header('Location: /people.php?error=person_not_found');
        exit();
    }
    
    try {
        // Delete person from database
        $peopleRepository->delete($id);
        header('Location: /people.php?success=deleted');
    } catch (\Exception $e) {
        header('Location: /people.php?error=' . urlencode($e->getMessage()));
    }
    
    exit();
}
