<?php

namespace App\Http\Controllers;

use App\Models\Pbs;
use App\Models\User;
use App\Models\Template;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show reports dashboard
     */
    public function index()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        // Summary statistics
        $totalPbs = Pbs::count();
        $totalAmount = Pbs::sum('nominal');
        $totalUsers = User::where('role', 'user')->count();
        $totalTemplates = Template::count();

        // Monthly summary
        $monthlySummary = Pbs::select(
            DB::raw('MONTH(tanggal) as month'),
            DB::raw('YEAR(tanggal) as year'),
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(nominal) as total')
        )
        ->groupBy(DB::raw('YEAR(tanggal)'), DB::raw('MONTH(tanggal)'))
        ->orderBy('year', 'desc')
        ->orderBy('month', 'desc')
        ->limit(12)
        ->get();

        return view('admin.reports.index', compact(
            'totalPbs', 'totalAmount', 'totalUsers', 'totalTemplates', 'monthlySummary'
        ));
    }

    /**
     * Generate PB summary report
     */
    public function pbSummary(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));
        $divisi = $request->get('divisi');
        $status = $request->get('status');
        $format = $request->get('format', 'html');

        $query = Pbs::whereBetween('tanggal', [$startDate, $endDate]);

        if ($divisi) {
            $query->where('divisi', $divisi);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $pbs = $query->orderBy('tanggal', 'desc')->paginate(20);

        // Summary statistics
        $totalPbs = $query->count();
        $approvedPbs = Pbs::whereBetween('tanggal', [$startDate, $endDate])->where('status', 'approved')->count();
        $pendingPbs = Pbs::whereBetween('tanggal', [$startDate, $endDate])->where('status', 'pending')->count();
        $totalAmount = Pbs::whereBetween('tanggal', [$startDate, $endDate])->sum('nominal');

        if ($format === 'excel') {
            return $this->exportPbSummaryExcel($pbs, [], [], []);
        } elseif ($format === 'pdf') {
            return $this->exportPbSummaryPdf($pbs, [], [], []);
        } else {
            // Return HTML view
            return view('admin.reports.pb-summary', compact(
                'pbs', 'totalPbs', 'approvedPbs', 'pendingPbs', 'totalAmount'
            ));
        }
    }

    /**
     * Generate user activity report
     */
    public function userActivity(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $format = $request->get('format', 'html');

        // Get all users with their PB counts and activity counts
        $users = User::withCount(['pbs', 'activityLogs'])
                    ->orderBy('activity_logs_count', 'desc')
                    ->get();

        // Summary statistics
        $totalUsers = User::count();
        $activeUsers = User::whereHas('activityLogs', function($query) {
            $query->where('created_at', '>=', now()->subDays(7));
        })->count();
        $totalActivities = ActivityLog::count();

        if ($format === 'excel') {
            return $this->exportUserActivityExcel(collect(), [], [], []);
        } elseif ($format === 'pdf') {
            return $this->exportUserActivityPdf(collect(), [], [], []);
        } else {
            // Return HTML view
            return view('admin.reports.user-activity', compact(
                'users', 'totalUsers', 'activeUsers', 'totalActivities'
            ));
        }
    }

    /**
     * Generate template usage report
     */
    public function templateUsage(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $templates = Template::orderBy('download_count', 'desc')
                           ->orderBy('created_at', 'desc')
                           ->get();

        // Calculate summary data
        $totalTemplates = $templates->count();
        $totalDownloads = $templates->sum('download_count');
        $monthlyDownloads = $templates->where('last_downloaded', '>=', now()->startOfMonth())->sum('download_count');
        $mostPopular = $templates->first()->name ?? 'N/A';

        $format = $request->get('format', 'html');

        if ($format === 'excel') {
            return $this->exportTemplateUsageExcel($templates);
        } elseif ($format === 'pdf') {
            return $this->exportTemplateUsagePdf($templates);
        } else {
            // Return HTML view
            return view('admin.reports.template-usage', compact(
                'templates', 'totalTemplates', 'totalDownloads',
                'monthlyDownloads', 'mostPopular'
            ));
        }
    }

    /**
     * Export PB Summary to Excel
     */
    private function exportPbSummaryExcel($pbs, $summary, $divisionSummary, $userSummary)
    {
        $filename = 'Laporan_PB_Summary_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new class($pbs, $summary, $divisionSummary, $userSummary) implements
            \Maatwebsite\Excel\Concerns\FromCollection,
            \Maatwebsite\Excel\Concerns\WithMultipleSheets
        {
            private $pbs, $summary, $divisionSummary, $userSummary;

            public function __construct($pbs, $summary, $divisionSummary, $userSummary)
            {
                $this->pbs = $pbs;
                $this->summary = $summary;
                $this->divisionSummary = $divisionSummary;
                $this->userSummary = $userSummary;
            }

            public function collection()
            {
                return $this->pbs;
            }

            public function sheets(): array
            {
                return [
                    'Summary' => new class($this->summary, $this->divisionSummary, $this->userSummary) implements \Maatwebsite\Excel\Concerns\FromArray {
                        private $summary, $divisionSummary, $userSummary;

                        public function __construct($summary, $divisionSummary, $userSummary)
                        {
                            $this->summary = $summary;
                            $this->divisionSummary = $divisionSummary;
                            $this->userSummary = $userSummary;
                        }

                        public function array(): array
                        {
                            return [
                                ['LAPORAN SUMMARY PB'],
                                ['Periode', $this->summary['start_date'] . ' - ' . $this->summary['end_date']],
                                ['Total PB', $this->summary['total_count']],
                                ['Total Nominal', 'Rp ' . number_format($this->summary['total_amount'], 0, ',', '.')],
                                ['Rata-rata', 'Rp ' . number_format($this->summary['average_amount'], 0, ',', '.')],
                                [''],
                                ['RINGKASAN PER DIVISI'],
                                ['Divisi', 'Jumlah PB', 'Total Nominal', 'Rata-rata'],
                                ...collect($this->divisionSummary)->map(function ($item) {
                                    return [
                                        $item['division'],
                                        $item['count'],
                                        $item['total'],
                                        number_format($item['average'], 0, ',', '.')
                                    ];
                                })->toArray()
                            ];
                        }
                    },
                    'Detail PB' => new class($this->pbs) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
                        private $pbs;

                        public function __construct($pbs) {
                            $this->pbs = $pbs;
                        }

                        public function collection()
                        {
                            return $this->pbs->map(function ($pb, $index) {
                                return [
                                    $index + 1,
                                    $pb->nomor_pb,
                                    $pb->tanggal,
                                    $pb->penginput,
                                    $pb->nominal,
                                    $pb->keterangan,
                                    $pb->divisi
                                ];
                            });
                        }

                        public function headings(): array
                        {
                            return ['No', 'Nomor PB', 'Tanggal', 'Penginput', 'Nominal', 'Keterangan', 'Divisi'];
                        }
                    }
                ];
            }
        }, $filename);
    }

    /**
     * Export PB Summary to PDF
     */
    private function exportPbSummaryPdf($pbs, $summary, $divisionSummary, $userSummary)
    {
        $pdf = Pdf::loadView('admin.reports.pb-summary-pdf', compact('pbs', 'summary', 'divisionSummary', 'userSummary'))
                  ->setPaper('a4', 'portrait');

        $filename = 'Laporan_PB_Summary_' . now()->format('Y-m-d_H-i-s') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Export User Activity to Excel
     */
    private function exportUserActivityExcel($activities, $summary, $actionSummary, $userActivitySummary)
    {
        $filename = 'Laporan_User_Activity_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new class($activities, $summary) implements
            \Maatwebsite\Excel\Concerns\FromCollection,
            \Maatwebsite\Excel\Concerns\WithHeadings
        {
            private $activities, $summary;

            public function __construct($activities, $summary)
            {
                $this->activities = $activities;
                $this->summary = $summary;
            }

            public function collection()
            {
                return $this->activities->map(function ($activity, $index) {
                    return [
                        $index + 1,
                        $activity->user->name ?? 'Unknown',
                        $activity->action,
                        $activity->description ?? '',
                        $activity->created_at->format('Y-m-d H:i:s'),
                        $activity->ip_address ?? ''
                    ];
                });
            }

            public function headings(): array
            {
                return ['No', 'User', 'Action', 'Description', 'Date Time', 'IP Address'];
            }
        }, $filename);
    }

    /**
     * Export User Activity to PDF
     */
    private function exportUserActivityPdf($activities, $summary, $actionSummary, $userActivitySummary)
    {
        $pdf = Pdf::loadView('admin.reports.user-activity-pdf', compact('activities', 'summary', 'actionSummary', 'userActivitySummary'))
                  ->setPaper('a4', 'portrait');

        $filename = 'Laporan_User_Activity_' . now()->format('Y-m-d_H-i-s') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Export Template Usage to Excel
     */
    private function exportTemplateUsageExcel($templates)
    {
        $filename = 'Laporan_Template_Usage_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new class($templates) implements
            \Maatwebsite\Excel\Concerns\FromCollection,
            \Maatwebsite\Excel\Concerns\WithHeadings
        {
            private $templates;

            public function __construct($templates)
            {
                $this->templates = $templates;
            }

            public function collection()
            {
                return $this->templates->map(function ($template, $index) {
                    return [
                        $index + 1,
                        $template->name ?? 'Untitled',
                        $template->original_name ?? '',
                        $template->file_size ? number_format($template->file_size / 1024, 1) . ' KB' : '',
                        $template->download_count ?? 0,
                        $template->created_at ? $template->created_at->format('Y-m-d H:i:s') : '',
                    ];
                });
            }

            public function headings(): array
            {
                return ['No', 'Template Name', 'Original Name', 'File Size', 'Download Count', 'Upload Date'];
            }
        }, $filename);
    }

    /**
     * Export Template Usage to PDF
     */
    private function exportTemplateUsagePdf($templates)
    {
        $pdf = Pdf::loadView('admin.reports.template-usage-pdf', compact('templates'))
                  ->setPaper('a4', 'portrait');

        $filename = 'Laporan_Template_Usage_' . now()->format('Y-m-d_H-i-s') . '.pdf';

        return $pdf->download($filename);
    }
}
