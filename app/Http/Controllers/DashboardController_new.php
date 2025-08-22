<?php

namespace App\Http\Controllers;

use App\Models\Pbs;
use App\Models\User;
use App\Models\Template;
use App\Models\ActivityLog;
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

    private function adminDashboard()
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

            Log::info('Admin dashboard data prepared successfully');

            return view('dashboard.admin-working', [
                'totalUsers' => $totalUsers,
                'totalPbs' => $totalPbs,
                'totalTemplates' => $totalTemplates,
                'totalAmount' => $totalAmount,
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
        try {
            $user = auth()->user();

            // User's PB statistics
            $totalUserPbs = Pbs::where('user_id', $user->id)->count();
            $pendingPbs = Pbs::where('user_id', $user->id)->where('status', 'pending')->count();
            $approvedPbs = Pbs::where('user_id', $user->id)->where('status', 'approved')->count();
            $totalUserAmount = Pbs::where('user_id', $user->id)->sum('nominal');

            // Recent PBs for this user
            $userPbs = Pbs::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

            return view('dashboard.user-working', [
                'totalUserPbs' => $totalUserPbs,
                'pendingPbs' => $pendingPbs,
                'approvedPbs' => $approvedPbs,
                'totalUserAmount' => $totalUserAmount,
                'userPbs' => $userPbs,
                'user' => $user
            ]);

        } catch (\Exception $e) {
            Log::error('User dashboard error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getChartData(Request $request)
    {
        try {
            $period = $request->get('period', 'monthly');

            $pbsData = $this->getPbsChartData($period);
            $usersData = $this->getUsersChartData($period);
            $divisionsData = $this->getDivisionsChartData($period);

            return response()->json([
                'pbs' => $pbsData,
                'users' => $usersData,
                'divisions' => $divisionsData
            ]);

        } catch (\Exception $e) {
            Log::error('Chart data error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load chart data'], 500);
        }
    }

    private function getPbsChartData($period)
    {
        $query = Pbs::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as period'),
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(nominal) as total')
        )
        ->groupBy('period')
        ->orderBy('period');

        return $query->get();
    }

    private function getUsersChartData($period)
    {
        $query = User::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as period'),
            DB::raw('COUNT(*) as count')
        )
        ->groupBy('period')
        ->orderBy('period');

        return $query->get();
    }

    private function getDivisionsChartData($period)
    {
        $query = Pbs::select(
            'divisi',
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(nominal) as total')
        )
        ->groupBy('divisi');

        return $query->get();
    }
}
