@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-bell me-2"></i>Notifikasi</h2>
        @if($notifications->count() > 0)
            <a href="#" onclick="markAllAsRead()" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-check-double me-1"></i>Tandai Semua Dibaca
            </a>
        @endif
    </div>

    @if($notifications->count() > 0)
        <div class="row">
            @foreach($notifications as $notification)
                <div class="col-md-12 mb-3">
                    <div class="card notification-item {{ $notification->is_read ? '' : 'border-primary' }}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="mb-2">
                                        <i class="fas {{ $notification->getTypeIcon() }} me-2"></i>
                                        {{ $notification->getTypeLabel() }}
                                        @if(!$notification->is_read)
                                            <span class="badge bg-primary ms-2">Baru</span>
                                        @endif
                                    </h6>
                                    <p class="mb-1 fw-semibold">{{ $notification->title }}</p>
                                    <p class="text-muted mb-2">{{ $notification->message }}</p>
                                    @if($notification->data)
                                        @php $data = json_decode($notification->data, true); @endphp
                                        @if(isset($data['pb_number']))
                                            <span class="badge bg-secondary">PB: {{ $data['pb_number'] }}</span>
                                        @endif
                                        @if(isset($data['nominal']))
                                            <span class="badge bg-success ms-1">{{ number_format($data['nominal']) }}</span>
                                        @endif
                                    @endif
                                </div>
                                <div class="text-end">
                                    <small class="text-muted d-block">{{ $notification->created_at->diffForHumans() }}</small>
                                    @if(!$notification->is_read)
                                        <button onclick="markAsRead({{ $notification->id }})" class="btn btn-sm btn-outline-primary mt-1">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $notifications->links() }}
        </div>
    @else
        <div class="alert alert-info text-center">
            <i class="fas fa-bell-slash fa-3x mb-3 text-muted"></i>
            <h4>Belum ada notifikasi</h4>
            <p class="text-muted">Anda belum memiliki notifikasi apapun.</p>
        </div>
    @endif
</div>

<script>
function markAsRead(notificationId) {
    fetch(`/notifications/${notificationId}/mark-read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function markAllAsRead() {
    fetch('/notifications/mark-all-read', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}
</script>
@endsection
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar position-fixed top-0 start-0 d-none d-md-block" style="width: 250px; z-index: 1000;">
        <div class="p-3">
            <h4 class="text-white mb-4">
                <i class="fas fa-file-invoice-dollar me-2"></i>SIRPO
            </h4>
            <nav class="nav flex-column">
                <a class="nav-link" href="{{ route('dashboard') }}">
                    <i class="fas fa-tachometer-alt"></i>Dashboard
                </a>
                <a class="nav-link" href="{{ route('pb.index') }}">
                    <i class="fas fa-file-invoice"></i>{{ auth()->user()->role === 'admin' ? 'Kelola PB' : 'My PB' }}
                </a>
                <a class="nav-link" href="{{ route('pb.create') }}">
                    <i class="fas fa-plus-circle"></i>Buat PB Baru
                </a>
                <a class="nav-link" href="{{ route('templates.index') }}">
                    <i class="fas fa-file-alt"></i>Templates
                </a>
                <a class="nav-link active" href="{{ route('notifications.index') }}">
                    <i class="fas fa-bell"></i>Notifikasi
                </a>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid py-4">
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 text-dark">Notifikasi</h1>
                            <p class="text-muted">Kelola semua notifikasi sistem</p>
                        </div>
                        <div>
                            @if(isset($notifications) && $notifications->where('read_at', null)->count() > 0)
                                <form action="{{ route('notifications.read-all') }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-check-double me-2"></i>Tandai Semua Dibaca
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-bell me-2"></i>Daftar Notifikasi
                            </h5>
                        </div>
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show">
                                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            @if(!isset($notifications) || $notifications->count() === 0)
                                <div class="text-center py-5">
                                    <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Tidak ada notifikasi</h5>
                                    <p class="text-muted">Notifikasi akan muncul di sini ketika ada aktivitas baru</p>
                                </div>
                            @else
                                @foreach($notifications as $notification)
                                    <div class="notification-item p-3 border-bottom {{ !isset($notification->read_at) ? 'unread' : '' }}">
                                        <div class="d-flex align-items-start">
                                            <div class="me-3">
                                                <i class="fas fa-bell fa-lg {{ !isset($notification->read_at) ? 'text-primary' : 'text-muted' }}"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <h6 class="mb-1 {{ !isset($notification->read_at) ? 'fw-bold' : '' }}">
                                                            {{ $notification->title ?? 'Notifikasi' }}
                                                            @if(!isset($notification->read_at))
                                                                <span class="badge bg-primary ms-2">Baru</span>
                                                            @endif
                                                        </h6>
                                                        <p class="mb-1 text-muted">{{ $notification->message ?? $notification->data['message'] ?? 'Pesan tidak tersedia' }}</p>
                                                        <small class="text-muted">
                                                            <i class="fas fa-clock"></i>
                                                            {{ isset($notification->created_at) ? $notification->created_at->diffForHumans() : 'Waktu tidak tersedia' }}
                                                            @if(isset($notification->read_at))
                                                                • <span class="text-success">Dibaca {{ $notification->read_at->diffForHumans() }}</span>
                                                            @endif
                                                        </small>
                                                    </div>
                                                    <div>
                                                        @if(!isset($notification->read_at))
                                                            <form action="{{ route('notifications.read', $notification->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit" class="btn btn-sm btn-outline-success" title="Tandai Dibaca">
                                                                    <i class="fas fa-check"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </div>

                                                @if(isset($notification->data['pb_id']))
                                                    <div class="mt-2">
                                                        <a href="{{ route('pb.show', $notification->data['pb_id']) }}" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-eye me-1"></i>Lihat Detail PB
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    @if(isset($notifications) && method_exists($notifications, 'hasPages') && $notifications->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $notifications->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    // Auto-refresh notification count
    setInterval(function() {
        fetch('{{ route('notifications.unread-count') }}')
            .then(response => response.json())
            .then(data => {
                const badge = document.querySelector('.notification-badge');
                if (badge) {
                    if (data.count > 0) {
                        badge.textContent = data.count > 99 ? '99+' : data.count;
                        badge.style.display = 'inline';
                    } else {
                        badge.style.display = 'none';
                    }
                }
            })
            .catch(error => console.log('Error checking notifications:', error));
    }, 30000); // Check every 30 seconds
    </script>
</body>
</html>
