<!-- Brand Logo -->
<a href="{{ route('dashboard') }}" class="brand-link">
  <img src="{{ asset('admin/dist/img/logo.png') }}"
       alt="Expense Manager Logo"
       class="brand-image">
  <span class="brand-text">Expense Manager</span>
</a>

<!-- Sidebar -->
<div class="sidebar">
  <nav class="mt-1">
    <ul class="nav nav-pills nav-sidebar flex-column"
        data-widget="treeview"
        role="menu"
        data-accordion="false">

      {{-- ── Dashboard ── --}}
      <li class="nav-item">
        <a href="{{ route('dashboard') }}"
           class="nav-link {{ Route::is('dashboard') ? 'active' : '' }}">
          <i class="nav-icon fas fa-th-large"></i>
          <p>Dashboard</p>
        </a>
      </li>

      {{-- ── System (admin only) ── --}}
      <li class="nav-header">System</li>

      @if(auth()->user() && auth()->user()->hasRole('super-admin'))

      <li class="nav-item">
        <a href="{{ route('users.index') }}"
           class="nav-link {{ Request::routeIs('users.*') ? 'active' : '' }}">
          <i class="nav-icon fas fa-users"></i>
          <p>Users</p>
        </a>
      </li>

      <li class="nav-item {{ Request::routeIs('roles.*','category.*','projects.*') ? 'menu-open' : '' }}">
        <a href="#" class="nav-link {{ Request::routeIs('roles.*','category.*','projects.*') ? 'active' : '' }}">
          <i class="nav-icon fas fa-sliders-h"></i>
          <p>
            Settings
            <i class="fas fa-angle-left right mr-4"></i>
          </p>
        </a>
        <ul class="nav nav-treeview">
          <li class="nav-item">
            <a href="{{ route('roles.index') }}"
               class="nav-link {{ Request::routeIs('roles.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-user-shield"></i>
              <p>Roles</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('category.index') }}"
               class="nav-link {{ Request::routeIs('category.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-tags"></i>
              <p>Categories</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('projects.index') }}"
               class="nav-link {{ Request::routeIs('projects.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-folder-open"></i>
              <p>Projects</p>
            </a>
          </li>
        </ul>
      </li>

      <li class="nav-item">
        <a href="{{ route('activity-logs.index') }}"
           class="nav-link {{ Request::routeIs('activity-logs.*') ? 'active' : '' }}">
          <i class="nav-icon fas fa-history"></i>
          <p>Activity Logs</p>
        </a>
      </li>

      @endif

      {{-- ── Expenses (all users) ── --}}
      <li class="nav-header">Finance</li>

      <li class="nav-item">
        <a href="{{ route('expense.index') }}"
           class="nav-link {{ Request::routeIs('expense.*') ? 'active' : '' }}">
          <i class="nav-icon fas fa-receipt"></i>
          <p>Expenses</p>
        </a>
      </li>

      @if(auth()->user() && auth()->user()->hasRole(['super-admin', 'owner']))
      <li class="nav-item">
        <a href="{{ route('transfer.index') }}"
           class="nav-link {{ Request::routeIs('transfer.*') ? 'active' : '' }}">
          <i class="nav-icon fas fa-exchange-alt"></i>
          <p>Transfers</p>
        </a>
      </li>
      @endif

    </ul>
  </nav>
</div>
