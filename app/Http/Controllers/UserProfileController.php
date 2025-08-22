<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ProfileChangeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;

class UserProfileController extends Controller
{
    /**
     * Show the user's profile form.
     */
    public function edit(Request $request)
    {
        return view('profile.edit-working', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nik' => ['required', 'string', 'max:50', 'unique:users,nik,' . $user->id],
            'current_nik' => ['required', 'string'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'remove_photo' => ['nullable', 'boolean']
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'nik.required' => 'NIK wajib diisi.',
            'nik.max' => 'NIK maksimal 50 karakter.',
            'nik.unique' => 'NIK sudah digunakan oleh pengguna lain.',
            'current_nik.required' => 'NIK saat ini wajib diisi untuk verifikasi.',
            'profile_photo.image' => 'File harus berupa gambar.',
            'profile_photo.mimes' => 'Format gambar yang diizinkan: JPEG, PNG, JPG, GIF, WEBP.',
            'profile_photo.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        // Verify current NIK
        if ($request->current_nik !== $user->nik) {
            return back()->withErrors([
                'current_nik' => 'NIK saat ini tidak sesuai.'
            ])->withInput();
        }

        try {
            $hasChanges = false;
            $approvalNeeded = false;
            $messages = [];

            // Handle profile photo upload/removal
            if ($request->hasFile('profile_photo')) {
                // Delete old photo if exists
                if ($user->profile_photo) {
                    Storage::disk('public')->delete('profile-photos/' . $user->profile_photo);
                }

                // Upload new photo
                $photo = $request->file('profile_photo');
                $photoName = time() . '_' . $photo->getClientOriginalName();
                $photo->storeAs('profile-photos', $photoName, 'public');

                $user->update(['profile_photo' => $photoName]);
                $messages[] = 'Foto profil berhasil diperbarui.';
                $hasChanges = true;

                Log::info('Profile photo updated', [
                    'user_id' => $user->id,
                    'photo_name' => $photoName,
                ]);
            } else if ($request->remove_photo == '1' && $user->profile_photo) {
                // Remove existing photo
                Storage::disk('public')->delete('profile-photos/' . $user->profile_photo);
                $user->update(['profile_photo' => null]);
                $messages[] = 'Foto profil berhasil dihapus.';
                $hasChanges = true;

                Log::info('Profile photo removed', [
                    'user_id' => $user->id,
                ]);
            }

            // Check for name changes (can be applied immediately)
            if ($request->name !== $user->name) {
                $user->update(['name' => $request->name]);
                $messages[] = 'Nama berhasil diperbarui.';
                $hasChanges = true;

                Log::info('User name updated immediately', [
                    'user_id' => $user->id,
                    'old_name' => $user->name,
                    'new_name' => $request->name,
                ]);
            }

            // Check for NIK changes (requires admin approval)
            if ($request->nik !== $user->nik) {
                // Check if there's already a pending NIK change request
                $existingRequest = ProfileChangeRequest::where('user_id', $user->id)
                    ->where('request_type', 'nik_change')
                    ->where('status', 'pending')
                    ->first();

                if ($existingRequest) {
                    return back()->withErrors([
                        'nik' => 'Anda sudah memiliki permintaan perubahan NIK yang sedang menunggu persetujuan admin.'
                    ])->withInput();
                }

                // Create new NIK change request
                ProfileChangeRequest::create([
                    'user_id' => $user->id,
                    'request_type' => 'nik_change',
                    'old_data' => ['nik' => $user->nik],
                    'new_data' => ['nik' => $request->nik],
                    'status' => 'pending',
                ]);

                $messages[] = 'Permintaan perubahan NIK telah dikirim dan menunggu persetujuan admin.';
                $approvalNeeded = true;
                $hasChanges = true;

                Log::info('NIK change request created', [
                    'user_id' => $user->id,
                    'old_nik' => $user->nik,
                    'new_nik' => $request->nik,
                ]);
            }

            if (!$hasChanges) {
                return back()->with('status', 'Tidak ada perubahan yang dilakukan.');
            }

            $statusMessage = implode(' ', $messages);

            if ($approvalNeeded) {
                $statusMessage .= ' Anda akan mendapat notifikasi setelah admin memproses permintaan Anda.';
            }

            return back()->with('status', $statusMessage);

        } catch (\Exception $e) {
            Log::error('Failed to update user profile', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withErrors([
                'general' => 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.'
            ])->withInput();
        }
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('status', 'Password berhasil diperbarui.');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request)
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
