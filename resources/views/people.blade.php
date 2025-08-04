@extends('layouts.app')

@section('content')
<div class="container mt-5 pt-5">
    <div class="row mb-4">
        <div class="col">
            <h2>People Management</h2>
            <p>Manage people in the system</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('people.create') }}" class="btn btn-primary">
                <i class="bi bi-plus"></i> Add New Person
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

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
                    @foreach($people as $person)
                    <tr id="person-row-{{ $person['id'] }}">
                        <td>{{ $person['name'] }}</td>
                        <td>{{ $person['surname'] }}</td>
                        <td>{{ $person['id_number'] }}</td>
                        <td>{{ $person['mobile_number'] }}</td>
                        <td>{{ $person['email'] }}</td>
                        <td>{{ \Carbon\Carbon::parse($person['birth_date'])->format('Y-m-d') }}</td>
                        <td>{{ $person['language_name'] ?? 'Not specified' }}</td>
                        <td class="action-buttons">
                            <a href="{{ route('people.show', $person['id']) }}" class="btn btn-info btn-sm">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('people.edit', $person['id']) }}" class="btn btn-warning btn-sm">
                                <i class="bi bi-pencil-fill"></i>
                            </a>
                            <button type="button" class="btn btn-danger btn-sm delete-btn" 
                                data-id="{{ $person['id'] }}" 
                                data-url="{{ route('people.destroy', $person['id']) }}">
                                <i class="bi bi-trash-fill"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this person?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    $('#peopleTable').DataTable({
        "responsive": true,
        "order": [[1, 'asc'], [0, 'asc']]
    });

    let deleteUrl = '';
    let deleteId = '';

    // Show modal on delete button click
    $(document).on('click', '.delete-btn', function() {
        deleteId = $(this).data('id');
        deleteUrl = $(this).data('url');
        $('#deleteModal').modal('show');
    });

    // Handle delete confirmation
    $('#confirmDeleteBtn').click(function() {
        $.ajax({
            url: deleteUrl,
            type: 'POST',
            data: {
                _method: 'DELETE',
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#deleteModal').modal('hide');
                $('#person-row-' + deleteId).fadeOut(500, function() {
                    $(this).remove();
                });
            },
            error: function(xhr) {
                alert('Error deleting person.');
            }
        });
    });
});
</script>

<style>
.action-buttons .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.8rem;
    margin-right: 0.25rem;
}
.alert { margin-bottom: 1rem; }
.dataTables_wrapper { margin-bottom: 2rem; }
</style>
@endsection
