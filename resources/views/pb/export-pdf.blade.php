<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Export PDF PB</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            margin: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }

        .header h2 { margin: 0; color: #333; font-size: 18px; }
        .header p { margin: 5px 0; color: #666; }

        .info-box {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 20px;
        }
        .info-box strong { color: #495057; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 9px;
        }

        th, td {
            border: 1px solid #333;
            padding: 6px 4px;
            vertical-align: middle; /* biar isi cell selalu di tengah vertikal */
        }

        th {
            background-color: #e9ecef;
            font-weight: bold;
            text-align: center;
        }

        .text-center { text-align: center; }
        .text-right  { text-align: right; }

        /* Tambahan khusus kolom keterangan */
        .text-keterangan {
            text-align: center;   /* rata tengah horizontal */
            vertical-align: middle; /* rata tengah vertikal */
        }

        .total-row { background-color: #f8f9fa; font-weight: bold; }
        .footer { margin-top: 30px; text-align: right; font-size: 8px; color: #666; }
        .no-data { text-align: center; padding: 30px; color: #6c757d; font-style: italic; }

        .section-title {
            margin-top: 20px; margin-bottom: 10px; padding-bottom: 5px;
            font-size: 14px; font-weight: bold;
        }
        .section-title.aktif { color: #28a745; border-bottom: 2px solid #28a745; }
        .section-title.batal { color: #dc3545; border-bottom: 2px solid #dc3545; }

        .table-batal { background-color: #ffe6e6; }
        .table-batal th { background-color: #f8d7da; }

        .summary-box {
            margin-top: 30px; padding: 15px; border: 2px solid #007bff;
            background-color: #e3f2fd; border-radius: 5px;
        }
        .summary-title { color: #0056b3; margin: 0 0 10px 0; text-align: center; font-size: 14px; }
        .summary-table { width: 100%; border: none; }
        .summary-table td { border: none; padding: 5px; }
        .summary-total { background-color: #bbdefb; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN PERMINTAAN BAYAR (PB)</h2>
        <p>SIRPO - Sistem Informasi Request Purchase Order</p>
        @if(isset($periode))
            <p><strong>Periode: {{ $periode }}</strong></p>
        @endif
    </div>

    <div class="info-box">
        <strong>Informasi Laporan:</strong><br>
        Tanggal Cetak: {{ now()->format('d/m/Y H:i:s') }} <br>
        Total Data Aktif: {{ $pbsAktif->count() }} record <br>
        Total Data Dibatalkan: {{ $pbsBatal->count() }} record <br>
        Total Nominal Aktif: Rp {{ number_format($pbsAktif->sum('nominal')/100, 2, ',', '.') }} <br>
        @if(isset($divisi_filter) && $divisi_filter)
            Filter Divisi: {{ $divisi_filter == 'OP' ? 'Operasional' : 'Akuntansi' }} <br>
        @endif
    </div>

    <!-- Tabel PB Aktif -->
    <h3 class="section-title aktif">📊 Data PB Aktif ({{ $pbsAktif->count() }} record)</h3>

    @if($pbsAktif->count() > 0)
        <table>
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="12%">Nomor PB</th>
                    <th width="10%">Tanggal</th>
                    <th width="13%">Penginput</th>
                    <th width="13%">Nominal</th>
                    <th width="25%">Keterangan</th>
                    <th width="10%">Divisi</th>
                    <th width="12%">Status</th>
                </tr>
            </thead>
            <tbody>
                @php $totalAktif = 0; @endphp
                @foreach($pbsAktif as $index => $pb)
                    @php $totalAktif += $pb->nominal; @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">{{ $pb->nomor_pb }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($pb->tanggal)->format('d/m/Y') }}</td>
                        <td>{{ $pb->penginput }}</td>
                        <td class="text-right">Rp {{ number_format($pb->nominal/100, 2, ',', '.') }}</td>
                        <td class="text-keterangan">{{ $pb->keterangan }}</td>
                        <td class="text-center">{{ strtoupper($pb->divisi) }}</td>
                        <td class="text-center" style="color: #28a745; font-weight: bold;">AKTIF</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="4" class="text-center"><strong>TOTAL AKTIF</strong></td>
                    <td class="text-right"><strong>Rp {{ number_format($totalAktif/100, 2, ',', '.') }}</strong></td>
                    <td colspan="3"></td>
                </tr>
            </tfoot>
        </table>
    @else
        <div class="no-data"><p>Tidak ada data PB aktif untuk ditampilkan.</p></div>
    @endif

    <!-- Tabel PB Dibatalkan -->
    <h3 class="section-title batal">❌ Data PB Dibatalkan ({{ $pbsBatal->count() }} record)</h3>

    @if($pbsBatal->count() > 0)
        <table class="table-batal">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="12%">Nomor PB</th>
                    <th width="10%">Tanggal</th>
                    <th width="13%">Penginput</th>
                    <th width="13%">Nominal</th>
                    <th width="25%">Keterangan</th>
                    <th width="10%">Divisi</th>
                    <th width="12%">Status</th>
                </tr>
            </thead>
            <tbody>
                @php $totalBatal = 0; @endphp
                @foreach($pbsBatal as $index => $pb)
                    @php $totalBatal += $pb->nominal; @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">{{ $pb->nomor_pb }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($pb->tanggal)->format('d/m/Y') }}</td>
                        <td>{{ $pb->penginput }}</td>
                        <td class="text-right">Rp {{ number_format($pb->nominal/100, 2, ',', '.') }}</td>
                        <td class="text-keterangan" style="color: #721c24;">
                            {{ $pb->keterangan }}
                            @if($pb->cancel_reason)
                                <br><small style="color: #dc3545; font-style: italic;">Dibatal: {{ $pb->cancel_reason }}</small>
                            @endif
                        </td>
                        <td class="text-center">{{ strtoupper($pb->divisi) }}</td>
                        <td class="text-center" style="color: #dc3545; font-weight: bold;">DIBATALKAN</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background-color: #f5c6cb;">
                    <td colspan="4" class="text-center"><strong>TOTAL DIBATALKAN</strong></td>
                    <td class="text-right"><strong>Rp {{ number_format($totalBatal/100, 2, ',', '.') }}</strong></td>
                    <td colspan="3"></td>
                </tr>
            </tfoot>
        </table>
    @else
        <div class="no-data"><p>Tidak ada data PB yang dibatalkan.</p></div>
    @endif

    <!-- Summary Total -->
    @if($pbsAktif->count() > 0 || $pbsBatal->count() > 0)
        <div class="summary-box">
            <h4 class="summary-title">📋 RINGKASAN LAPORAN</h4>
            <table class="summary-table">
                <tr>
                    <td width="25%"><strong>Total PB Aktif:</strong></td>
                    <td width="25%">{{ $pbsAktif->count() }} record</td>
                    <td width="25%"><strong>Nominal Aktif:</strong></td>
                    <td width="25%">Rp {{ number_format($pbsAktif->sum('nominal')/100, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td><strong>Total PB Dibatalkan:</strong></td>
                    <td>{{ $pbsBatal->count() }} record</td>
                    <td><strong>Nominal Dibatalkan:</strong></td>
                    <td>Rp {{ number_format($pbsBatal->sum('nominal')/100, 2, ',', '.') }}</td>
                </tr>
                <tr class="summary-total">
                    <td style="padding: 8px;"><strong>GRAND TOTAL:</strong></td>
                    <td style="padding: 8px;">{{ $pbsAktif->count() + $pbsBatal->count() }} record</td>
                    <td style="padding: 8px;"><strong>TOTAL NOMINAL:</strong></td>
                    <td style="padding: 8px;">Rp {{ number_format(($pbsAktif->sum('nominal') + $pbsBatal->sum('nominal'))/100, 2, ',', '.') }}</td>
                </tr>
            </table>
        </div>
    @endif

    <div class="footer">
        <p>Generated by SIRPO System - {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
