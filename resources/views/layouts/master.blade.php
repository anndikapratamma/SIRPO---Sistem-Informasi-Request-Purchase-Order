<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'SIRPO - Sistem Informasi Request Purchase Order')</title>
  <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/logos/favicon.png') }}" />

  <!-- Core CSS -->
  <link rel="stylesheet" href="{{ asset('assets/css/styles.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/css/icons/tabler-icons/tabler-icons.css') }}" />

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">

  @stack('styles')

  <style>
    /* Custom SIRPO Styles */
    .page-wrapper[data-layout="vertical"][data-sidebartype="full"] .body-wrapper {
      margin-left: 270px;
    }

    @media (max-width: 1199px) {
      .page-wrapper[data-layout="vertical"][data-sidebartype="full"] .body-wrapper {
        margin-left: 0;
      }
    }

    .left-sidebar {
      background: linear-gradient(135deg, #6f42c1 0%, #007bff 100%);
    }

    .sidebar-link.active {
      background-color: rgba(255, 255, 255, 0.1);
      color: white !important;
    }

    .sidebar-link:hover {
      background-color: rgba(255, 255, 255, 0.05);
    }

    /* PB Styling */
    .table-danger {
      background-color: #f8d7da !important;
    }
    .table-danger:hover {
      background-color: #f5c2c7 !important;
    }
    .pb-number {
      font-weight: bold;
      color: #0d6efd;
    }
    .pb-number-cancelled {
      color: #dc3545 !important;
    }

    .stat-card {
      transition: transform 0.2s;
      border: none;
      box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    .stat-card:hover {
      transform: translateY(-2px);
    }

    .card {
      border: none;
      box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
      margin-bottom: 1rem;
    }

    .card-header {
      background-color: #fff;
      border-bottom: 1px solid #dee2e6;
      font-weight: 600;
    }
  </style>
</head>

<body>
  <!--  Body Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">

    @include('components.sidebar')

    <!--  Main wrapper -->
    <div class="body-wrapper">
      @include('components.navbar')

      <div class="container-fluid">
        @yield('content')
      </div>
    </div>
  </div>

  <!-- Core JS -->
  <script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
  <script src="{{ asset('assets/libs/bootstrap/js/index.umd.js') }}"></script>
  <script src="{{ asset('assets/libs/simplebar/src/index.js') }}"></script>

  <!-- Core JS -->
  <script src="{{ asset('assets/js/sidebarmenu.js') }}"></script>
  <script src="{{ asset('assets/js/app.min.js') }}"></script>

  <!-- Iconify -->
  <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  @stack('scripts')

  <script>
    // Initialize sidebar toggle
    $(document).ready(function() {
      $("#sidebarCollapse, #headerCollapse").on("click", function() {
        $("#main-wrapper").toggleClass("show-sidebar");
      });
    });
  </script>
</body>
</html>
