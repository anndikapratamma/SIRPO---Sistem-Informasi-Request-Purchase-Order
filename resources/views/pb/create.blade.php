@extends('layouttempalte.master')

@section('title', 'Tambah PB - SIRPO')

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid py-4">
            <div class="row mb-4">
                <div class="col-12">
                    <h1 class="h3 text-dark">Buat PB Baru</h1>
                    <p class="text-muted">Input permintaan bayar dengan mudah - Bisa input multiple PB sekaligus</p>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-plus-circle me-2"></i>Form Input PB
                            </h5>
                            <div class="d-flex align-items-center gap-2">
                                <form method="GET" action="{{ route('pb.create') }}" class="d-flex align-items-center gap-2">
                                    <label class="form-label mb-0 fw-bold">Jumlah PB:</label>
                                    <select name="count" class="form-select form-select-sm" style="width: 80px;" onchange="this.form.submit()">
                                        @for($i = 1; $i <= 10; $i++)
                                            <option value="{{ $i }}" {{ request('count', 1) == $i ? 'selected' : '' }}>{{ $i }}</option>
                                        @endfor
                                    </select>
                                </form>
                                <span class="badge bg-success">Total: {{ request('count', 1) }} PB</span>
                            </div>
                        </div>
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if(session('date_request_submitted'))
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    Request untuk tanggal masa depan telah dikirim ke admin. Anda akan diberitahu jika disetujui.
                                </div>
                            @endif

                            <form method="POST" action="{{ route('pb.store.bulk') }}" enctype="multipart/form-data" id="bulkPbForm">
                                @csrf

                                <!-- Common Fields -->
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                                            <input type="date" id="commonDate" name="common_date" class="form-control"
                                                   value="{{ old('common_date', date('Y-m-d')) }}"
                                                   max="{{ date('Y-m-d') }}" required>
                                            <small class="form-text text-muted">
                                                Hanya bisa pilih tanggal hari ini atau sebelumnya.
                                                <a href="#" id="requestFutureDate">Request tanggal masa depan?</a>
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Penginput</label>
                                            <input type="text" name="penginput" class="form-control" value="{{ Auth::user()->name }}" readonly>
                                            <small class="form-text text-muted">Otomatis terisi sesuai user yang login</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Copy Data ke Semua PB</label>
                                            <div class="d-flex gap-2">
                                                <button type="button" onclick="copyDateToAll()" class="btn btn-outline-secondary btn-sm">Copy Tanggal</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <!-- PB Items Container -->
                                <div id="pbItemsContainer">
                                    @php
                                        $pbCount = request('count', 1);
                                        $today = date('Y-m-d');
                                    @endphp

                                    @for($i = 0; $i < $pbCount; $i++)
                                    <div class="pb-item border rounded p-3 mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="mb-0"><i class="fas fa-file-invoice-dollar"></i> PB #{{ $i + 1 }}</h6>
                                            @if($pbCount > 1)
                                            <button type="button" onclick="removePb(this)" class="btn btn-outline-danger btn-sm">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                            @endif
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                                                    <input type="date" name="pbs[{{ $i }}][tanggal]" class="form-control pb-date"
                                                           value="{{ old('pbs.'.$i.'.tanggal', $today) }}"
                                                           max="{{ $today }}" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Unit <span class="text-danger">*</span></label>
                                                    <select name="pbs[{{ $i }}][divisi]" class="form-select pb-divisi" required>
                                                        <option value="">-- Pilih Unit --</option>
                                                        <option value="E-CHANNEL" {{ (old('pbs.'.$i.'.divisi') ?: auth()->user()->divisi) == 'E-CHANNEL' ? 'selected' : '' }}>E-CHANNEL</option>
                                                        <option value="TREASURY OPERASIONAL" {{ (old('pbs.'.$i.'.divisi') ?: auth()->user()->divisi) == 'TREASURY OPERASIONAL' ? 'selected' : '' }}>TREASURY OPERASIONAL</option>
                                                        <option value="LAYANAN OPERASIONAL" {{ (old('pbs.'.$i.'.divisi') ?: auth()->user()->divisi) == 'LAYANAN OPERASIONAL' ? 'selected' : '' }}>LAYANAN OPERASIONAL</option>
                                                        <option value="AKUNTANSI & TAX MANAGEMENT" {{ (old('pbs.'.$i.'.divisi') ?: auth()->user()->divisi) == 'AKUNTANSI & TAX MANAGEMENT' ? 'selected' : '' }}>AKUNTANSI & TAX MANAGEMENT</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Nominal <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">Rp</span>
                                                        <input type="text" name="pbs[{{ $i }}][nominal]" class="form-control nominal-input"
                                                               value="{{ old('pbs.'.$i.'.nominal') }}" placeholder="Contoh: 1.000.000"
                                                               oninput="formatRupiah(this)" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Upload File <small class="text-muted">(Optional)</small></label>
                                                    <input type="file" name="pbs[{{ $i }}][file]" class="form-control"
                                                           accept=".pdf,.jpg,.jpeg,.png,.xlsx,.xls,.doc,.docx">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Keterangan</label>
                                            <textarea name="pbs[{{ $i }}][keterangan]" class="form-control" rows="3"
                                                      placeholder="Masukkan keterangan permintaan bayar">{{ old('pbs.'.$i.'.keterangan') }}</textarea>
                                        </div>
                                    </div>
                                    @endfor
                                </div>

                                <div class="d-flex justify-content-between align-items-center mt-4">
                                    <div>
                                        <span class="text-muted">Total PB: <strong>{{ $pbCount }}</strong></span>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-save me-2"></i>Simpan Semua PB
                                        </button>
                                        <a href="{{ route('pb.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left me-2"></i>Kembali
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Request Future Date -->
    <div class="modal fade" id="futureDateModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Request Tanggal Masa Depan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('pb.request-future-date') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Tanggal yang Diminta <span class="text-danger">*</span></label>
                            <input type="date" name="requested_date" class="form-control"
                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Alasan <span class="text-danger">*</span></label>
                            <textarea name="reason" class="form-control" rows="3"
                                      placeholder="Jelaskan mengapa perlu input PB untuk tanggal masa depan" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Kirim Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Format nominal rupiah
        function formatRupiah(input) {
            let value = input.value.replace(/\D/g, '');
            if (value) {
                input.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }
        }

        // Remove PB form
        function removePb(button) {
            console.log('removePb function called'); // Debug
            console.log('Button:', button); // Debug

            if (confirm('Hapus PB ini?')) {
                const pbItem = button.closest('.pb-item');
                console.log('PB Item to remove:', pbItem); // Debug

                if (pbItem) {
                    pbItem.remove();
                    updatePbNumbers();
                    console.log('PB removed successfully'); // Debug
                } else {
                    console.error('Could not find .pb-item to remove');
                }
            }
        }

        // Update PB numbers after removal
        function updatePbNumbers() {
            console.log('updatePbNumbers called'); // Debug
            const items = document.querySelectorAll('.pb-item');
            console.log('Found items:', items.length); // Debug

            items.forEach((item, index) => {
                const header = item.querySelector('h6');
                if (header) {
                    header.innerHTML = `<i class="fas fa-file-invoice-dollar"></i> PB #${index + 1}`;
                }

                // Update input names
                const inputs = item.querySelectorAll('input, select, textarea');
                inputs.forEach(input => {
                    if (input.name && input.name.includes('pbs[')) {
                        const oldName = input.name;
                        input.name = input.name.replace(/pbs\[\d+\]/, `pbs[${index}]`);
                        console.log(`Updated name: ${oldName} -> ${input.name}`); // Debug
                    }
                });
            });

            // Update total count in badge
            const totalBadge = document.querySelector('.badge.bg-success');
            if (totalBadge) {
                totalBadge.textContent = `Total: ${items.length} PB`;
            }

            // Hide/show remove buttons based on count
            const removeButtons = document.querySelectorAll('.btn-outline-danger');
            removeButtons.forEach(btn => {
                if (btn.textContent.includes('Hapus')) {
                    if (items.length <= 1) {
                        btn.style.display = 'none';
                    } else {
                        btn.style.display = 'inline-block';
                    }
                }
            });
        }

        // Copy date to all PB
        function copyDateToAll() {
            const commonDate = document.getElementById('commonDate').value;
            if (commonDate) {
                document.querySelectorAll('.pb-date').forEach(input => {
                    input.value = commonDate;
                });
                alert('Tanggal berhasil dicopy ke semua PB!');
            } else {
                alert('Pilih tanggal terlebih dahulu!');
            }
        }

        // Form submission - clean nominal format
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded'); // Debug

            const form = document.getElementById('bulkPbForm');
            if (form) {
                form.addEventListener('submit', function(e) {
                    document.querySelectorAll('.nominal-input').forEach(input => {
                        input.value = input.value.replace(/\./g, '');
                    });
                });
            }

            // Alternative event binding for remove buttons (backup method)
            document.addEventListener('click', function(e) {
                if (e.target.closest('.btn-outline-danger') && e.target.closest('.btn-outline-danger').onclick) {
                    // Button already has onclick, let it handle
                    return;
                }

                // Fallback for buttons without onclick
                if (e.target.closest('.btn-outline-danger') && e.target.closest('.btn-outline-danger').textContent.includes('Hapus')) {
                    console.log('Fallback remove button clicked'); // Debug
                    removePb(e.target.closest('.btn-outline-danger'));
                }
            });
        });
    </script>
@endsection
