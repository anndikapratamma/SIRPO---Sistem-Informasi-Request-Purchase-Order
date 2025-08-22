<!--  Header Start -->
<header class="app-header">
  <nav class="navbar navbar-expand-lg navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item d-block d-xl-none">
        <a class="nav-link sidebartoggler" id="headerCollapse" href="javascript:void(0)">
          <i class="ti ti-menu-2"></i>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="javascript:void(0)">
          <iconify-icon icon="solar:bell-linear" class="fs-6"></iconify-icon>
          <div class="notification bg-primary rounded-circle"></div>
        </a>
      </li>
    </ul>
    <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
      <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
        <li class="nav-item dropdown">
          <a class="nav-link" href="javascript:void(0)" id="drop2" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="{{ asset('assets/images/profile/user-1.jpg') }}" alt="" width="35" height="35" class="rounded-circle">
          </a>
          <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop2">
            <div class="message-body">
              <div class="mx-3 mt-2">
                <h5>{{ auth()->user()->name }}</h5>
                <span class="badge bg-{{ auth()->user()->role === 'admin' ? 'primary' : 'success' }}">
                  {{ ucfirst(auth()->user()->role) }}
                </span>
              </div>
              <a href="{{ route('profile.edit') }}" class="d-flex align-items-center gap-2 dropdown-item">
                <i class="ti ti-user fs-6"></i>
                <p class="mb-0 fs-3">My Profile</p>
              </a>
              <a href="{{ route('notifications.index') }}" class="d-flex align-items-center gap-2 dropdown-item">
                <i class="ti ti-bell fs-6"></i>
                <p class="mb-0 fs-3">Notifications</p>
              </a>
              @if(auth()->user()->role === 'admin')
              <a href="{{ route('admin.settings.index') }}" class="d-flex align-items-center gap-2 dropdown-item">
                <i class="ti ti-settings fs-6"></i>
                <p class="mb-0 fs-3">Settings</p>
              </a>
              @endif
              <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form-header').submit();" class="btn btn-outline-primary mx-3 mt-2 d-block">
                Logout
              </a>
              <form id="logout-form-header" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
              </form>
            </div>
          </div>
        </li>
      </ul>
    </div>
  </nav>
</header>
<!--  Header End -->
