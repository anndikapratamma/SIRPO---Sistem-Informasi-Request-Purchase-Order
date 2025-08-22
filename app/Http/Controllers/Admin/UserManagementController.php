<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class UserManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('nik', 'LIKE', "%{$search}%")
                  ->orWhere('role', 'LIKE', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Sort functionality
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');

        $query->orderBy($sortBy, $sortDirection);

        $users = $query->paginate(15)->withQueryString();

        // Get stats
        $stats = [
            'total_users' => User::count(),
            'admin_count' => User::where('role', 'admin')->count(),
            'user_count' => User::where('role', 'user')->count(),
            'recent_registrations' => User::where('created_at', '>=', now()->subDays(7))->count(),
        ];

        return view('admin.users.index-working', compact('users', 'stats'));
    }

    /**
     * Show user details
     */
    public function show($id)
    {
        $user = User::findOrFail($id);

        // Get user's PB statistics if applicable
        $pbStats = [];
        if (class_exists('App\Models\Pbs')) {
            $pbStats = [
                'total_pb' => \App\Models\Pbs::where('user_id', $user->id)->count(),
                'approved_pb' => \App\Models\Pbs::where('user_id', $user->id)->where('status', 'approved')->count(),
                'pending_pb' => \App\Models\Pbs::where('user_id', $user->id)->where('status', 'pending')->count(),
                'rejected_pb' => \App\Models\Pbs::where('user_id', $user->id)->where('status', 'rejected')->count(),
            ];
        }

        return view('admin.users.show-working', compact('user', 'pbStats'));
    }

    /**
     * Show form for creating new user
     */
    public function create()
    {
        return view('admin.users.create-working');
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nik' => ['required', 'string', 'max:50', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:admin,user'],

        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'nik.required' => 'NIK wajib diisi.',
            'nik.max' => 'NIK maksimal 50 karakter.',
            'nik.unique' => 'NIK sudah terdaftar dalam sistem.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',
            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role harus admin atau user.',
        ]);

        try {
            $user = User::create([
                'name' => $request->name,
                'nik' => $request->nik,
                'password' => Hash::make($request->password),
                'role' => $request->role,

            ]);

            Log::info('New user created by admin', [
                'admin_id' => auth()->id(),
                'created_user_id' => $user->id,
                'created_user_nik' => $user->nik,
                'role' => $user->role
            ]);

            return redirect()->route('admin.users.index')
                ->with('success', 'Akun pengguna berhasil dibuat.');

        } catch (\Exception $e) {
            Log::error('Failed to create user', [
                'error' => $e->getMessage(),
                'admin_id' => auth()->id()
            ]);

            return back()->withInput()
                ->with('error', 'Gagal membuat akun pengguna. Silakan coba lagi.');
        }
    }

    /**
     * Show form for editing user
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);

        // Prevent admin from editing their own role
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.show', $user->id)
                ->with('warning', 'Anda tidak dapat mengedit role akun Anda sendiri.');
        }

        return view('admin.users.edit-working', compact('user'));
    }

    /**
     * Update user information
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Prevent admin from editing their own role
        if ($user->id === auth()->id() && $request->filled('role')) {
            return redirect()->route('admin.users.show', $user->id)
                ->with('warning', 'Anda tidak dapat mengubah role akun Anda sendiri.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nik' => ['required', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', 'in:admin,user'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'nik.required' => 'NIK wajib diisi.',
            'nik.max' => 'NIK maksimal 50 karakter.',
            'nik.unique' => 'NIK sudah terdaftar dalam sistem.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',
            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role harus admin atau user.',
        ]);

        try {
            $updateData = [
                'name' => $request->name,
                'nik' => $request->nik,
                'role' => $request->role,
                
            ];

            // Only update password if provided
            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $user->update($updateData);

            Log::info('User updated by admin', [
                'admin_id' => auth()->id(),
                'updated_user_id' => $user->id,
                'updated_fields' => array_keys($updateData)
            ]);

            return redirect()->route('admin.users.show', $user->id)
                ->with('success', 'Data pengguna berhasil diperbarui.');

        } catch (\Exception $e) {
            Log::error('Failed to update user', [
                'error' => $e->getMessage(),
                'admin_id' => auth()->id(),
                'user_id' => $user->id
            ]);

            return back()->withInput()
                ->with('error', 'Gagal memperbarui data pengguna. Silakan coba lagi.');
        }
    }

    /**
     * Reset user password
     */
    public function resetPassword(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.min' => 'Password minimal 8 karakter.',
            'new_password.confirmed' => 'Konfirmasi password tidak sesuai.',
        ]);

        try {
            $user->update([
                'password' => Hash::make($request->new_password),
            ]);

            Log::info('User password reset by admin', [
                'admin_id' => auth()->id(),
                'reset_user_id' => $user->id,
                'reset_user_nik' => $user->nik
            ]);

            return redirect()->route('admin.users.show', $user->id)
                ->with('success', 'Password pengguna berhasil direset.');

        } catch (\Exception $e) {
            Log::error('Failed to reset user password', [
                'error' => $e->getMessage(),
                'admin_id' => auth()->id(),
                'user_id' => $user->id
            ]);

            return back()->with('error', 'Gagal mereset password pengguna. Silakan coba lagi.');
        }
    }

    /**
     * Toggle user status (active/inactive)
     */
    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);

        // Prevent admin from deactivating themselves
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.show', $user->id)
                ->with('warning', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        try {
            $newStatus = $user->is_active ? 0 : 1;
            $user->update(['is_active' => $newStatus]);

            $statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';

            Log::info('User status toggled by admin', [
                'admin_id' => auth()->id(),
                'toggled_user_id' => $user->id,
                'new_status' => $newStatus
            ]);

            return redirect()->route('admin.users.show', $user->id)
                ->with('success', "Akun pengguna berhasil {$statusText}.");

        } catch (\Exception $e) {
            Log::error('Failed to toggle user status', [
                'error' => $e->getMessage(),
                'admin_id' => auth()->id(),
                'user_id' => $user->id
            ]);

            return back()->with('error', 'Gagal mengubah status pengguna. Silakan coba lagi.');
        }
    }

    /**
     * Delete user (soft delete)
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Prevent admin from deleting themselves
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('warning', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        try {
            // Check if user has related data (PBs, etc.)
            $hasRelatedData = false;
            if (class_exists('App\Models\Pbs')) {
                $hasRelatedData = \App\Models\Pbs::where('user_id', $user->id)->exists();
            }

            if ($hasRelatedData) {
                return redirect()->route('admin.users.show', $user->id)
                    ->with('warning', 'Pengguna tidak dapat dihapus karena memiliki data PB terkait.');
            }

            Log::info('User deleted by admin', [
                'admin_id' => auth()->id(),
                'deleted_user_id' => $user->id,
                'deleted_user_nik' => $user->nik
            ]);

            $user->delete();

            return redirect()->route('admin.users.index')
                ->with('success', 'Akun pengguna berhasil dihapus.');

        } catch (\Exception $e) {
            Log::error('Failed to delete user', [
                'error' => $e->getMessage(),
                'admin_id' => auth()->id(),
                'user_id' => $user->id
            ]);

            return redirect()->route('admin.users.show', $user->id)
                ->with('error', 'Gagal menghapus akun pengguna. Silakan coba lagi.');
        }
    }

    /**
     * Bulk actions for users
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => ['required', 'in:activate,deactivate,delete'],
            'user_ids' => ['required', 'array', 'min:1'],
            'user_ids.*' => ['exists:users,id'],
        ]);

        $userIds = $request->user_ids;
        $action = $request->action;

        // Remove current admin from bulk actions
        $userIds = array_filter($userIds, function($id) {
            return $id != auth()->id();
        });

        if (empty($userIds)) {
            return back()->with('warning', 'Tidak ada pengguna yang dapat diproses.');
        }

        try {
            $processedCount = 0;

            switch ($action) {
                case 'activate':
                    $processedCount = User::whereIn('id', $userIds)->update(['is_active' => 1]);
                    break;
                case 'deactivate':
                    $processedCount = User::whereIn('id', $userIds)->update(['is_active' => 0]);
                    break;
                case 'delete':
                    // Check for users with related data
                    $usersWithData = [];
                    if (class_exists('App\Models\Pbs')) {
                        $usersWithData = User::whereIn('id', $userIds)
                            ->whereHas('pbs')
                            ->pluck('name')
                            ->toArray();
                    }

                    if (!empty($usersWithData)) {
                        return back()->with('warning',
                            'Beberapa pengguna tidak dapat dihapus karena memiliki data PB: ' .
                            implode(', ', $usersWithData));
                    }

                    $processedCount = User::whereIn('id', $userIds)->delete();
                    break;
            }

            Log::info('Bulk action performed by admin', [
                'admin_id' => auth()->id(),
                'action' => $action,
                'user_ids' => $userIds,
                'processed_count' => $processedCount
            ]);

            $actionText = [
                'activate' => 'diaktifkan',
                'deactivate' => 'dinonaktifkan',
                'delete' => 'dihapus'
            ][$action];

            return redirect()->route('admin.users.index')
                ->with('success', "{$processedCount} pengguna berhasil {$actionText}.");

        } catch (\Exception $e) {
            Log::error('Failed to perform bulk action', [
                'error' => $e->getMessage(),
                'admin_id' => auth()->id(),
                'action' => $action,
                'user_ids' => $userIds
            ]);

            return back()->with('error', 'Gagal memproses aksi massal. Silakan coba lagi.');
        }
    }
}
