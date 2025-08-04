@extends('layouts.app')

@section('content')
<div class="container mt-5 pt-5">
    <div class="row mb-4">
        <div class="col">
            <h2>Person Details</h2>
            <p>Viewing information for {{ $person->name }} {{ $person->surname }}</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('people.index') }}" class="btn btn-secondary me-2">
                <i class="bi bi-arrow-left"></i> Back to People
            </a>
            <a href="{{ route('people.edit', $person->id) }}" class="btn btn-warning">
                <i class="bi bi-pencil-fill"></i> Edit
            </a>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <div class="row">
                <!-- Left Column -->
                <div class="col-md-6">
                    <dl class="person-info">
                        <dt>Name</dt>
                        <dd>{{ $person->name }}</dd>

                        <dt>Surname</dt>
                        <dd>{{ $person->surname }}</dd>

                        <dt>South African ID Number</dt>
                        <dd>{{ $person->id_number }}</dd>

                        <dt>Mobile Number</dt>
                        <dd>{{ $person->mobile_number }}</dd>
                    </dl>
                </div>
                
                <!-- Right Column -->
                <div class="col-md-6">
                    <dl class="person-info">
                        <dt>Email Address</dt>
                        <dd>{{ $person->email }}</dd>

                        <dt>Birth Date</dt>
                        <dd>{{ \Carbon\Carbon::parse($person->birth_date)->format('F j, Y') }}</dd>

                        <dt>Language</dt>
                        <dd>{{ $person->language->name ?? 'Not specified' }}</dd>

                        <dt>Interests</dt>
                        <dd>
                            @if($person->interests->count() > 0)
                                <ul class="interests-list">
                                    @foreach($person->interests as $interest)
                                        <li>{{ $interest->name }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <p>No interests specified</p>
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

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
@endsection
