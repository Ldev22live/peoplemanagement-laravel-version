@extends('layouts.app')

@section('content')
<div class="container mt-5 pt-5">
    <div class="row mb-4">
        <div class="col">
            <h2>{{ $isEditing ? 'Edit' : 'Add' }} Person</h2>
            <p>{{ $isEditing ? 'Update' : 'Create a new' }} person in the system</p>
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
            </div>
            @endif
        </div>
        <div class="col-auto">
            <a href="{{ route('people.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to People
            </a>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <form id="personForm" action="{{ $isEditing ? route('people.update', $person->id) : route('people.store') }}" method="POST" class="needs-validation" novalidate>
                @csrf
                @if($isEditing)
                    @method('PUT')
                @endif

                <!-- Name & Surname -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-outline mb-4">
                            <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $person->name ?? '') }}" required />
                            <label class="form-label" for="name">Name</label>
                            <div class="invalid-feedback">Please enter a name.</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-outline mb-4">
                            <input type="text" id="surname" name="surname" class="form-control" value="{{ old('surname', $person->surname ?? '') }}" required />
                            <label class="form-label" for="surname">Surname</label>
                            <div class="invalid-feedback">Please enter a surname.</div>
                        </div>
                    </div>
                </div>

                <!-- ID Number & Mobile -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-outline mb-4">
                            <input type="text" id="id_number" name="id_number" class="form-control" value="{{ old('id_number', $person->id_number ?? '') }}" pattern="[0-9]{13}" {{ $isEditing ? 'readonly' : 'required' }} />
                            <label class="form-label" for="id_number">South African ID Number</label>
                            <div class="invalid-feedback">Please enter a valid 13-digit South African ID number.</div>
                            <small class="form-text text-muted">Format: 13 digits (YYMMDDSSSS08C)</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-outline mb-4">
                            <input type="tel" id="mobile_number" name="mobile_number" class="form-control" value="{{ old('mobile_number', $person->mobile_number ?? '') }}" pattern="[0-9]{10}" required />
                            <label class="form-label" for="mobile_number">Mobile Number</label>
                            <div class="invalid-feedback">Please enter a valid 10-digit mobile number.</div>
                            <small class="form-text text-muted">Format: 10 digits (e.g., 0712345678)</small>
                        </div>
                    </div>
                </div>

                <!-- Email & Birth Date -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-outline mb-4">
                            <input type="email" id="email" name="email" class="form-control" value="{{ old('email', $person->email ?? '') }}" required />
                            <label class="form-label" for="email">Email Address</label>
                            <div class="invalid-feedback">Please enter a valid email address.</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-outline mb-4">
                            <input type="date" id="birth_date" name="birth_date" class="form-control" value="{{ old('birth_date', isset($person->birth_date) ? \Carbon\Carbon::parse($person->birth_date)->format('Y-m-d') : '') }}" required />
                            <label class="form-label" for="birth_date">Birth Date</label>
                            <div class="invalid-feedback">Please select a birth date.</div>
                        </div>
                    </div>
                </div>

                <!-- Language -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Language</label>
                        <select class="form-select" name="language_id" required>
                            <option value="" disabled {{ empty(old('language_id', $person->language_id ?? '')) ? 'selected' : '' }}>Select a language</option>
                            @foreach($languages as $language)
                                <option value="{{ $language->id }}" {{ old('language_id', $person->language_id ?? '') == $language->id ? 'selected' : '' }}>{{ $language->name }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback">Please select a language.</div>
                    </div>

                    <!-- Interests -->
                    <div class="col-md-6">
                        <label class="form-label">Interests (optional)</label>
                        <div class="interests-container">
                            @foreach($interests as $interest)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="interests[]" value="{{ $interest->id }}" id="interest_{{ $interest->id }}" {{ in_array($interest->id, old('interests', $personInterests ?? [])) ? 'checked' : '' }} />
                                    <label class="form-check-label" for="interest_{{ $interest->id }}">
                                        {{ $interest->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="d-flex justify-content-end">
                    <a href="{{ route('people.index') }}" class="btn btn-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">{{ $isEditing ? 'Update' : 'Save' }} Person</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.0/mdb.min.js"></script>
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

<script>
(function () {
    'use strict';
    var forms = document.querySelectorAll('.needs-validation');

    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            // Remove strict interest validation - Laravel will handle it
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
})();
</script>

<style>
.form-check-input[type=checkbox] { margin-right: 0.5rem; }
.interests-container {
    max-height: 200px;
    overflow-y: auto;
    border: 1px solid #ccc;
    border-radius: 0.25rem;
    padding: 0.5rem;
}
</style>
@endsection
