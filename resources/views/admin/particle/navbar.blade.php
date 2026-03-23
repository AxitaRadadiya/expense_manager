<!-- Left: Toggle -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button">
          <i class="fas fa-bars"></i>
        </a>
      </li>
    </ul>

    <!-- Right -->
    <ul class="navbar-nav ml-auto align-items-center">

      <!-- Settings -->
      <!-- <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#" title="Settings"
           style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:8px;">
          <i class="fas fa-cog"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-right">
          <span class="dropdown-header">
            <i class="fas fa-cog mr-1"></i> System
          </span>
          @canany(['general-settings-list','general-settings-view','general-settings-create','general-settings-edit','general-settings-delete'])
          <div class="dropdown-divider"></div>
          <a href="{{ route('general_settings.index') }}" class="dropdown-item">
            <i class="fas fa-wrench mr-2" style="color:#C9960C;"></i> General Settings
          </a>
          @endcanany
          @canany(['roles-list','roles-view','roles-create','roles-edit','roles-delete'])
          <div class="dropdown-divider"></div>
          <a href="{{ route('roles.index') }}" class="dropdown-item">
            <i class="fas fa-shield-alt mr-2" style="color:#F5BE2E;"></i> Roles
          </a>
          @endcanany
          @canany(['log-list'])
          <div class="dropdown-divider"></div>
          <a href="{{ route('logs.index') }}" class="dropdown-item">
            <i class="fas fa-clipboard-list mr-2" style="color:#4a90d9;"></i> Logs
          </a>
          @endcanany
        </div>
      </li> -->

      <!-- User -->
      <li class="nav-item dropdown ml-1">
        <a class="nav-link navbar-user-pill" data-toggle="dropdown" href="#">
          <span class="user-avatar">
            <i class="fas fa-user" style="font-size:11px;color:#F5BE2E;"></i>
          </span>
          <span class="d-none d-md-inline" style="font-size:.83rem;font-weight:600;color:rgba(255,255,255,.85);">
            {{ ucfirst(Auth()->user()->name) }}
          </span>
          <i class="fas fa-caret-down ml-1" style="font-size:10px;opacity:.6;color:rgba(255,255,255,.7);"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-right" style="min-width:185px;">
          <span class="dropdown-header">{{ ucfirst(Auth()->user()->name) }}</span>
          @can('profile-edit')
          <div class="dropdown-divider"></div>
          <a href="{{ route('admin.profile.edit') }}" class="dropdown-item">
            <i class="fas fa-user-circle mr-2" style="color:#C9960C;"></i> My Profile
          </a>
          @endcan
          <div class="dropdown-divider"></div>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <a href="{{ route('logout') }}" class="dropdown-item"
               style="color:#e05252;"
               onclick="event.preventDefault();this.closest('form').submit();">
              <i class="fas fa-sign-out-alt mr-2"></i> Logout
            </a>
          </form>
        </div>
      </li>

      <!-- Fullscreen -->
      <li class="nav-item ml-1">
        <a class="nav-link" data-widget="fullscreen" href="#" role="button" title="Fullscreen"
           style="width:34px;height:34px;display:flex;align-items:center;justify-content:center;border-radius:7px;">
          <i class="fas fa-expand-arrows-alt" style="font-size:.82rem;"></i>
        </a>
      </li>

      <!-- Quick Logout -->
      <li class="nav-item ml-1">
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <a href="{{ route('logout') }}" class="nav-link"
             onclick="event.preventDefault();this.closest('form').submit();"
             title="Logout"
             style="width:34px;height:34px;display:flex;align-items:center;justify-content:center;
                    border-radius:7px;color:#e05252 !important;border:1px solid rgba(224,82,82,.25);">
            <i class="fas fa-power-off" style="font-size:.82rem;"></i>
          </a>
        </form>
      </li>

    </ul>