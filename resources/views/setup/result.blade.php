@extends('layouts.app')

@section('content')
<div class="container mt-5 pt-5">
    <h2>Setup Result</h2>
    <p>{{ $message }}</p>

    @if(!empty($details))
    <ul>
        @foreach($details as $key => $value)
            <li><strong>{{ $key }}:</strong> {{ $value }}</li>
        @endforeach
    </ul>
    @endif

    <a href="{{ route('login') }}">Go to Login Page</a>
</div>
@endsection
