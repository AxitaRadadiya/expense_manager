<!-- Brand Logo -->
<a href="{{ route('dashboard') }}" class="brand-link">
  <img src="{{ asset('admin/dist/img/logo.png') }}"
    alt="Shubham Construction Logo"
    class="brand-image">
  <span class="brand-text">Shubham Construction</span>
</a>

<!-- Sidebar -->
<div class="sidebar">
  @php
  $authUser = auth()->user();
  $isSupervisor = $authUser && $authUser->hasRole('supervisor');
  @endphp

  <nav class="mt-1">
    <ul class="nav nav-pills nav-sidebar flex-column"
      data-widget="treeview"
      role="menu"
      data-accordion="false">

      <li class="nav-item">
        <a href="{{ route('dashboard') }}"
          class="nav-link {{ Route::is('dashboard') ? 'active' : '' }}">
          <i class="nav-icon fas fa-th-large"></i>
          <p>Dashboard</p>
        </a>
      </li>

      @if(! $isSupervisor)
      <li class="nav-header">System</li>
      @endif
      @if($authUser && $authUser->can('user-view'))
      <li class="nav-item">
        <a href="{{ route('users.index') }}"
          class="nav-link {{ Request::routeIs('users.*') ? 'active' : '' }}">
          <i class="nav-icon fas fa-users"></i>
          <p>Users</p>
        </a>
      </li>
      @endif
      @if($authUser && $authUser->can('vendor-view'))
      <li class="nav-item">
        <a href="{{ route('vendor.index') }}"
          class="nav-link {{ Request::routeIs('vendor.*') ? 'active' : '' }}">
          <i class="nav-icon fas fa-store"></i>
          <p>Vendors</p>
        </a>
      </li>
      @endif
      @if($authUser && $authUser->can('credit-view'))
      <li class="nav-item">
        <a href="{{ route('credit.index') }}"
          class="nav-link {{ Request::routeIs('credit.*') ? 'active' : '' }}">
          <i class="nav-icon fas fa-dollar-sign"></i>
          <p>Credits</p>
        </a>
      </li>
      @endif

      @if($authUser && $authUser->can('credit-view') && ! $isSupervisor)
      <li class="nav-item">
        <a href="{{ route('transfer.index') }}"
          class="nav-link {{ Request::routeIs('transfer.*') ? 'active' : '' }}">
          <i class="nav-icon fas fa-exchange-alt"></i>
          <p>Transfers</p>
        </a>
      </li>
      @endif

      @if($authUser && $authUser->can('expense-view'))
      <li class="nav-item">
        <a href="{{ route('expense.index') }}"
          class="nav-link {{ Request::routeIs('expense.*') ? 'active' : '' }}">
          <i class="nav-icon fas fa-wallet"></i>
          <p>Expenses</p>
        </a>
      </li>
      @endif

      @if($authUser && $authUser->can('item-expense-view'))
      <li class="nav-item">
        <a href="{{ route('item-expense.index') }}"
          class="nav-link {{ Request::routeIs('item-expense.*') ? 'active' : '' }}">
          <i class="nav-icon fas fa-box"></i>
          <p>Item Management</p>
        </a>
      </li>
      @endif

      @if($authUser && $authUser->hasRole('super-admin'))
      <li class="nav-item">
        <a href="{{ route('reports.index') }}"
          class="nav-link {{ Request::routeIs('reports.*') ? 'active' : '' }}">
          <i class="nav-icon fas fa-chart-bar"></i>
          <p>Reports</p>
        </a>
      </li>
      @endif

      

      @if($authUser && $authUser->can('role-view'))
      <li class="nav-item {{ Request::routeIs('roles.*') ? 'menu-open' : '' }}">
        <a href="#" class="nav-link {{ Request::routeIs('roles.*') ? 'active' : '' }}">
          <i class="nav-icon fas fa-cog"></i>
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
          @if($authUser && $authUser->hasRole('super-admin'))
          <li class="nav-item">
            <a href="{{ route('category.index') }}"
              class="nav-link {{ Request::routeIs('category.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-layer-group"></i>
              <p>Categories</p>
            </a>
          </li>
          @endif
          @if($authUser && $authUser->can('project-view'))
          <li class="nav-item">
            <a href="{{ route('projects.index') }}"
              class="nav-link {{ Request::routeIs('projects.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-folder-open"></i>
              <p>Projects</p>
            </a>
          </li>
          @endif
          @if($authUser && $authUser->can('project-view'))
          <li class="nav-item">
            <a href="{{ route('item.index') }}"
              class="nav-link {{ Request::routeIs('item.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-tag"></i>
              <p>Items</p>
            </a>
          </li>
          @endif
        </ul>
      </li>
      @endif

      @if($authUser && $authUser->hasRole('super-admin'))
      <li class="nav-item">
        <a href="{{ route('activity-logs.index') }}"
          class="nav-link {{ Request::routeIs('activity-logs.*') ? 'active' : '' }}">
          <i class="nav-icon fas fa-history"></i>
          <p>Activity Logs</p>
        </a>
      </li>
      @endif
    </ul>
  </nav>
</div>