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
      <li class="menu-header">Dashboard</li>
      <li class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <a href="{{ route('dashboard') }}" class="nav-link">
          <i class="fas fa-fire"></i>
          <span>Dashboard</span>
        </a>
      </li>

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

      <li class="dropdown {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
        <a href="#" class="nav-link has-dropdown">
          <i class="fas fa-users"></i>
          <span>User Management</span>
        </a>
        <ul class="dropdown-menu">
          <li class="{{ request()->routeIs('admin.users.index') ? 'active' : '' }}">
            <a href="{{ route('admin.users.index') }}" class="nav-link">Manage Users</a>
          </li>
          <li class="{{ request()->routeIs('admin.users.create') ? 'active' : '' }}">
            <a href="{{ route('admin.users.create') }}" class="nav-link">Add User</a>
          </li>
          <li class="{{ request()->routeIs('admin.users.roles') ? 'active' : '' }}">
            <a href="{{ route('admin.users.roles') }}" class="nav-link">Role Management</a>
          </li>
        </ul>
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

        @if(auth()->user()->role === 'admin')
        <!-- Admin Section Header -->
        <li class="nav-header">ADMIN</li>

        <!-- System Management -->
        <li class="nav-item {{ request()->routeIs('admin.backup.*') || request()->routeIs('admin.settings.*') ? 'menu-open' : '' }}">
          <a href="#" class="nav-link {{ request()->routeIs('admin.backup.*') || request()->routeIs('admin.settings.*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-cogs"></i>
            <p>
              System
              <i class="fas fa-angle-left right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="{{ route('admin.backup.index') }}" class="nav-link {{ request()->routeIs('admin.backup.*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Backup</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Settings</p>
              </a>
            </li>
          </ul>
        </li>

        <!-- Reports -->
        <li class="nav-item {{ request()->routeIs('pb.laporan.*') || request()->routeIs('admin.reports.*') ? 'menu-open' : '' }}">
          <a href="#" class="nav-link {{ request()->routeIs('pb.laporan.*') || request()->routeIs('admin.reports.*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-chart-bar"></i>
            <p>
              Reports
              <i class="fas fa-angle-left right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="{{ route('pb.laporan.bulanan') }}" class="nav-link {{ request()->routeIs('pb.laporan.bulanan') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Laporan Bulanan</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('pb.laporan.mingguan') }}" class="nav-link {{ request()->routeIs('pb.laporan.mingguan') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Laporan Mingguan</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('admin.reports.index') }}" class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Advanced Reports</p>
              </a>
            </li>
          </ul>
        </li>

        <!-- User Management -->
        <li class="nav-item {{ request()->routeIs('admin.users.*') ? 'menu-open' : '' }}">
          <a href="#" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-users"></i>
            <p>
              User Management
              <i class="fas fa-angle-left right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.index') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Manage Users</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('admin.users.create') }}" class="nav-link {{ request()->routeIs('admin.users.create') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Add User</p>
              </a>
            </li>
            {{-- <li class="nav-item">
              <a href="{{ route('admin.users.roles') }}" class="nav-link {{ request()->routeIs('admin.users.roles') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Role Management</p>
              </a>
            </li> --}}
          </ul>
        </li>
        @endif

        <!-- Account Section Header -->
        <li class="nav-header">ACCOUNT</li>

        <!-- Profile -->
        <li class="nav-item">
          <a href="{{ route('profile.edit') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-user"></i>
            <p>Profile</p>
          </a>
        </li>

        <!-- Logout -->
        <li class="nav-item">
          <a href="#" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="nav-icon fas fa-sign-out-alt"></i>
            <p>Logout</p>
          </a>
        </li>
      </ul>
    </nav>
  </div>

  <!-- Logout Form -->
  <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
  </form>
</aside> {
  cursor: pointer;
}

.dropdown-menu {
  list-style: none;
  padding: 0;
  margin: 0;
  background: rgba(0, 0, 0, 0.2);
  display: none;
}

.dropdown-menu.show {
  display: block;
}

.dropdown-menu .sidebar-item {
  padding-left: 2.5rem;
}

.has-dropdown::after {
  content: '\f107';
  font-family: 'Font Awesome 5 Free';
  font-weight: 900;
  margin-left: auto;
  transition: transform 0.3s;
}

.has-dropdown[aria-expanded="true"]::after {
  transform: rotate(180deg);
}

.scroll-sidebar {
  height: calc(100vh - 70px);
  overflow-y: auto;
}

/* Custom scrollbar */
.scroll-sidebar::-webkit-scrollbar {
  width: 5px;
}

.scroll-sidebar::-webkit-scrollbar-track {
  background: transparent;
}

.scroll-sidebar::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.2);
  border-radius: 10px;
}

@media (max-width: 1199.98px) {
  .left-sidebar {
    transform: translateX(-100%);
  }
  .left-sidebar.show {
    transform: translateX(0);
  }
}
</style>
<!-- Sidebar End -->
