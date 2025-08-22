{{-- Partial untuk menampilkan tabel PB dengan role-based access --}}
<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="15%">Nomor PB</th>
                @if(Auth::user()->role === 'admin' || Auth::user()->is_admin)
                <th width="12%">Tanggal</th>
                @endif
                <th width="15%">Penginput</th>
                @if(Auth::user()->role === 'admin' || Auth::user()->is_admin)
                <th width="15%">Nominal</th>
                <th width="28%">Keterangan</th>
                @endif
                <th width="10%">Divisi</th>
                @if(Auth::user()->role === 'admin' || Auth::user()->is_admin)
                <th width="8%">Hari</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @php
                $total = 0;
                $groupedByDivisi = $pbs->groupBy('divisi');
            @endphp

            @forelse($pbs as $index => $pb)
                @php $total += $pb->nominal; @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="fw-bold"><small>{{ $pb->nomor_pb }}</small></td>
                    @if(Auth::user()->role === 'admin' || Auth::user()->is_admin)
                    <td>{{ \Carbon\Carbon::parse($pb->tanggal)->format('d/m/Y') }}</td>
                    @endif
                    <td>{{ $pb->penginput }}</td>
                    @if(Auth::user()->role === 'admin' || Auth::user()->is_admin)
                    <td class="text-end">
                        <strong>Rp {{ number_format($pb->nominal, 0, ',', '.') }}</strong>
                    </td>
                    <td>{{ $pb->keterangan ?: '-' }}</td>
                    @endif
                    <td class="text-center">
                        <span class="badge bg-{{ $pb->divisi == 'OP' ? 'primary' : 'success' }}">
                            {{ strtoupper($pb->divisi) }}
                        </span>
                    </td>
                    @if(Auth::user()->role === 'admin' || Auth::user()->is_admin)
                    <td><small>{{ \Carbon\Carbon::parse($pb->tanggal)->locale('id')->dayName }}</small></td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ (Auth::user()->role === 'admin' || Auth::user()->is_admin) ? '8' : '4' }}"
                        class="text-center py-4">
                        <div class="text-muted">
                            <i class="fas fa-inbox fa-2x mb-2"></i><br>
                            Tidak ada data ditemukan
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>

        @if($pbs->count() > 0 && (Auth::user()->role === 'admin' || Auth::user()->is_admin))
        <tfoot>
            <tr class="table-info">
                <th colspan="3" class="text-center">TOTAL KESELURUHAN</th>
                <th class="text-end">
                    <strong>Rp {{ number_format($total, 0, ',', '.') }}</strong>
                </th>
                <th colspan="3" class="text-center">
                    <small>{{ $pbs->count() }} item</small>
                </th>
            </tr>

            <!-- Summary per divisi jika ada data -->
            @if($groupedByDivisi->count() > 1)
                @foreach($groupedByDivisi as $divisi => $items)
                <tr class="table-secondary">
                    <th colspan="3" class="text-center">
                        Total {{ strtoupper($divisi) }}
                    </th>
                    <th class="text-end">
                        Rp {{ number_format($items->sum('nominal'), 0, ',', '.') }}
                    </th>
                    <th colspan="3" class="text-center">
                        <small>{{ $items->count() }} item</small>
                    </th>
                </tr>
                @endforeach
            @endif
        </tfoot>
        @endif
    </table>
</div>

@if($pbs->count() > 0)
<!-- Summary Cards -->
<div class="row mt-3">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h5>Total Data</h5>
                <h3>{{ $pbs->count() }}</h3>
                <small>PB Records</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h5>Total Nominal</h5>
                <h3>{{ number_format($total / 1000000, 1) }}M</h3>
                <small>Rp {{ number_format($total, 0, ',', '.') }}</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h5>Rata-rata</h5>
                <h3>{{ number_format($total / $pbs->count() / 1000, 0) }}K</h3>
                <small>Rp {{ number_format($total / $pbs->count(), 0, ',', '.') }}</small>
            </div>
        </div>
    </div>
</div>
@endif
