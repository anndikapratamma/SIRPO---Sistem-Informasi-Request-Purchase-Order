<!-- Main Sidebar Container -->
<div class="main-sidebar sidebar-style-2">
  <aside id="sidebar-wrapper">
    <div class="sidebar-brand">
      <a href="{{ route('dashboard') }}">
        <img src="{{ asset('logo.png') }}" alt="SIRPO" width="35">
        <span class="logo-name">SIRPO</span>
      </a>

    </div>
    <div class="sidebar-brand sidebar-brand-sm">
      <a href="{{ route('dashboard') }}">SP</a>
    </div>

    <ul class="sidebar-menu">
      <li class="menu-header">PB Management</li>
       <li class="{{ request()->routeIs('pb.index') ? 'active' : '' }}">
        <a href="{{ route('pb.index') }}" class="nav-link">
          <i class="fas fa-file-invoice"></i>
          <span>{{ auth()->user()->role === 'admin' ? 'Kelola PB' : 'My PB' }}</span>
        </a>
      </li>

      @if(auth()->user()->role !== 'admin')
      <li class="{{ request()->routeIs('pb.create') ? 'active' : '' }}">
        <a href="{{ route('pb.create') }}" class="nav-link">
          <i class="fas fa-plus"></i>
          <span>Buat PB Baru</span>
        </a>
      </li>
      @endif

      <li class="menu-header">MY PB </li>
      <li class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <a href="{{ route('dashboard') }}" class="nav-link">
          <i class="fas fa-fire"></i>
          <span>My Input </span>
        </a>
      </li>

      <li class="{{ request()->routeIs('templates.*') ? 'active' : '' }}">
        <a href="{{ route('templates.index') }}" class="nav-link">
          <i class="fas fa-file-alt"></i>
          <span>Templates</span>
        </a>
      </li>

      @if(auth()->user()->role === 'admin')
      <li class="menu-header">Admin</li>
      <li class="dropdown {{ request()->routeIs('admin.backup.*') || request()->routeIs('admin.settings.*') ? 'active' : '' }}">
        <a href="#" class="nav-link has-dropdown">
          <i class="fas fa-cogs"></i>
          <span>System</span>
        </a>
        <ul class="dropdown-menu">
          <li class="{{ request()->routeIs('admin.backup.*') ? 'active' : '' }}">
            <a href="{{ route('admin.backup.index') }}" class="nav-link">Backup</a>
          </li>
          <li class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
            <a href="{{ route('admin.settings.index') }}" class="nav-link">Settings</a>
          </li>
        </ul>
      </li>

      <li class="dropdown {{ request()->routeIs('pb.laporan.*') || request()->routeIs('admin.reports.*') ? 'active' : '' }}">
        <a href="#" class="nav-link has-dropdown">
          <i class="fas fa-chart-bar"></i>
          <span>Reports</span>
        </a>
        <ul class="dropdown-menu">
          <li class="{{ request()->routeIs('pb.laporan.bulanan') ? 'active' : '' }}">
            <a href="{{ route('pb.laporan.bulanan') }}" class="nav-link">Laporan Bulanan</a>
          </li>
          <li class="{{ request()->routeIs('pb.laporan.mingguan') ? 'active' : '' }}">
            <a href="{{ route('pb.laporan.mingguan') }}" class="nav-link">Laporan Mingguan</a>
          </li>
          <li class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
            <a href="{{ route('admin.reports.index') }}" class="nav-link">Advanced Reports</a>
          </li>
        </ul>
      </li>

    <li class="dropdown {{ request()->routeIs('admin.users.*') || request()->routeIs('admin.profile-changes.*') ? 'active' : '' }}">
  <a href="#" class="nav-link has-dropdown">
    <i class="fas fa-users"></i>
    <span>User Management</span>
  </a>
  <ul class="dropdown-menu">
    <li class="{{ request()->routeIs('admin.users.index') ? 'active' : '' }}">
      <a href="{{ route('admin.users.index') }}" class="nav-link">Manage Users</a>
    </li>
    <li class="{{ request()->routeIs('admin.profile-changes.*') ? 'active' : '' }}">
      <a href="{{ route('admin.profile-changes.index') }}" class="nav-link">Profile Changes</a>
    </li>
  </ul>
</li>

      <!-- Backup System -->
      <li class="{{ request()->routeIs('admin.backup.*') ? 'active' : '' }}">
        <a href="{{ route('admin.backup.index') }}" class="nav-link">
          <i class="fas fa-database"></i>
          <span>Backup System</span>
        </a>
      </li>
      @endif

      <li class="menu-header">Account</li>
      <li class="{{ request()->routeIs('profile.*') ? 'active' : '' }}">
        <a href="{{ route('profile.edit') }}" class="nav-link">
          <i class="fas fa-user"></i>
          <span>Profile</span>
        </a>
      </li>

      <li>
        <a href="#" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
          <i class="fas fa-sign-out-alt"></i>
          <span>Logout</span>
        </a>
      </li>
    </ul>

    <!-- Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
      @csrf
    </form>
  </aside>
</div>
