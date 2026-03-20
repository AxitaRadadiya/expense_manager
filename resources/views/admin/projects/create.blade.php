@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h3>Create Project</h3>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('projects.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">Name</label>
            <input name="name" value="{{ old('name') }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Start Date</label>
            <input type="date" name="start_date" value="{{ old('start_date') }}" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">End Date</label>
            <input type="date" name="end_date" value="{{ old('end_date') }}" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Status</label>
            <input name="status" value="{{ old('status','pending') }}" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Amount</label>
            <input name="amount" value="{{ old('amount','0.00') }}" class="form-control" type="number" step="0.01">
        </div>
        <div class="mb-3">
            <label class="form-label">Note</label>
            <textarea name="note" class="form-control">{{ old('note') }}</textarea>
        </div>

        <button class="btn btn-primary">Create</button>
        <a href="{{ route('projects.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
