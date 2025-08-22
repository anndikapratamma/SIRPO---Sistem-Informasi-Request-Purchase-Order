<?php

namespace App\Http\Controllers;

use App\Models\Pbs;
use App\Models\User;
use App\Models\Template;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SearchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Global search
     */
    public function globalSearch(Request $request)
    {
        $query = $request->get('q');
        $type = $request->get('type', 'all');

        if (empty($query)) {
            return response()->json([
                'results' => [],
                'total' => 0
            ]);
        }

        $results = [];

        if ($type === 'all' || $type === 'pbs') {
            $pbsResults = $this->searchPbs($query);
            $results['pbs'] = $pbsResults;
        }

        if (auth()->user()->role === 'admin') {
            if ($type === 'all' || $type === 'users') {
                $usersResults = $this->searchUsers($query);
                $results['users'] = $usersResults;
            }

            if ($type === 'all' || $type === 'templates') {
                $templatesResults = $this->searchTemplates($query);
                $results['templates'] = $templatesResults;
            }

            if ($type === 'all' || $type === 'activities') {
                $activitiesResults = $this->searchActivities($query);
                $results['activities'] = $activitiesResults;
            }
        }

        $total = collect($results)->sum(function ($items) {
            return is_array($items) ? count($items) : $items->count();
        });

        return response()->json([
            'results' => $results,
            'total' => $total,
            'query' => $query
        ]);
    }

    /**
     * Advanced PB search
     */
    public function advancedPbSearch(Request $request)
    {
        $query = Pbs::query();

        // Text search
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('nomor_pb', 'LIKE', "%{$search}%")
                  ->orWhere('penginput', 'LIKE', "%{$search}%")
                  ->orWhere('keterangan', 'LIKE', "%{$search}%");
            });
        }

        // Date range
        if ($request->filled('start_date')) {
            $query->where('tanggal', '>=', $request->get('start_date'));
        }
        if ($request->filled('end_date')) {
            $query->where('tanggal', '<=', $request->get('end_date'));
        }

        // Amount range
        if ($request->filled('min_amount')) {
            $query->where('nominal', '>=', $request->get('min_amount'));
        }
        if ($request->filled('max_amount')) {
            $query->where('nominal', '<=', $request->get('max_amount'));
        }

        // Division filter
        if ($request->filled('divisi')) {
            $query->where('divisi', $request->get('divisi'));
        }

        // User filter
        if ($request->filled('penginput')) {
            $query->where('penginput', $request->get('penginput'));
        }

        // Sort options
        $sortBy = $request->get('sort_by', 'tanggal');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Get results with pagination
        $pbs = $query->paginate(20);

        // Add summary statistics
        $summary = [
            'total_count' => $query->count(),
            'total_amount' => $query->sum('nominal'),
            'average_amount' => $query->avg('nominal'),
            'divisions' => $query->select('divisi', DB::raw('COUNT(*) as count'))
                                ->groupBy('divisi')
                                ->pluck('count', 'divisi'),
            'users' => $query->select('penginput', DB::raw('COUNT(*) as count'))
                            ->groupBy('penginput')
                            ->orderBy('count', 'desc')
                            ->limit(5)
                            ->pluck('count', 'penginput')
        ];

        if ($request->ajax()) {
            return response()->json([
                'pbs' => $pbs,
                'summary' => $summary,
                'pagination' => [
                    'current_page' => $pbs->currentPage(),
                    'last_page' => $pbs->lastPage(),
                    'per_page' => $pbs->perPage(),
                    'total' => $pbs->total()
                ]
            ]);
        }

        return view('search.advanced-pb', compact('pbs', 'summary'));
    }

    /**
     * Search suggestions
     */
    public function suggestions(Request $request)
    {
        $query = $request->get('q');
        $type = $request->get('type', 'pbs');

        if (empty($query)) {
            return response()->json([]);
        }

        $suggestions = [];

        switch ($type) {
            case 'pbs':
                $suggestions = $this->getPbSuggestions($query);
                break;
            case 'users':
                if (auth()->user()->role === 'admin') {
                    $suggestions = $this->getUserSuggestions($query);
                }
                break;
            case 'templates':
                $suggestions = $this->getTemplateSuggestions($query);
                break;
        }

        return response()->json($suggestions);
    }

    /**
     * Export search results
     */
    public function exportResults(Request $request)
    {
        // Implementation for exporting search results
        // Similar to existing export functions but with search filters
    }

    /**
     * Search PBs
     */
    private function searchPbs($query)
    {
        return Pbs::where('nomor_pb', 'LIKE', "%{$query}%")
                  ->orWhere('penginput', 'LIKE', "%{$query}%")
                  ->orWhere('keterangan', 'LIKE', "%{$query}%")
                  ->orWhere('divisi', 'LIKE', "%{$query}%")
                  ->orderBy('tanggal', 'desc')
                  ->limit(10)
                  ->get();
    }

    /**
     * Search Users
     */
    private function searchUsers($query)
    {
        return User::where('name', 'LIKE', "%{$query}%")
                   ->orWhere('nik', 'LIKE', "%{$query}%")
                   ->orWhere('role', 'LIKE', "%{$query}%")
                   ->orderBy('name')
                   ->limit(10)
                   ->get();
    }

    /**
     * Search Templates
     */
    private function searchTemplates($query)
    {
        return Template::where('name', 'LIKE', "%{$query}%")
                       ->orWhere('description', 'LIKE', "%{$query}%")
                       ->orWhere('original_filename', 'LIKE', "%{$query}%")
                       ->orderBy('name')
                       ->limit(10)
                       ->get();
    }

    /**
     * Search Activities
     */
    private function searchActivities($query)
    {
        return ActivityLog::with('user')
                          ->where('action', 'LIKE', "%{$query}%")
                          ->orWhere('description', 'LIKE', "%{$query}%")
                          ->orWhereHas('user', function ($q) use ($query) {
                              $q->where('name', 'LIKE', "%{$query}%");
                          })
                          ->orderBy('created_at', 'desc')
                          ->limit(10)
                          ->get();
    }

    /**
     * Get PB suggestions
     */
    private function getPbSuggestions($query)
    {
        $suggestions = [];

        // Nomor PB suggestions
        $nomor_pbs = Pbs::where('nomor_pb', 'LIKE', "%{$query}%")
                        ->distinct()
                        ->pluck('nomor_pb')
                        ->take(5);

        foreach ($nomor_pbs as $nomor) {
            $suggestions[] = [
                'type' => 'nomor_pb',
                'value' => $nomor,
                'label' => "Nomor PB: {$nomor}"
            ];
        }

        // User suggestions
        $users = Pbs::where('penginput', 'LIKE', "%{$query}%")
                    ->distinct()
                    ->pluck('penginput')
                    ->take(5);

        foreach ($users as $user) {
            $suggestions[] = [
                'type' => 'penginput',
                'value' => $user,
                'label' => "Penginput: {$user}"
            ];
        }

        // Division suggestions
        $divisions = Pbs::where('divisi', 'LIKE', "%{$query}%")
                        ->distinct()
                        ->pluck('divisi')
                        ->take(3);

        foreach ($divisions as $divisi) {
            $suggestions[] = [
                'type' => 'divisi',
                'value' => $divisi,
                'label' => "Divisi: " . strtoupper($divisi)
            ];
        }

        return $suggestions;
    }

    /**
     * Get user suggestions
     */
    private function getUserSuggestions($query)
    {
        return User::where('name', 'LIKE', "%{$query}%")
                   ->orWhere('nik', 'LIKE', "%{$query}%")
                   ->select('name', 'nik', 'role')
                   ->limit(5)
                   ->get()
                   ->map(function ($user) {
                       return [
                           'type' => 'user',
                           'value' => $user->name,
                           'label' => "{$user->name} ({$user->nik})",
                           'role' => $user->role
                       ];
                   });
    }

    /**
     * Get template suggestions
     */
    private function getTemplateSuggestions($query)
    {
        return Template::where('name', 'LIKE', "%{$query}%")
                       ->orWhere('description', 'LIKE', "%{$query}%")
                       ->select('name', 'description', 'is_active')
                       ->limit(5)
                       ->get()
                       ->map(function ($template) {
                           return [
                               'type' => 'template',
                               'value' => $template->name,
                               'label' => $template->name,
                               'description' => $template->description,
                               'active' => $template->is_active
                           ];
                       });
    }
}
