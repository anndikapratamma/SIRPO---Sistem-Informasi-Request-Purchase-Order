<!-- Main Header -->
<div class="navbar-bg"></div>
<nav class="navbar navbar-expand-lg main-navbar">
  <!-- Left side - Brand and Sidebar Toggle -->
  <div class="navbar-left">
    <div class="navbar-brand mr-3">
      <span class="text-white font-weight-bold">SIRPO</span>
    </div>
    <ul class="navbar-nav">
      <li class="nav-item">
        <a href="#" data-toggle="sidebar" class="nav-link nav-link-lg collapse-btn" aria-label="Toggle Sidebar">
          <i class="fas fa-bars"></i>
        </a>
      </li>
    </ul>
  </div>

  <!-- Right side - User Menu Only -->
  <ul class="navbar-nav navbar-right">
    <!-- User Account Menu -->
    <li class="dropdown">
      <a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user" aria-haspopup="true" aria-expanded="false">
        @if(auth()->user()->profile_photo)
          <img alt="User avatar" src="{{ asset('storage/profile-photos/' . auth()->user()->profile_photo) }}" class="rounded-circle mr-2" width="32" height="32">
        @else
          <div class="user-avatar-small mr-2">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
        @endif
        <span class="d-none d-lg-inline-block">Hi, {{ auth()->user()->name }}</span>
      </a>
      <div class="dropdown-menu dropdown-menu-right pullDown">
        <div class="dropdown-title">Logged in <span id="login-time">calculating...</span></div>
        <a href="{{ route('profile.edit') }}" class="dropdown-item has-icon">
          <i class="far fa-user"></i> My Profile
        </a>
        @if(auth()->user()->role === 'admin')
        <a href="{{ route('admin.settings.index') }}" class="dropdown-item has-icon">
          <i class="fas fa-cog"></i> Settings
        </a>
        @endif
        <div class="dropdown-divider"></div>
        <a href="#" class="dropdown-item has-icon text-danger" onclick="event.preventDefault(); document.getElementById('logout-form-navbar').submit();">
          <i class="fas fa-sign-out-alt"></i> Logout
        </a>
        <form id="logout-form-navbar" action="{{ route('logout') }}" method="POST" style="display: none;">
          @csrf
        </form>
      </div>
    </li>
  </ul>
</nav>

<!-- Custom Navbar CSS -->
<style>
.navbar-bg {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  height: 60px;
  position: fixed;
  width: 100%;
  top: 0;
  z-index: 1030;
}

.main-navbar {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  width: 100%;
  height: 60px;
  background: transparent !important;
  z-index: 1040;
  padding: 0.75rem 1rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  transition: all 0.3s ease;
}

.sidebar-show .main-navbar {
  margin-left: 250px;
  width: calc(100% - 250px);
}

@media (max-width: 768px) {
  .sidebar-show .main-navbar {
    margin-left: 70px;
    width: calc(100% - 70px);
  }
}

.navbar-left {
  display: flex;
  align-items: center;
}

.navbar-brand {
  font-size: 1.4rem;
  font-weight: 700;
  color: white !important;
  margin-right: 1.5rem;
  line-height: 1.2;
}

.nav-link-lg {
  padding: 0.5rem 0.75rem !important;
  display: flex;
  align-items: center;
}

.collapse-btn {
  background: rgba(255, 255, 255, 0.15);
  border-radius: 6px;
  color: white !important;
  transition: background 0.2s ease;
}

.collapse-btn:hover {
  background: rgba(255, 255, 255, 0.25);
}

.navbar-right {
  display: flex;
  align-items: center;
}

.navbar-right .nav-link {
  color: white !important;
}

.navbar-right .nav-link:hover {
  color: rgba(255, 255, 255, 0.9) !important;
}

.dropdown-menu.pullDown {
  animation: pullDown 0.3s ease-out;
  border: none;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
  border-radius: 8px;
  min-width: 240px;
  margin-top: 0.5rem;
  right: 0;
}

@keyframes pullDown {
  0% {
    transform: translateY(-10px);
    opacity: 0;
  }
  100% {
    transform: translateY(0);
    opacity: 1;
  }
}

.dropdown-title {
  background: #f8f9fa;
  border-bottom: 1px solid #e9ecef;
  padding: 10px 15px;
  font-weight: 600;
  color: #007bff;
  font-size: 0.85rem;
}

.dropdown-item {
  padding: 10px 15px;
  display: flex;
  align-items: center;
  font-size: 0.9rem;
}

.dropdown-item:hover {
  background: #f1f3f5;
}

.dropdown-item i {
  width: 18px;
  margin-right: 8px;
  text-align: center;
}

.nav-link-user {
  display: flex;
  align-items: center;
  padding: 0.5rem 0.75rem !important;
  border-radius: 20px;
  background: rgba(255, 255, 255, 0.15);
  transition: all 0.2s ease;
}

.nav-link-user span {
  margin-left: 0.5rem;
  font-size: 0.9rem;
}

.nav-link-user:hover {
  background: rgba(255, 255, 255, 0.25);
  color: white !important;
}

.nav-link-user img {
  border: 2px solid rgba(255, 255, 255, 0.4);
  transition: border-color 0.2s ease;
}

.nav-link-user:hover img {
  border-color: rgba(255, 255, 255, 0.7);
}

@media (max-width: 768px) {
  .main-navbar {
    padding: 0.5rem;
  }

  .navbar-brand {
    font-size: 1.2rem;
    margin-right: 1rem;
  }

  .nav-link-user span {
    display: none !important;
  }

  .nav-link-user {
    padding: 0.4rem 0.6rem !important;
  }

  .dropdown-menu.pullDown {
    min-width: 200px;
    right: 0;
  }
}

@media (max-width: 480px) {
  .navbar-brand {
    font-size: 1.1rem;
    margin-right: 0.75rem;
  }

  .nav-link-user {
    padding: 0.3rem 0.5rem !important;
  }

  .dropdown-menu.pullDown {
    min-width: 180px;
  }
}

.navbar-nav {
  display: flex;
  align-items: center;
  margin: 0;
  padding: 0;
  list-style: none;
}

.nav-item {
  margin: 0 0.2rem;
}

#login-time {
  font-weight: 600;
  color: #28a745;
  margin-left: 4px;
}

.user-avatar-small {
  width: 32px;
  height: 32px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border-radius: 50%;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 0.8rem;
  font-weight: bold;
  border: 2px solid rgba(255, 255, 255, 0.4);
}
</style>

<!-- Login Time Calculator JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function () {
  const loginTime = new Date();
  const loginTimeElement = document.getElementById('login-time');

  function updateLoginTime() {
    if (!loginTimeElement) return;

    const now = new Date();
    const diffMs = now - loginTime;

    const hours = Math.floor(diffMs / (1000 * 60 * 60));
    const minutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((diffMs % (1000 * 60)) / 1000);

    let timeString = '';
    if (hours > 0) {
      timeString = `${hours}h ${minutes}m ${seconds}s ago`;
    } else if (minutes > 0) {
      timeString = `${minutes}m ${seconds}s ago`;
    } else {
      timeString = `${seconds}s ago`;
    }

    loginTimeElement.textContent = timeString;
  }

  updateLoginTime();

  const observer = new IntersectionObserver(
    (entries) => {
      if (entries[0].isIntersecting) {
        const interval = setInterval(updateLoginTime, 1000);
        observer.disconnect();
        return () => clearInterval(interval);
      }
    },
    { threshold: 0.1 }
  );

  observer.observe(document.querySelector('.main-navbar'));
});
</script>
