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

// Get all people
$people = $peopleRepository->findAll();

// Get success/error messages
$success = isset($_GET['success']) ? $_GET['success'] : '';
$error = isset($_GET['error']) ? $_GET['error'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>People Management - UrConneX</title>
    
    <!-- Material Design for Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.0/mdb.min.css" rel="stylesheet">
    
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
    <!-- Custom styles -->
    <link href="styles/main.css" rel="stylesheet">
    <style>
        .action-buttons .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
            margin-right: 0.25rem;
        }
        
        .alert {
            margin-bottom: 1rem;
        }
        
        .dataTables_wrapper {
            margin-bottom: 2rem;
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
                <h2>People Management</h2>
                <p>Manage people in the system</p>
            </div>
            <div class="col-auto">
                <a href="people_form.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Person
                </a>
            </div>
        </div>
        
        <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php 
            switch ($success) {
                case 'created':
                    echo 'Person added successfully and notification email sent.';
                    break;
                case 'updated':
                    echo 'Person updated successfully.';
                    break;
                case 'deleted':
                    echo 'Person deleted successfully.';
                    break;
                default:
                    echo $success;
            }
            ?>
            <button type="button" class="btn-close" data-mdb-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php 
            switch ($error) {
                case 'id_exists':
                    echo 'A person with this ID number already exists.';
                    break;
                case 'email_exists':
                    echo 'A person with this email address already exists.';
                    break;
                case 'invalid_id':
                    echo 'The provided South African ID number is invalid.';
                    break;
                default:
                    echo $error;
            }
            ?>
            <button type="button" class="btn-close" data-mdb-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-body">
                <table id="peopleTable" class="table table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Surname</th>
                            <th>ID Number</th>
                            <th>Mobile</th>
                            <th>Email</th>
                            <th>Birth Date</th>
                            <th>Language</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($people as $person): ?>
                        <tr>
                            <td><?= htmlspecialchars($person['name']) ?></td>
                            <td><?= htmlspecialchars($person['surname']) ?></td>
                            <td><?= htmlspecialchars($person['id_number']) ?></td>
                            <td><?= htmlspecialchars($person['mobile_number']) ?></td>
                            <td><?= htmlspecialchars($person['email']) ?></td>
                            <td><?= date('Y-m-d', strtotime($person['birth_date'])) ?></td>
                            <td><?= htmlspecialchars($person['language_name'] ?? 'Not specified') ?></td>
                            <td class="action-buttons">
                                <a href="people_view.php?id=<?= $person['id'] ?>" class="btn btn-info btn-sm">
                                    V
                                </a>
                                <a href="people_form.php?id=<?= $person['id'] ?>" class="btn btn-warning btn-sm">
                                    E
                                </a>
                                <button type="button" class="btn btn-danger btn-sm" 
                                        onclick="confirmDelete(<?= $person['id'] ?>, '<?= htmlspecialchars($person['name'] . ' ' . $person['surname']) ?>')">
                                    D
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete <span id="personName"></span>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-mdb-dismiss="modal">Cancel</button>
                    <form id="deleteForm" action="processes/handle_people.php" method="post">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" id="personId" name="id" value="">
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Footer -->
    <?php include_once __DIR__ . '/footer.php'; ?>
    
    <!-- Material Design for Bootstrap JS -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.0/mdb.min.js"></script>
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#peopleTable').DataTable({
                "responsive": true,
                "order": [[1, 'asc'], [0, 'asc']] // Sort by surname then name
            });
        });
        
        function confirmDelete(id, name) {
            document.getElementById('personId').value = id;
            document.getElementById('personName').textContent = name;
            
            var deleteModal = new mdb.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        }
    </script>
</body>
</html>
