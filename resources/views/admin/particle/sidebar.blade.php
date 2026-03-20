<!-- Brand Logo -->
    <a href="{{ route('dashboard') }}" class="brand-link">
      <img src="{{ asset('images/brand-logo.png') }}"
           alt="Print Manager Logo"
           class="brand-image"
           style="width:32px;height:32px;border-radius:8px;object-fit:cover;border:2px solid #C9960C;opacity:.95;flex-shrink:0;">
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

          {{-- ── Setting (treeview) ── --}}
          <li class="nav-header">System</li>
          @if(auth()->user() && auth()->user()->hasRole('super-admin'))
          <li class="nav-item">
            <a href="{{ route('projects.index') }}" class="nav-link {{ Request::routeIs('projects.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-folder-open"></i>
              <p>Projects</p>
            </a>
          </li>

          <li class="nav-item">
            <a href="{{ route('expense.index') }}" class="nav-link {{ Request::routeIs('expense.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-folder-open"></i>
              <p>Expenses</p>
            </a>
          </li>

          <li class="nav-item">
            <a href="{{ route('users.index') }}" class="nav-link {{ Request::routeIs('users.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-users"></i>
              <p>Users</p>
            </a>
          </li>

          <li class="nav-item">
            <a href="{{ route('roles.index') }}" class="nav-link {{ Request::routeIs('roles.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-user-shield"></i>
              <p>Roles</p>
            </a>
          </li>
          @endif

          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-sliders-h"></i>
              <p>
                Setting
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              {{-- add sub-items here --}}
            </ul>
          </li>

        </ul>
      </nav>
    </div>