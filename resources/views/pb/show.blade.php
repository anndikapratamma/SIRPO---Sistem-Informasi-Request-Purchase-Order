@extends('layouttempalte.master')

@section('title', 'Detail PB - SIRPO')

@section('content')


    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid py-4">
            @if(!isset($pb) || !$pb)
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>Data PB tidak ditemukan.
                </div>
                <a href="{{ route('pb.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar PB
                </a>
            @else
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h1 class="h3 text-dark">Detail Permintaan Bayar</h1>
                                <p class="text-muted">
                                    <strong class="text-primary">{{ $pb->nomor_pb ?? 'N/A' }}</strong>
                                    @if(isset($pb->status) && $pb->status === 'cancelled')
                                        <span class="badge bg-danger ms-2">DIBATALKAN</span>
                                    @endif
                                </p>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('pb.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Kembali
                                </a>
                                @if(auth()->user()->role === 'admin' || (auth()->user()->id == ($pb->user_id ?? null)))
                                    <a href="{{ route('pb.edit', $pb->id) }}" class="btn btn-primary">
                                        <i class="fas fa-edit me-2"></i>Edit
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-file-invoice me-2"></i>Informasi PB
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-sm-4"><strong>Nomor PB:</strong></div>
                                    <div class="col-sm-8">
                                        <strong class="text-primary">{{ $pb->nomor_pb ?? 'N/A' }}</strong>
                                        @if(isset($pb->status) && $pb->status === 'cancelled')
                                            <span class="badge bg-danger ms-2">DIBATALKAN</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-sm-4"><strong>Tanggal:</strong></div>
                                    <div class="col-sm-8">{{ $pb->tanggal ? \Carbon\Carbon::parse($pb->tanggal)->format('d F Y') : 'Tidak tersedia' }}</div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-sm-4"><strong>Penginput:</strong></div>
                                    <div class="col-sm-8">{{ $pb->penginput ?? 'Tidak tersedia' }}</div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-sm-4"><strong>Nominal:</strong></div>
                                    <div class="col-sm-8">
                                        <strong class="text-success">Rp {{ number_format($pb->nominal ?? 0, 0, ',', '.') }}</strong>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-sm-4"><strong>Divisi:</strong></div>
                                    <div class="col-sm-8">
                                        <span class="badge {{ ($pb->divisi ?? '') === 'OP' ? 'bg-success' : 'bg-info' }}">
                                            {{ $pb->divisi ?? 'Tidak tersedia' }}
                                        </span>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-sm-4"><strong>Keterangan:</strong></div>
                                    <div class="col-sm-8">{{ $pb->keterangan ?? 'Tidak ada keterangan' }}</div>
                                </div>

                                <!-- File Section -->
                                <div class="row mb-3">
                                    <div class="col-sm-4"><strong>File Lampiran:</strong></div>
                                    <div class="col-sm-8">
                                        @if($pb->file_path && $pb->file_name)
                                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                                <div class="file-info bg-light p-2 rounded flex-grow-1">
                                                    <i class="fas fa-{{
                                                        str_contains($pb->file_type, 'pdf') ? 'file-pdf text-danger' :
                                                        (str_contains($pb->file_type, 'image') ? 'file-image text-primary' :
                                                        (str_contains($pb->file_type, 'sheet') || str_contains($pb->file_type, 'excel') ? 'file-excel text-success' :
                                                        (str_contains($pb->file_type, 'word') || str_contains($pb->file_type, 'document') ? 'file-word text-info' : 'file text-secondary')))
                                                    }} me-2"></i>
                                                    <strong>{{ $pb->file_name }}</strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        Ukuran: {{ number_format($pb->file_size / 1024, 2) }} KB
                                                        | Tipe: {{ strtoupper(pathinfo($pb->file_name, PATHINFO_EXTENSION)) }}
                                                    </small>
                                                </div>
                                                <div class="file-actions d-flex gap-1">
                                                    <a href="{{ route('pb.download-file', $pb->id) }}"
                                                       class="btn btn-sm btn-outline-primary"
                                                       title="Download File">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    @if(str_contains($pb->file_type ?? '', 'image'))
                                                        <a href="{{ asset('storage/' . $pb->file_path) }}"
                                                           target="_blank"
                                                           class="btn btn-sm btn-success"
                                                           title="Lihat Gambar"
                                                           style="color: white; background-color: #28a745;">
                                                            <i class="fas fa-image"></i> Lihat
                                                        </a>
                                                    @endif
                                                    @if(auth()->user()->role === 'admin' || auth()->user()->id == $pb->user_id)
                                                        <button type="button"
                                                                class="btn btn-sm btn-outline-danger"
                                                                onclick="deleteFile({{ $pb->id }})"
                                                                title="Hapus File">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">Tidak ada file lampiran</span>
                                        @endif
                                    </div>
                                </div>

                                @if(isset($pb->keperluan) && $pb->keperluan !== $pb->keterangan)
                                <div class="row mb-3">
                                    <div class="col-sm-4"><strong>Keperluan:</strong></div>
                                    <div class="col-sm-8">{{ $pb->keperluan ?? 'Tidak ada keperluan' }}</div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="fas fa-info-circle me-2"></i>Informasi Tambahan
                                </h6>
                            </div>
                            <div class="card-body">
                                @if(isset($pb->user) && $pb->user)
                                <div class="mb-3">
                                    <small class="text-muted">Dibuat oleh:</small><br>
                                    <strong>{{ $pb->user->name ?? 'Tidak tersedia' }}</strong><br>
                                    <small class="text-muted">NIK: {{ $pb->user->nik ?? 'Tidak tersedia' }}</small>
                                </div>
                                @endif

                                <div class="mb-3">
                                    <small class="text-muted">Tanggal Dibuat:</small><br>
                                    {{ $pb->created_at ? $pb->created_at->format('d F Y, H:i') : 'Tidak tersedia' }} WIB
                                </div>

                                @if($pb->updated_at && $pb->updated_at != $pb->created_at)
                                <div class="mb-3">
                                    <small class="text-muted">Terakhir Diupdate:</small><br>
                                    {{ $pb->updated_at->format('d F Y, H:i') }} WIB
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        // Test console log untuk debugging
        console.log('PB Show page loaded');
        console.log('CSRF Token:', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'));

        async function deleteFile(pbId) {
            console.log('deleteFile called with PB ID:', pbId);

            if (!confirm('Apakah Anda yakin ingin menghapus file ini? Tindakan ini tidak dapat dibatalkan.')) {
                console.log('User cancelled delete');
                return;
            }

            try {
                console.log('Sending delete request...');
                const response = await fetch(`/pb/${pbId}/delete-file`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                console.log('Response status:', response.status);
                const result = await response.json();
                console.log('Response data:', result);

                if (result.success) {
                    alert('File berhasil dihapus!');
                    location.reload();
                } else {
                    alert(result.message || 'Gagal menghapus file');
                }
            } catch (error) {
                console.error('Error deleting file:', error);
                alert('Terjadi kesalahan saat menghapus file');
            }
        }
    </script>

@endsection
