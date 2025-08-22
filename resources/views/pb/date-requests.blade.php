@extends('layouttempalte.master')

@section('title', 'Kelola Request Tanggal - SIRPO')

@section('content')
<div class="main-content">
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="h3 text-dark">Kelola Request Tanggal Masa Depan</h1>
                <p class="text-muted">Review dan proses request tanggal masa depan dari user</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-calendar-alt me-2"></i>Request Pending
                            <span class="badge bg-warning ms-2">{{ $requests->total() }}</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($requests->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>User</th>
                                            <th>Tanggal Diminta</th>
                                            <th>Alasan</th>
                                            <th>Tanggal Request</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($requests as $request)
                                            <tr>
                                                <td>
                                                    <strong>{{ $request->user->name }}</strong><br>
                                                    <small class="text-muted">{{ $request->user->email }}</small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">
                                                        {{ $request->requested_date->format('d/m/Y') }}
                                                    </span><br>
                                                    <small class="text-muted">
                                                        {{ $request->requested_date->diffForHumans() }}
                                                    </small>
                                                </td>
                                                <td>
                                                    <div style="max-width: 250px;">
                                                        {{ Str::limit($request->reason, 100) }}
                                                        @if(strlen($request->reason) > 100)
                                                            <br><small>
                                                                <a href="#" class="text-primary" data-bs-toggle="modal"
                                                                   data-bs-target="#reasonModal{{ $request->id }}">
                                                                    Lihat selengkapnya
                                                                </a>
                                                            </small>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    {{ $request->created_at->format('d/m/Y H:i') }}<br>
                                                    <small class="text-muted">
                                                        {{ $request->created_at->diffForHumans() }}
                                                    </small>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <button type="button" class="btn btn-success"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#approveModal{{ $request->id }}">
                                                            <i class="fas fa-check"></i> Setujui
                                                        </button>
                                                        <button type="button" class="btn btn-danger"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#rejectModal{{ $request->id }}">
                                                            <i class="fas fa-times"></i> Tolak
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- Approve Modal -->
                                            <div class="modal fade" id="approveModal{{ $request->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Setujui Request</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Apakah Anda yakin ingin menyetujui request tanggal
                                                               <strong>{{ $request->requested_date->format('d/m/Y') }}</strong>
                                                               dari <strong>{{ $request->user->name }}</strong>?</p>
                                                            <div class="alert alert-info">
                                                                <strong>Alasan:</strong><br>
                                                                {{ $request->reason }}
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                            <form method="POST" action="{{ route('pb.date-requests.process', $request->id) }}" style="display: inline;">
                                                                @csrf
                                                                <input type="hidden" name="action" value="approve">
                                                                <button type="submit" class="btn btn-success">Ya, Setujui</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Reject Modal -->
                                            <div class="modal fade" id="rejectModal{{ $request->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Tolak Request</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <form method="POST" action="{{ route('pb.date-requests.process', $request->id) }}">
                                                            @csrf
                                                            <input type="hidden" name="action" value="reject">
                                                            <div class="modal-body">
                                                                <p>Menolak request tanggal
                                                                   <strong>{{ $request->requested_date->format('d/m/Y') }}</strong>
                                                                   dari <strong>{{ $request->user->name }}</strong></p>

                                                                <div class="alert alert-info">
                                                                    <strong>Alasan User:</strong><br>
                                                                    {{ $request->reason }}
                                                                </div>

                                                                <div class="mb-3">
                                                                    <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                                                                    <textarea name="rejection_reason" class="form-control" rows="3"
                                                                              placeholder="Jelaskan mengapa request ditolak" required></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                <button type="submit" class="btn btn-danger">Tolak Request</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Reason Detail Modal -->
                                            @if(strlen($request->reason) > 100)
                                                <div class="modal fade" id="reasonModal{{ $request->id }}" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Detail Alasan</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p><strong>Request dari:</strong> {{ $request->user->name }}</p>
                                                                <p><strong>Tanggal diminta:</strong> {{ $request->requested_date->format('d/m/Y') }}</p>
                                                                <hr>
                                                                <p><strong>Alasan lengkap:</strong></p>
                                                                <div class="alert alert-light">
                                                                    {{ $request->reason }}
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="d-flex justify-content-center mt-4">
                                {{ $requests->links() }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-calendar-check fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Tidak Ada Request Pending</h5>
                                <p class="text-muted">Semua request tanggal masa depan sudah diproses.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
