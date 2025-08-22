<?php

namespace App\Http\Controllers;

use App\Models\Pbs;
use App\Exports\PbsExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class PbsController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Build query directly instead of using PbService
        $query = Pbs::with(['user', 'cancelledBy']);
        
        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_pb', 'LIKE', "%{$search}%")
                  ->orWhere('keterangan', 'LIKE', "%{$search}%")
                  ->orWhere('divisi', 'LIKE', "%{$search}%")
                  ->orWhere('nominal', 'LIKE', "%{$search}%")
                  ->orWhereDate('tanggal', 'LIKE', "%{$search}%");
            });
        }

        // Non-admin users can only see their own PBs
        if ($user->role !== 'admin') {
            $query->where('user_id', $user->id);
        }

        // Apply other filters
        if ($request->filled('divisi')) {
            $query->where('divisi', $request->divisi);
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('status', 'active');
            } elseif ($request->status === 'cancelled') {
                $query->where('status', 'cancelled');
            }
        }

        if ($request->filled('date')) {
            $query->whereDate('tanggal', $request->date);
        }

        // Get PBs
        $pbs = $query->orderBy('created_at', 'desc')->get();

        // Get available divisions for filter
        $divisions = ['E-CHANNEL', 'TREASURY OPERASIONAL', 'LAYANAN OPERASIONAL', 'AKUNTANSI & TAX MANAGEMENT'];

        return view('pb.index-working', compact('pbs', 'divisions'));
    }

    public function create()
    {
        // Simple permission check without PbService
        $userRole = auth()->user()->role ?? 'user';
        $today = \Carbon\Carbon::today();
        
        // Only allow creating PBs for today
        // if (!$today->isSameDay($today)) {
        //     return redirect()->route('pb.index')->with('error', 'Tidak dapat membuat PB di luar tanggal hari ini');
        // }

        return view('pb.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'penginput' => 'required|string',
            'nominal' => 'required|numeric|min:1',
            'keterangan' => 'nullable|string',
            'divisi' => 'required|string|in:E-CHANNEL,TREASURY OPERASIONAL,LAYANAN OPERASIONAL,AKUNTANSI & TAX MANAGEMENT',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,xlsx,xls,doc,docx|max:10240', // 10MB max
        ]);

        try {
            // Create PB directly without service
            $data = $request->all();
            
            // Auto-generate nomor PB
            $nomorPb = \App\Models\PbCounter::getNextNumber($data['tanggal'] ?? null);
            
            // Set input date to today
            $data['nomor_pb'] = $nomorPb;
            $data['input_date'] = \Carbon\Carbon::today();
            $data['user_id'] = auth()->user()->id;
            $data['status'] = 'active';
            
            // Handle file upload if present
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('pb_files', $fileName, 'public');
                
                $data['file_path'] = $filePath;
                $data['file_name'] = $file->getClientOriginalName();
                $data['file_type'] = $file->getClientMimeType();
                $data['file_size'] = $file->getSize();
            }
            
            // Remove file from data as it's not a database field
            unset($data['file']);
            
            $pb = Pbs::create($data);

            return redirect()->route('pb.index')->with('success', "PB berhasil dibuat dengan nomor: {$pb->nomor_pb}");

        } catch (\Exception $e) {
            Log::error('PB Creation Error: ' . $e->getMessage());
            return back()->withErrors(['Gagal membuat PB: ' . $e->getMessage()]);
        }
    }

    public function show($id)
    {
        try {
            $pb = Pbs::with(['user', 'cancelledBy'])->findOrFail($id);

            // Check if user can view this PB
            $user = auth()->user();
            if ($user->role !== 'admin' && $pb->user_id !== $user->id) {
                abort(403, 'Unauthorized access.');
            }

            return view('pb.show', compact('pb'));

        } catch (\Exception $e) {
            return redirect()->route('pb.index')->with('error', 'PB tidak ditemukan atau terjadi kesalahan.');
        }
    }

    public function edit($id)
    {
        try {
            $pb = Pbs::findOrFail($id);

            // Check if user can edit this PB
            $userRole = auth()->user()->role ?? 'user';
            if (!$pb->canBeEditedBy($userRole)) {
                return redirect()->route('pb.index')->with('error', 'Tidak dapat mengubah PB dari tanggal sebelumnya');
            }

            // Check ownership for non-admin
            $user = auth()->user();
            if ($user->role !== 'admin' && $pb->user_id !== $user->id) {
                abort(403, 'Unauthorized access.');
            }

            // Check if user can edit this PB (with safe user_id check)
            $user = auth()->user();
            try {
                if ($user->role !== 'admin' && Schema::hasColumn('pbs', 'user_id') && $pb->user_id !== $user->id) {
                    abort(403, 'Unauthorized access.');
                }
            } catch (\Exception $e) {
                // If user_id column doesn't exist, allow access
            }

            return view('pb.edit', compact('pb'));

        } catch (\Exception $e) {
            return redirect()->route('pb.index')->with('error', 'PB tidak ditemukan atau terjadi kesalahan.');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'penginput' => 'required|string',
            'nominal' => 'required|numeric|min:1',
            'keterangan' => 'nullable|string',
            'divisi' => 'required|string|in:E-CHANNEL,TREASURY OPERASIONAL,LAYANAN OPERASIONAL,AKUNTANSI & TAX MANAGEMENT',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,xlsx,xls,doc,docx|max:10240', // 10MB max
        ]);

        try {
            $pbService = new PbService();
            $pb = Pbs::findOrFail($id);
            $userRole = auth()->user()->role ?? 'user';

            // Use service to update PB
            $pbService->updatePb($pb, $request->all(), $userRole);

            return redirect()->route('pb.index')->with('success', 'Data PB berhasil diupdate.');

        } catch (\Exception $e) {
            Log::error('PB Update Error: ' . $e->getMessage());
            return back()->withErrors(['Gagal mengupdate PB: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Cancel PB (instead of delete)
     */
    public function cancel(Request $request, $id)
    {
        $request->validate([
            'cancel_reason' => 'nullable|string|max:255'
        ]);

        try {
            $pbService = new PbService();
            $pb = Pbs::findOrFail($id);
            $userRole = auth()->user()->role ?? 'user';

            // Use service to cancel PB
            $pbService->cancelPb($pb, $request->cancel_reason, $userRole);

            return redirect()->route('pb.index')->with('success', "PB {$pb->nomor_pb} berhasil dibatalkan.");

        } catch (\Exception $e) {
            Log::error('PB Cancel Error: ' . $e->getMessage());
            return back()->withErrors(['Gagal membatalkan PB: ' . $e->getMessage()]);
        }
    }

    /**
     * Restore cancelled PB (admin only)
     */
    public function restore($id)
    {
        try {
            $pbService = new PbService();
            $pb = Pbs::findOrFail($id);
            $userRole = auth()->user()->role ?? 'user';

            // Use service to restore PB
            $pbService->restorePb($pb, $userRole);

            return redirect()->route('pb.index')->with('success', "PB {$pb->nomor_pb} berhasil dikembalikan.");

        } catch (\Exception $e) {
            Log::error('PB Restore Error: ' . $e->getMessage());
            return back()->withErrors(['Gagal mengembalikan PB: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        // Destroy method now redirects to cancel
        // This maintains backward compatibility while implementing soft delete
        return $this->cancel(request(), $id);
    }

    // Laporan Bulanan - PERBAIKAN
    public function laporanBulanan(Request $request)
    {
        try {
            // Default ke bulan dan tahun saat ini
            $bulan = $request->get('bulan', now()->format('m'));
            $tahun = $request->get('tahun', now()->format('Y'));

            // Debug: untuk memastikan parameter diterima
            Log::info('Filter Bulanan - Bulan: ' . $bulan . ', Tahun: ' . $tahun);

            $query = Pbs::query();

            // Apply filters
            $query->whereMonth('tanggal', $bulan)
                  ->whereYear('tanggal', $tahun);

            // Try to filter by user if not admin and user_id column exists
            $user = auth()->user();
            try {
                if ($user->role !== 'admin' && Schema::hasColumn('pbs', 'user_id')) {
                    $query->where('user_id', $user->id);
                }
            } catch (\Exception $e) {
                // If user_id column doesn't exist, show all PBs
            }

            // Try to load user relationship if it exists
            try {
                if (Schema::hasColumn('pbs', 'user_id')) {
                    $pbs = $query->with('user')->orderBy('tanggal', 'desc')->get();
                } else {
                    $pbs = $query->orderBy('tanggal', 'desc')->get();
                }
            } catch (\Exception $e) {
                // If user relationship doesn't exist, load without it
                $pbs = $query->orderBy('tanggal', 'desc')->get();
            }

            // Debug: untuk memastikan query berjalan
            Log::info('Jumlah data ditemukan: ' . $pbs->count());

            return view('pb.laporan-bulanan-final', compact('pbs', 'bulan', 'tahun'));

        } catch (\Exception $e) {
            Log::error('Error in laporanBulanan: ' . $e->getMessage());
            return redirect()->route('pb.index')->with('error', 'Terjadi kesalahan saat memuat laporan bulanan.');
        }
    }

    // Laporan Mingguan - PERBAIKAN
    public function laporanMingguan(Request $request)
    {
        try {
            // Default ke awal dan akhir minggu ini
            $tanggal_awal = $request->get('tanggal_awal', now()->startOfWeek()->format('Y-m-d'));
            $tanggal_akhir = $request->get('tanggal_akhir', now()->endOfWeek()->format('Y-m-d'));

            // Debug: untuk memastikan parameter diterima
            Log::info('Filter Mingguan - Awal: ' . $tanggal_awal . ', Akhir: ' . $tanggal_akhir);

            $query = Pbs::query();

            // Apply date range filter
            $query->whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir]);

            // Try to filter by user if not admin and user_id column exists
            $user = auth()->user();
            try {
                if ($user->role !== 'admin' && Schema::hasColumn('pbs', 'user_id')) {
                    $query->where('user_id', $user->id);
                }
            } catch (\Exception $e) {
                // If user_id column doesn't exist, show all PBs
            }

            // Try to load user relationship if it exists
            try {
                if (Schema::hasColumn('pbs', 'user_id')) {
                    $pbs = $query->with('user')->orderBy('tanggal', 'desc')->get();
                } else {
                    $pbs = $query->orderBy('tanggal', 'desc')->get();
                }
            } catch (\Exception $e) {
                // If user relationship doesn't exist, load without it
                $pbs = $query->orderBy('tanggal', 'desc')->get();
            }

            // Debug: untuk memastikan query berjalan
            Log::info('Jumlah data ditemukan: ' . $pbs->count());

            return view('pb.laporan-mingguan-final', compact('pbs', 'tanggal_awal', 'tanggal_akhir'));

        } catch (\Exception $e) {
            Log::error('Error in laporanMingguan: ' . $e->getMessage());
            return redirect()->route('pb.index')->with('error', 'Terjadi kesalahan saat memuat laporan mingguan.');
        }
    }

    // Export Excel dengan filter
    public function exportExcel(Request $request)
    {
        try {
            $filters = [
                'bulan' => $request->get('bulan'),
                'tahun' => $request->get('tahun'),
                'tanggal_awal' => $request->get('tanggal_awal'),
                'tanggal_akhir' => $request->get('tanggal_akhir'),
                'divisi' => $request->get('divisi'),
            ];

            $filename = 'Data_PB_' . date('Y-m-d_H-i-s') . '.xlsx';

            return Excel::download(new PbsExport($filters), $filename);

        } catch (\Exception $e) {
            Log::error('Error in exportExcel: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengexport data Excel: ' . $e->getMessage());
        }
    }

    // Export PDF dengan filter
    public function exportPdf(Request $request)
    {
        try {
            $query = Pbs::query();
            $periode = '';
            $divisi_filter = $request->get('divisi');

            // Apply filters
            if ($request->filled('bulan') && $request->filled('tahun')) {
                $query->whereMonth('tanggal', $request->bulan)
                      ->whereYear('tanggal', $request->tahun);
                $periode = 'Bulan ' . str_pad($request->bulan, 2, '0', STR_PAD_LEFT) . '/' . $request->tahun;
            }

            if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
                $query->whereBetween('tanggal', [$request->tanggal_awal, $request->tanggal_akhir]);
                $periode = \Carbon\Carbon::parse($request->tanggal_awal)->format('d/m/Y') . ' - ' .
                          \Carbon\Carbon::parse($request->tanggal_akhir)->format('d/m/Y');
            }

            if ($request->filled('divisi')) {
                $query->where('divisi', $request->divisi);
            }

            // Try to filter by user if not admin and user_id column exists
            $user = auth()->user();
            try {
                if ($user->role !== 'admin' && Schema::hasColumn('pbs', 'user_id')) {
                    $query->where('user_id', $user->id);
                }
            } catch (\Exception $e) {
                // If user_id column doesn't exist, show all PBs
            }

            // Try to load user relationship if it exists
            try {
                if (Schema::hasColumn('pbs', 'user_id')) {
                    $pbs = $query->with('user')->orderBy('tanggal', 'desc')->get();
                } else {
                    $pbs = $query->orderBy('tanggal', 'desc')->get();
                }
            } catch (\Exception $e) {
                // If user relationship doesn't exist, load without it
                $pbs = $query->orderBy('tanggal', 'desc')->get();
            }

            $pdf = Pdf::loadView('pb.export-pdf', compact('pbs', 'periode', 'divisi_filter'))
                      ->setPaper('a4', 'landscape')
                      ->setOptions([
                          'defaultFont' => 'DejaVu Sans',
                          'isHtml5ParserEnabled' => true,
                          'isRemoteEnabled' => true,
                      ]);

            $filename = 'Laporan_PB_' . date('Y-m-d_H-i-s') . '.pdf';

            return $pdf->download($filename);

        } catch (\Exception $e) {
            Log::error('Error in exportPdf: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengexport data PDF: ' . $e->getMessage());
        }
    }

    /**
     * Download file attachment
     */
    public function downloadFile($id)
    {
        try {
            $pb = Pbs::findOrFail($id);
            
            // Check if user can access this PB
            $user = auth()->user();
            if ($user->role !== 'admin' && $pb->user_id !== $user->id) {
                abort(403, 'Unauthorized access.');
            }

            if (!$pb->file_path || !Storage::disk('public')->exists($pb->file_path)) {
                return redirect()->back()->with('error', 'File tidak ditemukan.');
            }

            $filePath = storage_path('app/public/' . $pb->file_path);
            return response()->download($filePath, $pb->file_name);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengunduh file: ' . $e->getMessage());
        }
    }

    /**
     * Delete file attachment (admin only)
     */
    public function deleteFile($id)
    {
        try {
            $pbService = new PbService();
            $pb = Pbs::findOrFail($id);
            $userRole = auth()->user()->role ?? 'user';

            $pbService->deleteFile($pb, $userRole);

            return response()->json(['success' => true, 'message' => 'File berhasil dihapus']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus file: ' . $e->getMessage()], 500);
        }
    }
}
