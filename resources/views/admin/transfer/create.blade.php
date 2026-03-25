@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h3>Create Amount</h3>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('transfer.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">User</label>
            <select name="user_id" class="form-control">
                <option value="">— Select user —</option>
                @foreach($users as $u)
                    <option value="{{ $u->id }}" {{ old('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->email }})</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Date</label>
            <input type="date" name="start_date" value="{{ old('start_date', now()->toDateString()) }}" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Amount</label>
            <input name="amount" value="{{ old('amount','0.00') }}" class="form-control" type="number" step="0.01" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Note</label>
            <textarea name="note" class="form-control" rows="3" placeholder="Optional note for this transfer">{{ old('note') }}</textarea>
        </div>

        <button class="btn btn-primary">Create</button>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
