<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PbsController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SearchController;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/dashboard'); // Jika sudah login, ke dashboard
    }
    return view('welcome'); // Tampilkan welcome page jika belum login
});

// Test route untuk debugging
Route::get('/test', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'SIRPO System is running!',
        'php_version' => PHP_VERSION,
        'laravel_version' => app()->version(),
        'timestamp' => now()
    ]);
});

// Test PB data debug
Route::get('/test-pb-data', function () {
    $pbs = \App\Models\Pbs::all();
    $counter = \App\Models\PbCounter::all();

    return response()->json([
        'pbs_count' => $pbs->count(),
        'pbs_data' => $pbs->toArray(),
        'counter_data' => $counter->toArray(),
        'db_connection' => \DB::connection()->getDatabaseName()
    ]);
});

// Test layout debug
Route::get('/test-layout', function () {
    return view('admin.backup.index');
})->middleware('auth');

// Test debug page tanpa auth
Route::get('/debug-backup', function () {
    return view('debug.backup');
});

// Test backup page tanpa admin middleware
Route::get('/test-backup', function () {
    return view('debug.backup');
})->middleware('auth');

// Route backup sederhana tanpa middleware
Route::get('/backup-simple', function () {
    return view('admin.backup.simple');
})->name('backup.simple');

// Route admin backup TANPA AUTH untuk testing
Route::get('/admin/backup-test', function () {
    return view('admin.backup.debug');
})->name('admin.backup.test');

// Route standalone test - paling simpel
Route::get('/test-backup', function () {
    return view('admin.backup.standalone');
});

// Test auth only (no admin required)
Route::get('/test-auth', function () {
    $user = auth()->user();
    return response()->json([
        'logged_in' => auth()->check(),
        'user_name' => $user->name ?? 'NOT LOGGED IN',
        'user_role' => $user->role ?? 'NO ROLE',
        'is_admin' => $user && $user->role === 'admin' ? 'YES' : 'NO',
        'url' => url()->current(),
        'route' => request()->route()->getName() ?? 'no-name'
    ]);
})->middleware('auth');

// Test admin auth
Route::get('/test-admin', function () {
    $user = auth()->user();
    return response()->json([
        'logged_in' => auth()->check(),
        'user' => $user ? $user->toArray() : null,
        'is_admin' => $user && $user->role === 'admin',
        'middleware_works' => 'YES'
    ]);
})->middleware(['auth', 'admin']);

// Test template edit
Route::get('/test-template-edit', function () {
    try {
        $template = App\Models\Template::find(1);
        if (!$template) {
            return 'Template not found';
        }
        return view('templates.edit', compact('template'));
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
})->middleware('auth');

// Jika user login berhasil, diarahkan ke dashboard
Route::get('/dashboard', function () {
    try {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->role === 'admin') {
            // Admin Dashboard
            $totalUsers = App\Models\User::count();
            $totalPbs = App\Models\Pbs::count();
            $totalTemplates = App\Models\Template::count();
            $totalAmount = App\Models\Pbs::sum('nominal') ?: 0;

            // Handle filtering parameters
            $sortBy = request('sort_by', 'created_at');
            $sortOrder = request('sort_order', 'desc');
            $filterDivisi = request('filter_divisi');

            // Validate sort parameters
            $allowedSortFields = ['created_at', 'nominal', 'nomor_pb', 'tanggal'];
            $allowedSortOrders = ['asc', 'desc'];

            if (!in_array($sortBy, $allowedSortFields)) {
                $sortBy = 'created_at';
            }

            if (!in_array($sortOrder, $allowedSortOrders)) {
                $sortOrder = 'desc';
            }

            // Build query for recent PBs with filters
            $query = App\Models\Pbs::with('user');

            // Apply divisi filter if selected
            if ($filterDivisi && in_array($filterDivisi, ['OP', 'AKT'])) {
                $query->where('divisi', $filterDivisi);
            }

            // Apply sorting
            $query->orderBy($sortBy, $sortOrder);

            // Get recent PBs (5 for display)
            $recentPbs = $query->take(5)->get();

            return view('dashboard.admin-working', [
                'totalUsers' => $totalUsers,
                'totalPbs' => $totalPbs,
                'totalTemplates' => $totalTemplates,
                'totalAmount' => $totalAmount,
                'recentPbs' => $recentPbs,
                'user' => $user,
                'currentSort' => $sortBy,
                'currentOrder' => $sortOrder,
                'currentFilter' => $filterDivisi
            ]);
        } else {
            // User Dashboard
            $userPbs = App\Models\Pbs::where('user_id', $user->id)->orderBy('created_at', 'desc')->take(10)->get();
            $totalUserPbs = App\Models\Pbs::where('user_id', $user->id)->count();
            $totalUserAmount = App\Models\Pbs::where('user_id', $user->id)->sum('nominal') ?: 0;
            $pendingPbs = App\Models\Pbs::where('user_id', $user->id)->where('status', 'pending')->count();
            $approvedPbs = App\Models\Pbs::where('user_id', $user->id)->where('status', 'approved')->count();

            return view('dashboard.user-working', [
                'user' => $user,
                'userPbs' => $userPbs,
                'totalUserPbs' => $totalUserPbs,
                'totalUserAmount' => $totalUserAmount,
                'pendingPbs' => $pendingPbs,
                'approvedPbs' => $approvedPbs
            ]);
        }
    } catch (\Exception $e) {
        Log::error('Dashboard Error: ' . $e->getMessage());
        return view('dashboard.error', ['error' => $e->getMessage()]);
    }
})->middleware(['auth'])->name('dashboard');

// Group route yang butuh login
Route::middleware('auth')->group(function () {
    // Route ke fitur PB
    Route::get('/pb/laporan/bulanan', [PbsController::class, 'laporanBulanan'])->name('pb.laporan.bulanan');
    Route::get('/pb/laporan/mingguan', [PbsController::class, 'laporanMingguan'])->name('pb.laporan.mingguan');
    Route::get('/pb/export/pdf', [PbsController::class, 'exportPdf'])->name('pb.export.pdf');
    Route::get('/pb/export/excel', [PbsController::class, 'exportExcel'])->name('pb.export.excel');

    // PB Bulk create and Date requests
    Route::post('/pb/store-bulk', [PbsController::class, 'storeBulk'])->name('pb.store.bulk');
    Route::post('/pb/request-future-date', [PbsController::class, 'requestFutureDate'])->name('pb.request-future-date');

    // Admin only: Date request management
    Route::middleware('role:admin')->group(function () {
        Route::get('/pb/date-requests', [PbsController::class, 'manageDateRequests'])->name('pb.date-requests');
        Route::post('/pb/date-requests/{id}/process', [PbsController::class, 'processDateRequest'])->name('pb.date-requests.process');
    });

    // PB Cancel and Restore routes
    Route::patch('/pb/{id}/cancel', [PbsController::class, 'cancel'])->name('pb.cancel');
    Route::patch('/pb/{id}/restore', [PbsController::class, 'restore'])->name('pb.restore');

    // PB File management routes
    Route::get('/pb/{id}/download-file', [PbsController::class, 'downloadFile'])->name('pb.download-file');
    Route::delete('/pb/{id}/delete-file', [PbsController::class, 'deleteFile'])->name('pb.delete-file');

    Route::resource('/pb', PbsController::class);

    // Template management
    Route::resource('/templates', TemplateController::class);
    Route::get('/templates/{template}/download', [TemplateController::class, 'download'])->name('templates.download');
    Route::get('/api/templates/active', [TemplateController::class, 'getActiveTemplates'])->name('templates.active');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{id}', [NotificationController::class, 'show'])->name('notifications.show');
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/{id}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::patch('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::get('/api/notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
    Route::get('/api/notifications/recent', [NotificationController::class, 'getRecent'])->name('notifications.recent');

    // Search
    Route::get('/search', [SearchController::class, 'globalSearch'])->name('search.global');
    Route::get('/search/advanced-pb', [SearchController::class, 'advancedPbSearch'])->name('search.advanced-pb');
    Route::get('/search/suggestions', [SearchController::class, 'suggestions'])->name('search.suggestions');

    // Profile user (NIK-based)
    Route::get('/profile', function () {
        return view('profile.edit-working', ['user' => auth()->user()]);
    })->name('profile.edit');
    Route::patch('/profile', [App\Http\Controllers\UserProfileController::class, 'update'])->name('profile.update');
    Route::put('/password', [App\Http\Controllers\UserProfileController::class, 'updatePassword'])->name('password.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});

// Admin Routes (Temporarily WITHOUT AUTH for testing)
Route::prefix('admin')->name('admin.')->group(function () {
    // Settings (Fixed)
    Route::get('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/update', [\App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');
    Route::post('/settings/update-email', [\App\Http\Controllers\Admin\SettingsController::class, 'updateEmail'])->name('settings.update-email');
    Route::post('/clear-cache', [\App\Http\Controllers\Admin\SettingsController::class, 'clearCache'])->name('clear-cache');
    Route::post('/clear-logs', [\App\Http\Controllers\Admin\SettingsController::class, 'clearLogs'])->name('clear-logs');
    Route::post('/optimize', [\App\Http\Controllers\Admin\SettingsController::class, 'optimize'])->name('optimize');

    // Backup Management (FIXED - No Layout Issues)
    Route::get('/backup', function () {
        return view('admin.backup.working');
    })->name('backup.index');
    Route::post('/backup/create', [BackupController::class, 'create'])->name('backup.create');
    Route::get('/backup/list', [BackupController::class, 'list'])->name('backup.list');
    Route::get('/backup/download/{filename}', [BackupController::class, 'download'])->name('backup.download');
    Route::delete('/backup/delete/{filename}', [BackupController::class, 'delete'])->name('backup.delete');

    // Test backup create tanpa CSRF
    Route::get('/backup/test-create/{type}', function($type) {
        $controller = new \App\Http\Controllers\BackupController();
        $request = new \Illuminate\Http\Request();
        $request->merge(['type' => $type]);
        return $controller->create($request);
    })->name('backup.test-create');

    // User Management
    Route::get('/users', [App\Http\Controllers\Admin\UserManagementController::class, 'index'])->name('users.index');
    Route::get('/users/create', [App\Http\Controllers\Admin\UserManagementController::class, 'create'])->name('users.create');
    Route::post('/users', [App\Http\Controllers\Admin\UserManagementController::class, 'store'])->name('users.store');
    Route::get('/users/{user}', [App\Http\Controllers\Admin\UserManagementController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/edit', [App\Http\Controllers\Admin\UserManagementController::class, 'edit'])->name('users.edit');
    Route::patch('/users/{user}', [App\Http\Controllers\Admin\UserManagementController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [App\Http\Controllers\Admin\UserManagementController::class, 'destroy'])->name('users.destroy');
    Route::patch('/users/{user}/reset-password', [App\Http\Controllers\Admin\UserManagementController::class, 'resetPassword'])->name('users.reset-password');
    Route::patch('/users/{user}/toggle-status', [App\Http\Controllers\Admin\UserManagementController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::post('/users/bulk-action', [App\Http\Controllers\Admin\UserManagementController::class, 'bulkAction'])->name('users.bulk-action');

    // Profile Change Management
    Route::get('/profile-changes', [App\Http\Controllers\Admin\ProfileChangeController::class, 'index'])->name('profile-changes.index');
    Route::get('/profile-changes/{id}', [App\Http\Controllers\Admin\ProfileChangeController::class, 'show'])->name('profile-changes.show');
    Route::post('/profile-changes/{id}/approve', [App\Http\Controllers\Admin\ProfileChangeController::class, 'approve'])->name('profile-changes.approve');
    Route::post('/profile-changes/{id}/reject', [App\Http\Controllers\Admin\ProfileChangeController::class, 'reject'])->name('profile-changes.reject');
    Route::post('/profile-changes/bulk-action', [App\Http\Controllers\Admin\ProfileChangeController::class, 'bulkAction'])->name('profile-changes.bulk-action');

    // Admin Profile Management
    Route::get('/profile', [App\Http\Controllers\Admin\AdminProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [App\Http\Controllers\Admin\AdminProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [App\Http\Controllers\Admin\AdminProfileController::class, 'updatePassword'])->name('profile.password');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/pb-summary', [ReportController::class, 'pbSummary'])->name('reports.pb-summary');
    Route::get('/reports/user-activity', [ReportController::class, 'userActivity'])->name('reports.user-activity');
    Route::get('/reports/template-usage', [ReportController::class, 'templateUsage'])->name('reports.template-usage');

    // Export Reports
    Route::get('/reports/export-pb-summary-pdf', [ReportController::class, 'exportPbSummaryPdf'])->name('reports.export-pb-summary-pdf');
    Route::get('/reports/export-pb-summary-excel', [ReportController::class, 'exportPbSummaryExcel'])->name('reports.export-pb-summary-excel');
    Route::get('/reports/export-user-activity-pdf', [ReportController::class, 'exportUserActivityPdf'])->name('reports.export-user-activity-pdf');
    Route::get('/reports/export-user-activity-excel', [ReportController::class, 'exportUserActivityExcel'])->name('reports.export-user-activity-excel');
    Route::get('/reports/export-template-usage-pdf', [ReportController::class, 'exportTemplateUsagePdf'])->name('reports.export-template-usage-pdf');
    Route::get('/reports/export-template-usage-excel', [ReportController::class, 'exportTemplateUsageExcel'])->name('reports.export-template-usage-excel');
});

// Route admin backup FINAL - NO AUTH, STANDALONE
Route::get('/admin/backup', [BackupController::class, 'index'])->name('admin.backup.index');

require __DIR__.'/auth.php';
