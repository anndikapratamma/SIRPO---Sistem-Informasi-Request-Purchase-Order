<?php

namespace App\Http\Controllers;

use App\Models\Pbs;
use App\Models\User;
use App\Models\Template;
use App\Models\ActivityLog;
use App\Models\ProfileChangeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            $user = auth()->user();

            // Debug logging
            Log::info('Dashboard accessed by user: ' . ($user ? $user->name : 'not authenticated'));

            if (!$user) {
                return redirect()->route('login');
            }

            if ($user->role === 'admin') {
                Log::info('Loading admin dashboard');
                return $this->adminDashboard();
            } else {
                Log::info('Loading user dashboard');
                return $this->userDashboard();
            }
        } catch (\Exception $e) {
            Log::error('Dashboard error: ' . $e->getMessage());
            return response()->view('errors.500', ['error' => $e->getMessage()], 500);
        }
    }

    public function masterDashboard()
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return redirect()->route('login');
            }

            if ($user->role === 'admin') {
                return $this->adminMasterDashboard();
            } else {
                return $this->userMasterDashboard();
            }
        } catch (\Exception $e) {
            Log::error('Master Dashboard error: ' . $e->getMessage());
            return response()->view('errors.500', ['error' => $e->getMessage()], 500);
        }
    }

    private function userMasterDashboard()
    {
        try {
            $user = auth()->user();

            // UPDATED: Shared visibility - User can see all PBs (same as admin)
            $allPbs = Pbs::all(); // Simplified query for debugging

            // Debug: Log the actual count
            \Log::info('User Dashboard Debug - Total PBs found: ' . $allPbs->count());
            \Log::info('User Dashboard Debug - PB Numbers: ' . $allPbs->pluck('nomor_pb')->implode(', '));

            // User statistics showing ALL PBs (same as admin)
            $totalUserPbs = $allPbs->count(); // Show total of all PBs
            $pendingPbs = $allPbs->where('status', 'pending')->count();
            $approvedPbs = $allPbs->where('status', 'approved')->count();
            $totalUserAmount = $allPbs->sum('nominal');

            // Recent PBs (all PBs visible to user)
            $userPbs = Pbs::with(['user', 'cancelledBy'])
                          ->orderBy('created_at', 'desc')
                          ->take(5)
                          ->get();

            return view('dashboard.user-working', compact(
                'totalUserPbs',
                'pendingPbs',
                'approvedPbs',
                'totalUserAmount',
                'userPbs'
            ));

        } catch (\Exception $e) {
            \Log::error('User Master Dashboard Error: ' . $e->getMessage());

            return view('dashboard.user-working')->with([
                'totalUserPbs' => 0,
                'pendingPbs' => 0,
                'approvedPbs' => 0,
                'totalUserAmount' => 0,
                'userPbs' => collect()
            ]);
        }
    }

            Log::info('User master dashboard data prepared successfully');

            return view('dashboard.user-working', [
                'totalUserPbs' => $totalUserPbs,
                'pendingPbs' => $pendingPbs,
                'approvedPbs' => $approvedPbs,
                'totalUserAmount' => $totalUserAmount,
                'userPbs' => $userPbs,
                'user' => $user
            ]);

        } catch (\Exception $e) {
            Log::error('User master dashboard error: ' . $e->getMessage());
            throw $e;
        }
    }

    private function adminMasterDashboard()
    {
        try {
            // Statistics
            $totalUsers = User::count();
            $totalPbs = Pbs::count();
            $totalTemplates = Template::count();
            $totalAmount = Pbs::sum('nominal');

            // Recent PBs
            $recentPbs = Pbs::with('user')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

            Log::info('Admin master dashboard data prepared successfully');

            return view('dashboard.admin-master', [
                'totalUsers' => $totalUsers,
                'totalPbs' => $totalPbs,
                'totalTemplates' => $totalTemplates,
                'totalAmount' => $totalAmount,
                'recentPbs' => $recentPbs,
                'user' => auth()->user()
            ]);

        } catch (\Exception $e) {
            Log::error('Admin master dashboard error: ' . $e->getMessage());
            throw $e;
        }
    }

    private function adminDashboard()
    {
        try {
            // Statistics
            $totalUsers = User::count();
            $totalPbs = Pbs::count();
            $totalTemplates = Template::count();
            $totalAmount = Pbs::sum('nominal');

            // Pending profile change requests
            $pendingProfileChanges = ProfileChangeRequest::pending()->count();

            // Recent PBs
            $recentPbs = Pbs::with('user')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

            Log::info('Admin dashboard data prepared successfully');

            return view('dashboard.admin-working', [
                'totalUsers' => $totalUsers,
                'totalPbs' => $totalPbs,
                'totalTemplates' => $totalTemplates,
                'totalAmount' => $totalAmount,
                'pendingProfileChanges' => $pendingProfileChanges,
                'recentPbs' => $recentPbs,
                'user' => auth()->user()
            ]);

        } catch (\Exception $e) {
            Log::error('Admin dashboard error: ' . $e->getMessage());
            throw $e;
        }
    }

    private function userDashboard()
    {
        $user = auth()->user();

        // UPDATED: Shared visibility - User can see all PBs (same as admin)
        $allPbs = Pbs::all();
        $totalPbs = $allPbs->count();
        $totalAmount = $allPbs->sum('nominal');

        // Monthly statistics for all PBs
        $monthlyPbs = Pbs::select(
            DB::raw('MONTH(tanggal) as month'),
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(nominal) as total')
        )
        ->whereYear('tanggal', now()->year)
        ->groupBy(DB::raw('MONTH(tanggal)'))
        ->orderBy('month')
        ->get();

        // Available templates
        $availableTemplates = Template::active()->latest()->limit(3)->get();

        // Recent PBs (all PBs visible to user)
        $recentPbs = Pbs::latest()
                        ->limit(5)
                        ->get();

        // Division stats for all PBs
        $divisionStats = Pbs::select('divisi', DB::raw('COUNT(*) as count'), DB::raw('SUM(nominal) as total'))
                            ->groupBy('divisi')
                            ->get();

        // Weekly trend for all PBs
        $weeklyTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = Pbs::whereDate('tanggal', $date)->count();
            $total = Pbs::whereDate('tanggal', $date)->sum('nominal');

            $weeklyTrend[] = [
                'date' => $date->format('Y-m-d'),
                'day' => $date->format('D'),
                'count' => $count,
                'total' => $total
            ];
        }

        return view('dashboard.user', compact(
            'totalPbs', 'totalAmount', 'monthlyPbs',
            'availableTemplates', 'recentPbs', 'divisionStats',
            'weeklyTrend'
        ));
    }

    /**
     * Get chart data via AJAX
     */
    public function getChartData(Request $request)
    {
        $type = $request->get('type');
        $period = $request->get('period', 'month');

        switch ($type) {
            case 'pbs':
                return $this->getPbsChartData($period);
            case 'users':
                return $this->getUsersChartData($period);
            case 'divisions':
                return $this->getDivisionsChartData($period);
            default:
                return response()->json([]);
        }
    }

    private function getPbsChartData($period)
    {
        $query = Pbs::select();

        if ($period === 'month') {
            $data = $query->select(
                DB::raw('MONTH(tanggal) as period'),
                DB::raw('MONTHNAME(tanggal) as label'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(nominal) as total')
            )
            ->whereYear('tanggal', now()->year)
            ->groupBy(DB::raw('MONTH(tanggal)'), DB::raw('MONTHNAME(tanggal)'))
            ->orderBy('period')
            ->get();
        } elseif ($period === 'week') {
            $data = collect();
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $count = Pbs::whereDate('tanggal', $date)->count();
                $total = Pbs::whereDate('tanggal', $date)->sum('nominal');

                $data->push([
                    'label' => $date->format('D, M j'),
                    'count' => $count,
                    'total' => $total
                ]);
            }
        }

        return response()->json($data);
    }

    private function getUsersChartData($period)
    {
        $data = Pbs::select('penginput as label', DB::raw('COUNT(*) as count'), DB::raw('SUM(nominal) as total'))
                   ->groupBy('penginput')
                   ->orderBy('count', 'desc')
                   ->limit(10)
                   ->get();

        return response()->json($data);
    }

    private function getDivisionsChartData($period)
    {
        $data = Pbs::select('divisi as label', DB::raw('COUNT(*) as count'), DB::raw('SUM(nominal) as total'))
                   ->groupBy('divisi')
                   ->orderBy('count', 'desc')
                   ->get();

        return response()->json($data);
    }
}


