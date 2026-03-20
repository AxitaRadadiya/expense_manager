@extends('admin.layouts.app')
@section('title','Roles')
@section('content')

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Roles</h1>
            </div>
            <div class="col-sm-6">
                <a href="#" id="newRoleBtn" class="btn btn-primary float-right">New Role</a>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card card-outline card-primary">
            <div class="card-body">
                <table class="table table-striped" id="rolesTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Permissions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
fetch("{{ route('roles.index') }}")
  .then(r => r.json())
  .then(data => {
    const tbody = document.querySelector('#rolesTable tbody');
    data.forEach(r => {
      const tr = document.createElement('tr');
      tr.innerHTML = `<td>${r.id}</td><td>${r.name}</td><td>${(r.permissions||[]).map(p=>p.name).join(', ')}</td>`;
      tbody.appendChild(tr);
    });
  });
</script>
@endpush

@endsection
