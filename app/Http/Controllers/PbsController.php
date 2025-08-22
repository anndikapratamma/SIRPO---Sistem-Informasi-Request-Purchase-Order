<?php

namespace App\Http\Controllers;

use App\Models\Pbs;
use App\Models\PbCounter;
use App\Models\PbDateRequest;
use App\Exports\PbsExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Services\NotificationService;

class PbsController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        $user = auth()->user();

        // Build query directly
        $query = Pbs::with(['user', 'cancelledBy']);

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_pb', 'LIKE', "%{$search}%")
                  ->orWhere('keterangan', 'LIKE', "%{$search}%")
                  ->orWhere('divisi', 'LIKE', "%{$search}%")
                  ->orWhere('nominal', 'LIKE', "%{$search}%")
                  ->orWhere('penginput', 'LIKE', "%{$search}%")
                  ->orWhereDate('tanggal', 'LIKE', "%{$search}%");
            });
        }

        // Non-admin users can see their own PBs + PBs created by admin
        // UPDATED: Allow all users to see all PBs (shared visibility)
        // Removed user filter - now all users can see all PBs regardless of who created them

        // Apply division filter
        if ($request->filled('divisi')) {
            $query->where('divisi', $request->divisi);
        }

        // Apply status filter
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('status', 'active');
            } elseif ($request->status === 'cancelled') {
                $query->where('status', 'cancelled');
            }
        }

        // Apply single date filter
        if ($request->filled('date')) {
            $query->whereDate('tanggal', $request->date);
        }

        // Apply date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('tanggal', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('tanggal', '<=', $request->date_to);
        }

        // Apply nominal range filter
        if ($request->filled('nominal_min')) {
            $query->where('nominal', '>=', $request->nominal_min);
        }
        if ($request->filled('nominal_max')) {
            $query->where('nominal', '<=', $request->nominal_max);
        }

        // Get PBs with proper ordering - newest first by ID and nomor_pb
        $pbs = $query->orderBy('id', 'desc')->orderBy('nomor_pb', 'desc')->get();

        // Get available divisions for filter
        $divisions = ['E-CHANNEL', 'TREASURY OPERASIONAL', 'LAYANAN OPERASIONAL', 'AKUNTANSI & TAX MANAGEMENT'];

        // Get summary statistics for current filters
        $summary = [
            'total' => $pbs->count(),
            'active' => $pbs->where('status', 'active')->count(),
            'cancelled' => $pbs->where('status', 'cancelled')->count(),
            'total_nominal' => $pbs->where('status', 'active')->sum('nominal'),
        ];

        return view('pb.index-working', compact('pbs', 'divisions', 'summary'));
    }

    public function create()
    {
        return view('pb.create');
    }

    public function store(Request $request)
    {
        // Bersihkan format nominal agar hanya angka
    $request->merge([
        'nominal' => str_replace('.', '', $request->nominal)
    ]);
        $request->validate([
            'tanggal' => 'required|date',
            'penginput' => 'required|string',
            'nominal' => 'required|numeric|min:1',
            'keterangan' => 'nullable|string',
            'divisi' => 'required|string|in:E-CHANNEL,TREASURY OPERASIONAL,LAYANAN OPERASIONAL,AKUNTANSI & TAX MANAGEMENT',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,xlsx,xls,doc,docx|max:10240',
        ]);

        try {
            DB::beginTransaction();

            // Generate unique nomor PB with concurrency handling
            $nomorPb = PbCounter::getNextNumber($request->tanggal);

            // Prepare data
            $data = $request->except('file');
            $data['nomor_pb'] = $nomorPb;
            $data['input_date'] = Carbon::today();
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

            $pb = Pbs::create($data);

            // Send notification to admins
            $this->notificationService->pbCreated($pb, auth()->user());

            DB::commit();

            return redirect()->route('pb.index')->with('success', "PB berhasil dibuat dengan nomor: {$pb->nomor_pb}");

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('PB Creation Error: ' . $e->getMessage());
            return back()->withErrors(['Gagal membuat PB: ' . $e->getMessage()])->withInput();
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

            // Get the position of this PB in the ordered list
            $query = Pbs::with(['user', 'cancelledBy']);

            // Apply same filtering as index for consistency
            if ($user->role !== 'admin') {
                $query->where(function($q) use ($user) {
                    $q->where('user_id', $user->id)
                      ->orWhereHas('user', function($subQuery) {
                          $subQuery->where('role', 'admin');
                      });
                });
            }

            $allPbs = $query->orderBy('created_at', 'desc')->get();
            $pbIndex = $allPbs->search(function($item) use ($id) {
                return $item->id == $id;
            });

            $pbNumber = $pbIndex !== false ? $pbIndex + 1 : 1;

            return view('pb.show', compact('pb', 'pbNumber'));

        } catch (\Exception $e) {
            return redirect()->route('pb.index')->with('error', 'PB tidak ditemukan atau terjadi kesalahan.');
        }
    }

    public function edit($id)
    {
        try {
            $pb = Pbs::findOrFail($id);

            // Check if user can edit this PB
            if (!$pb->canBeEditedBy(auth()->user()->role ?? 'user')) {
                return redirect()->route('pb.index')->with('error', 'Tidak dapat mengubah PB dari tanggal sebelumnya');
            }

            // Check ownership for non-admin
            $user = auth()->user();
            if ($user->role !== 'admin' && $pb->user_id !== $user->id) {
                abort(403, 'Unauthorized access.');
            }

            return view('pb.edit', compact('pb'));

        } catch (\Exception $e) {
            return redirect()->route('pb.index')->with('error', 'PB tidak ditemukan atau terjadi kesalahan.');
        }
    }

    public function update(Request $request, $id)
    {
        // Clean nominal before validation - remove dots for thousand separators
        $nominalValue = str_replace('.', '', $request->nominal);
        $request->merge(['nominal' => $nominalValue]);

        $request->validate([
            'tanggal' => 'required|date',
            'penginput' => 'required|string',
            'nominal' => 'required|numeric|min:1',
            'keterangan' => 'nullable|string',
            'divisi' => 'required|string|in:E-CHANNEL,TREASURY OPERASIONAL,LAYANAN OPERASIONAL,AKUNTANSI & TAX MANAGEMENT',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,xlsx,xls,doc,docx|max:10240',
        ]);

        try {
            $pb = Pbs::findOrFail($id);

            // Check if can edit
            if (!$pb->canBeEditedBy(auth()->user()->role ?? 'user')) {
                return redirect()->route('pb.index')->with('error', 'Tidak dapat mengubah PB dari tanggal sebelumnya');
            }

            // Prepare data
            $data = $request->except(['file', 'nomor_pb']);

            // Handle file upload if present
            if ($request->hasFile('file')) {
                // Delete old file if exists
                if ($pb->file_path) {
                    Storage::disk('public')->delete($pb->file_path);
                }

                $file = $request->file('file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('pb_files', $fileName, 'public');

                $data['file_path'] = $filePath;
                $data['file_name'] = $file->getClientOriginalName();
                $data['file_type'] = $file->getClientMimeType();
                $data['file_size'] = $file->getSize();
            }

            $pb->update($data);

            // Send notification about PB update
            $this->notificationService->pbUpdated($pb->fresh(), auth()->user());

            return redirect()->route('pb.index')->with('success', 'Data PB berhasil diupdate.');

        } catch (\Exception $e) {
            Log::error('PB Update Error: ' . $e->getMessage());
            return back()->withErrors(['Gagal mengupdate PB: ' . $e->getMessage()])->withInput();
        }
    }

    public function cancel(Request $request, $id)
    {
        $request->validate([
            'cancel_reason' => 'nullable|string|max:255'
        ]);

        try {
            $pb = Pbs::findOrFail($id);

            // Check if can edit
            if (!$pb->canBeEditedBy(auth()->user()->role ?? 'user')) {
                return redirect()->route('pb.index')->with('error', 'Tidak dapat membatalkan PB dari tanggal sebelumnya');
            }

            // Cancel PB
            $pb->cancel($request->cancel_reason, auth()->user()->id);

            // Send notification about PB cancellation
            $this->notificationService->pbCancelled($pb->fresh(), auth()->user(), $request->cancel_reason);

            return redirect()->route('pb.index')->with('success', "PB {$pb->nomor_pb} berhasil dibatalkan.");

        } catch (\Exception $e) {
            Log::error('PB Cancel Error: ' . $e->getMessage());
            return back()->withErrors(['Gagal membatalkan PB: ' . $e->getMessage()]);
        }
    }

    public function restore($id)
    {
        try {
            $pb = Pbs::findOrFail($id);
            $userRole = auth()->user()->role ?? 'user';

            if ($userRole !== 'admin') {
                return redirect()->route('pb.index')->with('error', 'Hanya admin yang dapat mengembalikan PB yang dibatalkan');
            }

            // Check if can edit
            if (!$pb->canBeEditedBy($userRole)) {
                return redirect()->route('pb.index')->with('error', 'Tidak dapat mengembalikan PB dari tanggal sebelumnya');
            }

            // Restore PB
            $pb->restore();

            // Send notification about PB restoration
            $this->notificationService->pbRestored($pb->fresh(), auth()->user());

            return redirect()->route('pb.index')->with('success', "PB {$pb->nomor_pb} berhasil dikembalikan.");

        } catch (\Exception $e) {
            Log::error('PB Restore Error: ' . $e->getMessage());
            return back()->withErrors(['Gagal mengembalikan PB: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        return $this->cancel(request(), $id);
    }

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

    public function deleteFile($id)
    {
        try {
            $pb = Pbs::findOrFail($id);
            $user = auth()->user();

            // Cek apakah user adalah admin atau pemilik PB
            if ($user->role !== 'admin' && $user->id != $pb->user_id) {
                return response()->json(['success' => false, 'message' => 'Anda tidak memiliki izin untuk menghapus file ini'], 403);
            }

            if ($pb->file_path) {
                // Hapus file dari storage
                Storage::disk('public')->delete($pb->file_path);

                // Update database
                $pb->update([
                    'file_path' => null,
                    'file_name' => null,
                    'file_type' => null,
                    'file_size' => null
                ]);

                return response()->json(['success' => true, 'message' => 'File berhasil dihapus']);
            } else {
                return response()->json(['success' => false, 'message' => 'Tidak ada file untuk dihapus'], 400);
            }

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus file: ' . $e->getMessage()], 500);
        }
    }

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

    public function exportPdf(Request $request)
    {
        try {
            $baseQuery = Pbs::query();
            $periode = '';
            $divisi_filter = $request->get('divisi');

            // Apply date filters to base query
            if ($request->filled('bulan') && $request->filled('tahun')) {
                $baseQuery->whereMonth('tanggal', $request->bulan)
                          ->whereYear('tanggal', $request->tahun);
                $periode = 'Bulan ' . str_pad($request->bulan, 2, '0', STR_PAD_LEFT) . '/' . $request->tahun;
            }

            if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
                $baseQuery->whereBetween('tanggal', [$request->tanggal_awal, $request->tanggal_akhir]);
                $periode = Carbon::parse($request->tanggal_awal)->format('d/m/Y') . ' - ' .
                          Carbon::parse($request->tanggal_akhir)->format('d/m/Y');
            }

            if ($request->filled('divisi')) {
                $baseQuery->where('divisi', $request->divisi);
            }

            $user = auth()->user();
            if ($user->role !== 'admin') {
                $baseQuery->where('user_id', $user->id);
            }

            // Get active PBs using model scope
            $queryAktif = clone $baseQuery;
            $pbsAktif = $queryAktif->active()
                                   ->with('user')
                                   ->orderBy('tanggal', 'desc')
                                   ->get();

            // Get cancelled PBs using model scope
            $queryBatal = clone $baseQuery;
            $pbsBatal = $queryBatal->cancelled()
                                   ->with('user')
                                   ->orderBy('tanggal', 'desc')
                                   ->get();

            $pdf = Pdf::loadView('pb.export-pdf', compact('pbsAktif', 'pbsBatal', 'periode', 'divisi_filter'))
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
     * Generate laporan bulanan
     */
    public function laporanBulanan(Request $request)
    {
        try {
            $tahun = $request->get('tahun', Carbon::now()->year);
            $bulan = $request->get('bulan', Carbon::now()->month);
            $divisi = $request->get('divisi');

            // Build query
            $query = Pbs::whereYear('tanggal', $tahun)
                        ->whereMonth('tanggal', $bulan);

            if ($divisi) {
                $query->where('divisi', $divisi);
            }

            // Filter by user role
            $user = auth()->user();
            if ($user->role !== 'admin') {
                $query->where('user_id', $user->id);
            }

            // Get data with relationships
            $pbs = $query->with(['user', 'cancelledBy'])
                         ->orderBy('tanggal', 'desc')
                         ->get();

            // Calculate statistics
            $stats = [
                'total_pb' => $pbs->count(),
                'total_aktif' => $pbs->where('status', 'active')->count(),
                'total_batal' => $pbs->where('status', 'cancelled')->count(),
                'total_nominal_aktif' => $pbs->where('status', 'active')->sum('nominal'),
                'total_nominal_batal' => $pbs->where('status', 'cancelled')->sum('nominal'),
            ];

            // Group by divisi
            $byDivisi = $pbs->groupBy('divisi')->map(function ($items, $key) {
                return [
                    'total' => $items->count(),
                    'aktif' => $items->where('status', 'active')->count(),
                    'batal' => $items->where('status', 'cancelled')->count(),
                    'nominal_aktif' => $items->where('status', 'active')->sum('nominal'),
                    'nominal_batal' => $items->where('status', 'cancelled')->sum('nominal'),
                ];
            });

            // Available options
            $divisions = ['E-CHANNEL', 'TREASURY OPERASIONAL', 'LAYANAN OPERASIONAL', 'AKUNTANSI & TAX MANAGEMENT'];
            $months = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];
            $years = range(2020, Carbon::now()->year + 1);

            $periode = $months[$bulan] . ' ' . $tahun;

            return view('pb.laporan-bulanan-final', compact(
                'pbs', 'stats', 'byDivisi', 'divisions', 'months', 'years',
                'tahun', 'bulan', 'divisi', 'periode'
            ));

        } catch (\Exception $e) {
            Log::error('Error in laporanBulanan: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memuat laporan bulanan: ' . $e->getMessage());
        }
    }

    /**
     * Generate laporan mingguan
     */
    public function laporanMingguan(Request $request)
    {
        try {
            $week = $request->get('week', Carbon::now()->week);
            $year = $request->get('year', Carbon::now()->year);
            $divisi = $request->get('divisi');

            // Calculate start and end of week
            $startOfWeek = Carbon::now()->setISODate($year, $week)->startOfWeek();
            $endOfWeek = Carbon::now()->setISODate($year, $week)->endOfWeek();

            // Build query
            $query = Pbs::whereBetween('tanggal', [$startOfWeek, $endOfWeek]);

            if ($divisi) {
                $query->where('divisi', $divisi);
            }

            // Filter by user role
            $user = auth()->user();
            if ($user->role !== 'admin') {
                $query->where('user_id', $user->id);
            }

            // Get data with relationships
            $pbs = $query->with(['user', 'cancelledBy'])
                         ->orderBy('tanggal', 'desc')
                         ->get();

            // Calculate statistics
            $stats = [
                'total_pb' => $pbs->count(),
                'total_aktif' => $pbs->where('status', 'active')->count(),
                'total_batal' => $pbs->where('status', 'cancelled')->count(),
                'total_nominal_aktif' => $pbs->where('status', 'active')->sum('nominal'),
                'total_nominal_batal' => $pbs->where('status', 'cancelled')->sum('nominal'),
            ];

            // Group by day
            $byDay = $pbs->groupBy(function ($item) {
                return Carbon::parse($item->tanggal)->format('Y-m-d');
            })->map(function ($items, $key) {
                return [
                    'total' => $items->count(),
                    'aktif' => $items->where('status', 'active')->count(),
                    'batal' => $items->where('status', 'cancelled')->count(),
                    'nominal_aktif' => $items->where('status', 'active')->sum('nominal'),
                    'nominal_batal' => $items->where('status', 'cancelled')->sum('nominal'),
                ];
            });

            // Group by divisi
            $byDivisi = $pbs->groupBy('divisi')->map(function ($items, $key) {
                return [
                    'total' => $items->count(),
                    'aktif' => $items->where('status', 'active')->count(),
                    'batal' => $items->where('status', 'cancelled')->count(),
                    'nominal_aktif' => $items->where('status', 'active')->sum('nominal'),
                    'nominal_batal' => $items->where('status', 'cancelled')->sum('nominal'),
                ];
            });

            // Available options
            $divisions = ['E-CHANNEL', 'TREASURY OPERASIONAL', 'LAYANAN OPERASIONAL', 'AKUNTANSI & TAX MANAGEMENT'];
            $years = range(2020, Carbon::now()->year + 1);
            $weeks = range(1, 53);

            $periode = 'Minggu ke-' . $week . ' Tahun ' . $year . ' (' .
                      $startOfWeek->format('d/m/Y') . ' - ' . $endOfWeek->format('d/m/Y') . ')';

            return view('pb.laporan-mingguan', compact(
                'pbs', 'stats', 'byDay', 'byDivisi', 'divisions', 'years', 'weeks',
                'week', 'year', 'divisi', 'periode', 'startOfWeek', 'endOfWeek'
            ));

        } catch (\Exception $e) {
            Log::error('Error in laporanMingguan: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memuat laporan mingguan: ' . $e->getMessage());
        }
    }

    /**
     * Store bulk PBs
     */
    public function storeBulk(Request $request)
    {
        $request->validate([
            'pbs' => 'required|array|min:1',
            'pbs.*.tanggal' => 'required|date|before_or_equal:today',
            'pbs.*.nominal' => 'required|numeric|min:1',
            'pbs.*.divisi' => 'required|string',
            'pbs.*.keterangan' => 'nullable|string',
            'pbs.*.file' => 'nullable|file|max:10240|mimes:pdf,jpg,jpeg,png,xlsx,xls,doc,docx'
        ]);

        DB::beginTransaction();
        try {
            $createdPbs = [];
            $user = auth()->user();

            foreach ($request->pbs as $pbData) {
                // Check date permission
                $tanggal = Carbon::parse($pbData['tanggal']);
                if ($tanggal->isFuture()) {
                    throw new \Exception("Tanggal {$tanggal->format('d/m/Y')} tidak diizinkan. Hanya bisa input tanggal hari ini atau sebelumnya.");
                }

                // Generate nomor PB
                $nomorPb = PbCounter::getNextNumber($pbData['tanggal']);

                // Handle file upload
                $fileData = [];
                if (isset($pbData['file']) && $pbData['file']) {
                    $file = $pbData['file'];
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('pb_files', $fileName, 'public');

                    $fileData = [
                        'file_path' => $filePath,
                        'file_name' => $file->getClientOriginalName(),
                        'file_type' => $file->getClientMimeType(),
                        'file_size' => $file->getSize()
                    ];
                }

                // Create PB
                $pb = Pbs::create([
                    'nomor_pb' => $nomorPb,
                    'tanggal' => $tanggal,
                    'penginput' => $user->name,
                    'nominal' => str_replace('.', '', $pbData['nominal']),
                    'keterangan' => $pbData['keterangan'] ?? null,
                    'divisi' => $pbData['divisi'],
                    'user_id' => $user->id,
                    'status' => 'active',
                    'input_date' => Carbon::today()
                ] + $fileData);

                $createdPbs[] = $pb;

                // Send notification
                $this->notificationService->pbCreated($pb, $user);
            }

            DB::commit();

            $count = count($createdPbs);
            $pbNumbers = collect($createdPbs)->pluck('nomor_pb')->join(', ');

            return redirect()->route('pb.index')->with('success',
                "Berhasil membuat {$count} PB dengan nomor: {$pbNumbers}");

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Bulk PB Creation Error: ' . $e->getMessage());
            return back()->withErrors(['Gagal membuat PB: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Request future date permission
     */
    public function requestFutureDate(Request $request)
    {
        $request->validate([
            'requested_date' => 'required|date|after:today',
            'reason' => 'required|string|max:500'
        ]);

        DB::beginTransaction();
        try {
            // Check if request already exists
            $existing = PbDateRequest::where('user_id', auth()->id())
                ->where('requested_date', $request->requested_date)
                ->where('status', 'pending')
                ->first();

            if ($existing) {
                return back()->with('warning', 'Request untuk tanggal tersebut sudah pernah diajukan dan sedang menunggu persetujuan.');
            }

            $dateRequest = PbDateRequest::create([
                'user_id' => auth()->id(),
                'requested_date' => $request->requested_date,
                'reason' => $request->reason,
                'status' => 'pending'
            ]);

            // Notify admin
            // $this->notificationService->futureDateRequested($dateRequest, auth()->user());

            DB::commit();

            return redirect()->route('pb.create')->with('date_request_submitted', true)
                ->with('success', 'Request untuk tanggal masa depan telah dikirim ke admin.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Future Date Request Error: ' . $e->getMessage());
            return back()->withErrors(['Gagal mengirim request: ' . $e->getMessage()]);
        }
    }

    /**
     * Admin: Approve/Reject future date requests
     */
    public function manageDateRequests(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $requests = PbDateRequest::with('user')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('pb.date-requests', compact('requests'));
    }

    /**
     * Admin: Process date request
     */
    public function processDateRequest(Request $request, $id)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'action' => 'required|in:approve,reject',
            'rejection_reason' => 'required_if:action,reject|string|max:500'
        ]);

        DB::beginTransaction();
        try {
            $dateRequest = PbDateRequest::findOrFail($id);

            if ($dateRequest->status !== 'pending') {
                throw new \Exception('Request sudah diproses sebelumnya.');
            }

            if ($request->action === 'approve') {
                $dateRequest->update([
                    'status' => 'approved',
                    'approved_by' => auth()->id(),
                    'approved_at' => now()
                ]);

                $message = 'Request tanggal masa depan disetujui.';
            } else {
                $dateRequest->update([
                    'status' => 'rejected',
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                    'rejection_reason' => $request->rejection_reason
                ]);

                $message = 'Request tanggal masa depan ditolak.';
            }

            // Notify user
            // $this->notificationService->dateRequestProcessed($dateRequest, auth()->user());

            DB::commit();

            return back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Process Date Request Error: ' . $e->getMessage());
            return back()->withErrors(['Gagal memproses request: ' . $e->getMessage()]);
        }
    }
}
