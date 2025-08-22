<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class AdminProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Show admin profile page
     */
    public function edit()
    {
        $admin = Auth::user();
        return view('admin.profile.edit-working', compact('admin'));
    }

    /**
     * Update admin profile information
     */
    public function update(Request $request)
    {
        $admin = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nik' => ['required', 'string', 'size:16', Rule::unique('users')->ignore($admin->id)],
            'current_nik' => ['required', 'string'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'nik.required' => 'NIK wajib diisi.',
            'nik.size' => 'NIK harus terdiri dari 16 digit.',
            'nik.unique' => 'NIK sudah terdaftar dalam sistem.',
            'current_nik.required' => 'NIK saat ini wajib diisi untuk verifikasi.',
        ]);

        // Verify current NIK
        if ($request->current_nik !== $admin->nik) {
            return back()->withErrors([
                'current_nik' => 'NIK saat ini tidak sesuai.'
            ])->withInput();
        }

        try {
            $admin->update([
                'name' => $request->name,
                'nik' => $request->nik,
            ]);

            Log::info('Admin profile updated', [
                'admin_id' => $admin->id,
                'updated_fields' => ['name', 'nik']
            ]);

            return back()->with('status', 'Profil admin berhasil diperbarui.');

        } catch (\Exception $e) {
            Log::error('Failed to update admin profile', [
                'error' => $e->getMessage(),
                'admin_id' => $admin->id
            ]);

            return back()->with('error', 'Gagal memperbarui profil. Silakan coba lagi.');
        }
    }

    /**
     * Update admin password
     */
    public function updatePassword(Request $request)
    {
        $admin = Auth::user();

        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',
        ]);

        // Verify current password
        if (!Hash::check($request->current_password, $admin->password)) {
            return back()->withErrors([
                'current_password' => 'Password saat ini tidak sesuai.'
            ]);
        }

        try {
            $admin->update([
                'password' => Hash::make($request->password),
            ]);

            Log::info('Admin password updated', [
                'admin_id' => $admin->id
            ]);

            return back()->with('status', 'Password admin berhasil diperbarui.');

        } catch (\Exception $e) {
            Log::error('Failed to update admin password', [
                'error' => $e->getMessage(),
                'admin_id' => $admin->id
            ]);

            return back()->with('error', 'Gagal memperbarui password. Silakan coba lagi.');
        }
    }

    /**
     * Show admin dashboard with statistics
     */
    public function dashboard()
    {
        $admin = Auth::user();
        
        // Get admin statistics
        $stats = [
            'total_users' => \App\Models\User::count(),
            'pending_requests' => \App\Models\ProfileChangeRequest::pending()->count(),
            'recent_logins' => \App\Models\User::where('updated_at', '>=', now()->subDays(7))->count(),
            'admin_count' => \App\Models\User::where('role', 'admin')->count(),
        ];

        // Get recent activities
        $recentRequests = \App\Models\ProfileChangeRequest::with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.profile.dashboard-working', compact('admin', 'stats', 'recentRequests'));
    }

    /**
     * Show admin activity log
     */
    public function activityLog(Request $request)
    {
        $admin = Auth::user();
        
        // This would require implementing activity logging
        // For now, return empty data
        $activities = collect([]);
        
        return view('admin.profile.activity-working', compact('admin', 'activities'));
    }
}
