<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProfileChangeRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ProfileChangeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Display pending profile change requests
     */
    public function index(Request $request)
    {
        $query = ProfileChangeRequest::with(['user', 'approvedBy'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('request_type', $request->type);
        }

        // Search by user name or NIK
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('nik', 'LIKE', "%{$search}%");
            });
        }

        $changeRequests = $query->paginate(15)->appends($request->query());

        // Get stats
        $stats = [
            'pending_count' => ProfileChangeRequest::pending()->count(),
            'approved_count' => ProfileChangeRequest::approved()->count(),
            'rejected_count' => ProfileChangeRequest::rejected()->count(),
            'nik_change_count' => ProfileChangeRequest::nikChanges()->pending()->count(),
        ];

        return view('admin.profile-changes.index-working', compact('changeRequests', 'stats'));
    }

    /**
     * Show details of a profile change request
     */
    public function show($id)
    {
        $changeRequest = ProfileChangeRequest::with(['user', 'approvedBy'])->findOrFail($id);

        return view('admin.profile-changes.show-working', compact('changeRequest'));
    }

    /**
     * Approve a profile change request
     */
    public function approve(Request $request, $id)
{
    Log::info('=== Approve triggered ===', [
        'id' => $id,
        'admin_id' => auth()->id()
    ]);

    $changeRequest = ProfileChangeRequest::findOrFail($id);

    if (!$changeRequest->isPending()) {
        return back()->with('error', 'Permintaan sudah diproses sebelumnya.');
    }

    $request->validate([
        'admin_notes' => ['nullable', 'string', 'max:1000'],
    ]);

    try {
        DB::transaction(function () use ($changeRequest, $request) {
            $user = $changeRequest->user;
            $newData = $changeRequest->new_data ?? [];

            match ($changeRequest->request_type) {
                'nik_change'  => $user->update(['nik' => $newData['nik'] ?? null]),
                'name_change' => $user->update(['name' => $newData['name'] ?? null]),
                default       => null,
            };

            $changeRequest->approve(auth()->user()->nik, $request->admin_notes);
        });

        Log::info('Profile change request approved', [
            'admin_id'   => auth()->id(),
            'request_id' => $changeRequest->id,
            'user_id'    => $changeRequest->user_id,    
            'type'       => $changeRequest->request_type
        ]);

        return redirect()->route('admin.profile-changes.index')
            ->with('success', 'Permintaan perubahan berhasil disetujui dan diterapkan.');

    } catch (\Throwable $e) {
        Log::error('Failed to approve profile change request', [
            'error'      => $e->getMessage(),
            'trace'      => $e->getTraceAsString(),
            'admin_id'   => auth()->id(),
            'request_id' => $changeRequest->id
        ]);

        return back()->with('error', 'Gagal menyetujui permintaan. Silakan coba lagi.');
    }
}


    /**
     * Reject a profile change request
     */
    public function reject(Request $request, $id)
    {
        $changeRequest = ProfileChangeRequest::findOrFail($id);

        if (!$changeRequest->isPending()) {
            return back()->with('error', 'Permintaan sudah diproses sebelumnya.');
        }

        $request->validate([
            'admin_notes' => ['required', 'string', 'max:1000'],
        ], [
            'admin_notes.required' => 'Alasan penolakan wajib diisi.',
        ]);

        try {
            $changeRequest->reject(auth()->id(), $request->admin_notes);

            Log::info('Profile change request rejected', [
                'admin_id' => auth()->id(),
                'request_id' => $changeRequest->id,
                'user_id' => $changeRequest->user_id,
                'reason' => $request->admin_notes
            ]);

            return redirect()->route('admin.profile-changes.index')
                ->with('success', 'Permintaan perubahan berhasil ditolak.');

        } catch (\Exception $e) {
            Log::error('Failed to reject profile change request', [
                'error' => $e->getMessage(),
                'admin_id' => auth()->id(),
                'request_id' => $changeRequest->id
            ]);

            return back()->with('error', 'Gagal menolak permintaan. Silakan coba lagi.');
        }
    }

    /**
     * Bulk action for change requests
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => ['required', 'in:approve,reject'],
            'request_ids' => ['required', 'array', 'min:1'],
            'request_ids.*' => ['exists:profile_change_requests,id'],
            'bulk_admin_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $requestIds = $request->request_ids;
        $action = $request->action;
        $notes = $request->bulk_admin_notes;

        try {
            DB::beginTransaction();

            $processedCount = 0;
            $changeRequests = ProfileChangeRequest::whereIn('id', $requestIds)
                ->where('status', 'pending')
                ->get();

            foreach ($changeRequests as $changeRequest) {
                if ($action === 'approve') {
                    // Apply changes to user
                    $user = $changeRequest->user;
                    $newData = $changeRequest->new_data;

                    if ($changeRequest->request_type === 'nik_change') {
                        $user->update(['nik' => $newData['nik']]);
                    } elseif ($changeRequest->request_type === 'name_change') {
                        $user->update(['name' => $newData['name']]);
                    }

                    $changeRequest->approve(auth()->id(), $notes);
                } else {
                    $changeRequest->reject(auth()->id(), $notes ?: 'Bulk rejection');
                }

                $processedCount++;
            }

            DB::commit();

            $actionText = $action === 'approve' ? 'disetujui' : 'ditolak';

            Log::info('Bulk profile change action performed', [
                'admin_id' => auth()->id(),
                'action' => $action,
                'processed_count' => $processedCount,
                'request_ids' => $requestIds
            ]);

            return redirect()->route('admin.profile-changes.index')
                ->with('success', "{$processedCount} permintaan berhasil {$actionText}.");

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to perform bulk action on profile changes', [
                'error' => $e->getMessage(),
                'admin_id' => auth()->id(),
                'action' => $action,
                'request_ids' => $requestIds
            ]);

            return back()->with('error', 'Gagal memproses permintaan massal. Silakan coba lagi.');
        }
    }
}
