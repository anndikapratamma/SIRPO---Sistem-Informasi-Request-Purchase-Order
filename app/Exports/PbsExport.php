<?php

namespace App\Exports;

use App\Models\Pbs;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PbsExport implements FromArray, WithHeadings, WithStyles
{
    protected $bulan;
    protected $tahun;
    protected $tanggal_awal;
    protected $tanggal_akhir;
    protected $divisi;

    public function __construct($filters = [])
    {
        $this->bulan = $filters['bulan'] ?? null;
        $this->tahun = $filters['tahun'] ?? null;
        $this->tanggal_awal = $filters['tanggal_awal'] ?? null;
        $this->tanggal_akhir = $filters['tanggal_akhir'] ?? null;
        $this->divisi = $filters['divisi'] ?? null;
    }

    public function array(): array
    {
        $baseQuery = Pbs::query();

        // Apply filters
        if ($this->bulan && $this->tahun) {
            $baseQuery->whereMonth('tanggal', $this->bulan)
                      ->whereYear('tanggal', $this->tahun);
        }

        if ($this->tanggal_awal && $this->tanggal_akhir) {
            $baseQuery->whereBetween('tanggal', [$this->tanggal_awal, $this->tanggal_akhir]);
        }

        if ($this->divisi) {
            $baseQuery->where('divisi', $this->divisi);
        }

        $user = auth()->user();
        if ($user->role !== 'admin') {
            $baseQuery->where('user_id', $user->id);
        }

        // Get active PBs
        $queryAktif = clone $baseQuery;
        $pbsAktif = $queryAktif->active()->orderBy('tanggal', 'desc')->get();

        // Get cancelled PBs
        $queryBatal = clone $baseQuery;
        $pbsBatal = $queryBatal->cancelled()->orderBy('tanggal', 'desc')->get();

        $data = [];

        // Header for active PBs
        $data[] = ['LAPORAN PB AKTIF', '', '', '', '', '', '', ''];
        $data[] = ['No', 'Nomor PB', 'Tanggal', 'Penginput', 'Nominal (Rp)', 'Keterangan', 'Divisi', 'Status'];

        // Active PBs data
        $no = 1;
        $totalAktif = 0;
        foreach ($pbsAktif as $pb) {
            $totalAktif += $pb->nominal;
            $data[] = [
                $no++,
                $pb->nomor_pb,
                $pb->tanggal,
                $pb->penginput,
                'Rp ' . number_format($pb->nominal / 100, 2, ',', '.'),
                $pb->keterangan,
                strtoupper($pb->divisi),
                $pb->status
            ];
        }

        // Total for active PBs
        $data[] = ['', '', '', 'TOTAL AKTIF', 'Rp ' . number_format($totalAktif / 100, 2, ',', '.'), '', '', ''];
        $data[] = ['', '', '', '', '', '', '', '']; // Empty row

        // Header for cancelled PBs
        $data[] = ['LAPORAN PB DIBATALKAN', '', '', '', '', '', '', ''];
        $data[] = ['No', 'Nomor PB', 'Tanggal', 'Penginput', 'Nominal (Rp)', 'Keterangan', 'Divisi', 'Status'];

        // Cancelled PBs data
        $no = 1;
        $totalBatal = 0;
        foreach ($pbsBatal as $pb) {
            $totalBatal += $pb->nominal;
            $data[] = [
                $no++,
                $pb->nomor_pb,
                $pb->tanggal,
                $pb->penginput,
                'Rp ' . number_format($pb->nominal / 100, 2, ',', '.'),
                $pb->keterangan,
                strtoupper($pb->divisi),
                $pb->status
            ];
        }

        // Total for cancelled PBs
        $data[] = ['', '', '', 'TOTAL DIBATALKAN', 'Rp ' . number_format($totalBatal / 100, 2, ',', '.'), '', '', ''];
        $data[] = ['', '', '', '', '', '', '', '']; // Empty row

        // Grand total
        $data[] = ['', '', '', 'GRAND TOTAL', 'Rp ' . number_format(($totalAktif + $totalBatal) / 100, 2, ',', '.'), '', '', ''];
        $data[] = ['', '', '', 'Total Record Aktif', $pbsAktif->count(), '', '', ''];
        $data[] = ['', '', '', 'Total Record Dibatalkan', $pbsBatal->count(), '', '', ''];
        $data[] = ['', '', '', 'Total Semua Record', $pbsAktif->count() + $pbsBatal->count(), '', '', ''];

        return $data;
    }

    public function headings(): array
    {
        return []; // No headings as we include them in the data
    }

    public function styles(Worksheet $sheet)
    {
        $styles = [];

        // Style for section headers
        $styles[1] = [
            'font' => ['bold' => true, 'size' => 14],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF28A745'] // Green
            ]
        ];

        // Style for table headers
        $styles[2] = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFCCCCCC']
            ]
        ];

        return $styles;
    }
}
