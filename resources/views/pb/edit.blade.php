@extends('layouttempalte.master')

@section('title', 'Edit PB - SIRPO')

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2>Edit PB - {{ $pb->nomor_pb ?? 'N/A' }}</h2>
                        <div>
                            <a href="{{ route('pb.show', $pb->id) }}" class="btn btn-info me-2">
                                <i class="fas fa-eye"></i> Lihat Detail
                            </a>
                            <a href="{{ route('pb.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Form Edit PB</h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="{{ route('pb.update', $pb->id) }}" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="tanggal" class="form-label">Tanggal <span class="text-danger">*</span></label>
                                                <input type="date" class="form-control" id="tanggal" name="tanggal"
                                                       value="{{ old('tanggal', $pb->tanggal instanceof \Carbon\Carbon ? $pb->tanggal->format('Y-m-d') : $pb->tanggal) }}" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="penginput" class="form-label">Penginput <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="penginput" name="penginput"
                                                       value="{{ old('penginput', $pb->penginput) }}" required>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="divisi" class="form-label">Divisi <span class="text-danger">*</span></label>
                                                <select class="form-select" id="divisi" name="divisi" required>
                                                    <option value="">Pilih Divisi</option>
                                                    <option value="E-CHANNEL" {{ old('divisi', $pb->divisi) == 'E-CHANNEL' ? 'selected' : '' }}>E-CHANNEL</option>
                                                    <option value="TREASURY OPERASIONAL" {{ old('divisi', $pb->divisi) == 'TREASURY OPERASIONAL' ? 'selected' : '' }}>TREASURY OPERASIONAL</option>
                                                    <option value="LAYANAN OPERASIONAL" {{ old('divisi', $pb->divisi) == 'LAYANAN OPERASIONAL' ? 'selected' : '' }}>LAYANAN OPERASIONAL</option>
                                                    <option value="AKUNTANSI & TAX MANAGEMENT" {{ old('divisi', $pb->divisi) == 'AKUNTANSI & TAX MANAGEMENT' ? 'selected' : '' }}>AKUNTANSI & TAX MANAGEMENT</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="nominal" class="form-label">Nominal <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text">Rp</span>
                                                    <input type="text" class="form-control" id="nominal" name="nominal"
                                                           value="{{ old('nominal', number_format($pb->nominal, 0, ',', '.')) }}"
                                                           required placeholder="Contoh: 1.000.000">
                                                </div>
                                                <small class="form-text text-muted">Format: 1.000.000 (gunakan titik sebagai pemisah ribuan)</small>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="keterangan" class="form-label">Keterangan</label>
                                            <input type="text" class="form-control" id="keterangan" name="keterangan"
                                                   value="{{ old('keterangan', $pb->keterangan) }}" placeholder="Deskripsi singkat PB">
                                        </div>

                                        <div class="mb-3">
                                            <label for="keperluan" class="form-label">Keperluan</label>
                                            <textarea class="form-control" id="keperluan" name="keperluan" rows="4"
                                                      placeholder="Detail keperluan penggunaan dana">{{ old('keperluan', $pb->keperluan) }}</textarea>
                                        </div>

                                        <!-- File Management Section -->
                                        <div class="mb-3">
                                            <label class="form-label">File Lampiran</label>

                                            @if($pb->file_path && $pb->file_name)
                                                <!-- Current File Display -->
                                                <div class="current-file bg-light p-3 rounded mb-3" id="currentFileSection">
                                                    <h6 class="mb-2">File Saat Ini:</h6>
                                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                                        <div class="file-info flex-grow-1">
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
                                                                <i class="fas fa-download"></i> Download
                                                            </a>
                                                            @if(str_contains($pb->file_type, 'image'))
                                                                <a href="{{ asset('storage/' . $pb->file_path) }}"
                                                                   target="_blank"
                                                                   class="btn btn-sm btn-success"
                                                                   title="Lihat Gambar"
                                                                   style="color: white; background-color: #28a745;">
                                                                    <i class="fas fa-image"></i> Lihat
                                                                </a>
                                                            @endif
                                                            @if(auth()->user()->role === 'admin')
                                                                <button type="button"
                                                                        class="btn btn-sm btn-outline-danger"
                                                                        onclick="deleteFile({{ $pb->id }})"
                                                                        title="Hapus File (Admin Only)">
                                                                    <i class="fas fa-trash"></i> Hapus
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- Upload New/Replace File -->
                                            <div class="upload-section">
                                                <label for="file" class="form-label">
                                                    @if($pb->file_path)
                                                        Ganti File (Opsional)
                                                    @else
                                                        Upload File Baru (Opsional)
                                                    @endif
                                                </label>
                                                <input type="file"
                                                       name="file"
                                                       id="file"
                                                       class="form-control"
                                                       accept=".pdf,.jpg,.jpeg,.png,.xlsx,.xls,.doc,.docx">
                                                <div class="form-text">
                                                    Format yang didukung: PDF, JPG, PNG, Excel (XLSX/XLS), Word (DOC/DOCX). Maksimal 10MB.
                                                    @if($pb->file_path)
                                                        <br><strong>Catatan:</strong> File baru akan menggantikan file yang ada.
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('pb.show', $pb->id) }}" class="btn btn-secondary">
                                                <i class="fas fa-times"></i> Batal
                                            </a>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save"></i> Update PB
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Informasi PB</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Nomor PB:</label>
                                        <p class="mb-1">{{ $pb->nomor_pb ?? 'N/A' }}</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Dibuat:</label>
                                        <p class="mb-1">{{ $pb->created_at->format('d F Y H:i') }}</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Terakhir Update:</label>
                                        <p class="mb-1">{{ $pb->updated_at->format('d F Y H:i') }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="mb-0">Catatan</h5>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <strong>Perhatian:</strong>
                                        <ul class="mb-0 mt-2">
                                            <li>Pastikan data yang diubah sudah benar</li>
                                            @if(auth()->user()->role !== 'admin')
                                                <li>Anda hanya bisa mengubah data, bukan status</li>
                                            @else
                                                <li>Sebagai admin, Anda dapat mengubah status PB</li>
                                            @endif
                                            <li>Perubahan akan tersimpan secara permanen</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Preview Modal -->
    <div class="modal fade" id="imagePreviewModal" tabindex="-1" role="dialog" aria-labelledby="imagePreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imagePreviewModalLabel">Preview File</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <img id="previewImage" src="" alt="Preview" class="img-fluid" style="max-height: 500px;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // Format number input with automatic thousand separators for edit form
    document.addEventListener('DOMContentLoaded', function() {
        const nominalInput = document.getElementById('nominal');

        if (nominalInput) {
            console.log('Setting up auto-format for edit form...');

            // Format input on typing
            nominalInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, ''); // Remove all non-digits
                if (value) {
                    // Format with dots as thousand separators
                    let formattedValue = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                    e.target.value = formattedValue;
                }
            });

            // Remove formatting before form submission
            document.querySelector('form').addEventListener('submit', function(e) {
                console.log('Form submitting, removing format from nominal...');
                nominalInput.value = nominalInput.value.replace(/\./g, ''); // Remove dots for submission
            });
        }
    });

    async function deleteFile(pbId) {
        if (!confirm('Apakah Anda yakin ingin menghapus file ini? Tindakan ini tidak dapat dibatalkan.')) {
            return;
        }

        try {
            const response = await fetch(`/pb/${pbId}/delete-file`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();

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

    // File validation
    document.getElementById('file').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const fileSize = file.size / 1024 / 1024; // Convert to MB
            if (fileSize > 10) {
                alert('Ukuran file tidak boleh lebih dari 10MB');
                e.target.value = '';
                return;
            }

            const allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg',
                                'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];

            if (!allowedTypes.includes(file.type)) {
                alert('Tipe file tidak diizinkan. Hanya PDF, gambar, Excel, dan Word yang diperbolehkan.');
                e.target.value = '';
                return;
            }
        }
    });
</script>
@endsection
