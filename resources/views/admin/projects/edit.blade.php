@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h3>Edit Project</h3>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('projects.update', $project) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">Name</label>
            <input name="name" value="{{ old('name', $project->name) }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Start Date</label>
            <input type="date" name="start_date" value="{{ old('start_date', optional($project->start_date)->format('Y-m-d')) }}" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">End Date</label>
            <input type="date" name="end_date" value="{{ old('end_date', optional($project->end_date)->format('Y-m-d')) }}" class="form-control">
        </div>
        <!-- <div class="mb-3">
            <label class="form-label">Status</label>
            <input name="status" value="{{ old('status', $project->status) }}" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Amount</label>
            <input name="amount" value="{{ old('amount', $project->amount) }}" class="form-control" type="number" step="0.01">
        </div> -->
        <div class="mb-3">
            <label class="form-label">Note</label>
            <textarea name="note" class="form-control">{{ old('note', $project->note) }}</textarea>
        </div>

        <button class="btn btn-primary">Save</button>
        <a href="{{ route('projects.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
