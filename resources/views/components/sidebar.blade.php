<!-- Sidebar Start -->
<aside class="left-sidebar">
  <!-- Sidebar scroll-->
  <div>
    <div class="brand-logo d-flex align-items-center justify-content-between">
      <a href="{{ route('dashboard') }}" class="text-nowrap logo-img">
        <img src="{{ asset('assets/images/logos/logo.svg') }}" alt="SIRPO Logo" />
      </a>
      <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
        <i class="ti ti-x fs-6"></i>
      </div>
    </div>
    <!-- Sidebar navigation-->
    <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
      <ul id="sidebarnav">
        <li class="nav-small-cap">
          <iconify-icon icon="solar:menu-dots-linear" class="nav-small-cap-icon fs-4"></iconify-icon>
          <span class="hide-menu">Home</span>
        </li>
        <li class="sidebar-item">
          <a class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}" aria-expanded="false">
            <i class="ti ti-atom"></i>
            <span class="hide-menu">Dashboard</span>
          </a>
        </li>

        <!-- PB Management -->
        <li class="sidebar-item">
          <a class="sidebar-link {{ request()->routeIs('pb.*') ? 'active' : '' }}" href="{{ route('pb.index') }}" aria-expanded="false">
            <div class="d-flex align-items-center gap-3">
              <span class="d-flex">
                <i class="ti ti-file-invoice"></i>
              </span>
              <span class="hide-menu">{{ auth()->user()->role === 'admin' ? 'Kelola PB' : 'My PB' }}</span>
            </div>
          </a>
        </li>

        @if(auth()->user()->role !== 'admin')
        <li class="sidebar-item">
          <a class="sidebar-link {{ request()->routeIs('pb.create') ? 'active' : '' }}" href="{{ route('pb.create') }}" aria-expanded="false">
            <div class="d-flex align-items-center gap-3">
              <span class="d-flex">
                <i class="ti ti-plus-circle"></i>
              </span>
              <span class="hide-menu">Buat PB Baru</span>
            </div>
          </a>
        </li>
        @endif

        <li class="sidebar-item">
          <a class="sidebar-link {{ request()->routeIs('templates.*') ? 'active' : '' }}" href="{{ route('templates.index') }}" aria-expanded="false">
            <div class="d-flex align-items-center gap-3">
              <span class="d-flex">
                <i class="ti ti-file-text"></i>
              </span>
              <span class="hide-menu">Templates</span>
            </div>
          </a>
        </li>

        <li class="sidebar-item">
          <a class="sidebar-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}" href="{{ route('notifications.index') }}" aria-expanded="false">
            <div class="d-flex align-items-center gap-3">
              <span class="d-flex">
                <i class="ti ti-bell"></i>
              </span>
              <span class="hide-menu">Notifications</span>
            </div>
          </a>
        </li>

        <li class="sidebar-item">
          <a class="sidebar-link {{ request()->routeIs('search.*') ? 'active' : '' }}" href="{{ route('search.global') }}" aria-expanded="false">
            <div class="d-flex align-items-center gap-3">
              <span class="d-flex">
                <i class="ti ti-search"></i>
              </span>
              <span class="hide-menu">Search</span>
            </div>
          </a>
        </li>

        @if(auth()->user()->role === 'admin')
        <li>
          <span class="sidebar-divider lg"></span>
        </li>
        <li class="nav-small-cap">
          <iconify-icon icon="solar:menu-dots-linear" class="nav-small-cap-icon fs-4"></iconify-icon>
          <span class="hide-menu">Admin</span>
        </li>
        <li class="sidebar-item">
          <a class="sidebar-link {{ request()->routeIs('admin.backup.*') ? 'active' : '' }}" href="{{ route('admin.backup.index') }}" aria-expanded="false">
            <div class="d-flex align-items-center gap-3">
              <span class="d-flex">
                <i class="ti ti-database"></i>
              </span>
              <span class="hide-menu">Backup</span>
            </div>
          </a>
        </li>
        <li class="sidebar-item">
          <a class="sidebar-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" href="{{ route('admin.reports.index') }}" aria-expanded="false">
            <div class="d-flex align-items-center gap-3">
              <span class="d-flex">
                <i class="ti ti-chart-bar"></i>
              </span>
              <span class="hide-menu">Reports</span>
            </div>
          </a>
        </li>
        <li class="sidebar-item">
          <a class="sidebar-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" href="{{ route('admin.settings.index') }}" aria-expanded="false">
            <div class="d-flex align-items-center gap-3">
              <span class="d-flex">
                <i class="ti ti-settings"></i>
              </span>
              <span class="hide-menu">Settings</span>
            </div>
          </a>
        </li>
        @endif

        <li>
          <span class="sidebar-divider lg"></span>
        </li>
        <li class="nav-small-cap">
          <iconify-icon icon="solar:menu-dots-linear" class="nav-small-cap-icon fs-4"></iconify-icon>
          <span class="hide-menu">Account</span>
        </li>
        <li class="sidebar-item">
          <a class="sidebar-link {{ request()->routeIs('profile.*') ? 'active' : '' }}" href="{{ route('profile.edit') }}" aria-expanded="false">
            <div class="d-flex align-items-center gap-3">
              <span class="d-flex">
                <i class="ti ti-user-circle"></i>
              </span>
              <span class="hide-menu">Profile</span>
            </div>
          </a>
        </li>
        <li class="sidebar-item">
          <a class="sidebar-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" aria-expanded="false">
            <div class="d-flex align-items-center gap-3">
              <span class="d-flex">
                <i class="ti ti-logout"></i>
              </span>
              <span class="hide-menu">Logout</span>
            </div>
          </a>
          <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
          </form>
        </li>
      </ul>
    </nav>
  </div>
</aside>
<!--  Sidebar End -->
