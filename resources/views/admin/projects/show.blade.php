@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h3>Project: {{ $project->name }}</h3>

    <dl class="row">
        <dt class="col-sm-3">Start Date</dt>
        <dd class="col-sm-9">{{ optional($project->start_date)->format('Y-m-d') }}</dd>

        <dt class="col-sm-3">End Date</dt>
        <dd class="col-sm-9">{{ optional($project->end_date)->format('Y-m-d') }}</dd>

        <dt class="col-sm-3">Status</dt>
        <dd class="col-sm-9">{{ $project->status }}</dd>

        <dt class="col-sm-3">Amount</dt>
        <dd class="col-sm-9">{{ number_format($project->amount,2) }}</dd>

        <dt class="col-sm-3">Note</dt>
        <dd class="col-sm-9">{{ $project->note }}</dd>
    </dl>

    <a href="{{ route('projects.index') }}" class="btn btn-secondary">Back</a>
    <a href="{{ route('projects.edit', $project) }}" class="btn btn-primary">Edit</a>
</div>
@endsection
