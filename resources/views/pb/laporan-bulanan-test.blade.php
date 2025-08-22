<!DOCTYPE html>
<html>
<head>
    <title>Test Laporan Bulanan</title>
</head>
<body>
    <h1>Test Laporan Bulanan</h1>
    <p>Ini adalah test sederhana untuk laporan bulanan</p>
    <p>Controller berhasil diakses!</p>

    <h2>Data yang diterima:</h2>
    <ul>
        <li>Total PB: {{ $stats['total_pb'] ?? 'N/A' }}</li>
        <li>PB Aktif: {{ $stats['total_aktif'] ?? 'N/A' }}</li>
        <li>PB Batal: {{ $stats['total_batal'] ?? 'N/A' }}</li>
        <li>Periode: {{ $periode ?? 'N/A' }}</li>
    </ul>

    @if(isset($pbs) && $pbs->count() > 0)
        <h3>Data PB ({{ $pbs->count() }} item):</h3>
        <table border="1">
            <tr>
                <th>Nomor PB</th>
                <th>Tanggal</th>
                <th>Status</th>
                <th>Nominal</th>
            </tr>
            @foreach($pbs->take(5) as $pb)
            <tr>
                <td>{{ $pb->nomor_pb }}</td>
                <td>{{ $pb->tanggal }}</td>
                <td>{{ $pb->status }}</td>
                <td>{{ number_format($pb->nominal) }}</td>
            </tr>
            @endforeach
        </table>
    @else
        <p>Tidak ada data PB</p>
    @endif
</body>
</html>
