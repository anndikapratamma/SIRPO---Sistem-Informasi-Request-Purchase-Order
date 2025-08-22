<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit-working', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Validation
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nik' => 'required|string|max:16|unique:users,nik,' . $user->id,
            'current_nik' => 'required|string',
            'password' => 'nullable|string|min:8|confirmed',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'remove_photo' => 'nullable|string'
        ]);

        // Verify current NIK
        if ($validated['current_nik'] !== $user->nik) {
            return back()->withErrors(['current_nik' => 'NIK saat ini tidak sesuai.'])->withInput();
        }

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            try {
                // Delete old photo if exists
                if ($user->profile_photo && Storage::disk('public')->exists('profile-photos/' . $user->profile_photo)) {
                    Storage::disk('public')->delete('profile-photos/' . $user->profile_photo);
                }

                // Store new photo
                $photo = $request->file('profile_photo');
                $photoName = time() . '_' . $user->id . '.' . $photo->getClientOriginalExtension();
                $photo->storeAs('profile-photos', $photoName, 'public');
                $user->profile_photo = $photoName;
            } catch (\Exception $e) {
                return back()->withErrors(['profile_photo' => 'Gagal upload foto: ' . $e->getMessage()])->withInput();
            }
        }

        // Handle photo removal
        if ($request->input('remove_photo') == '1') {
            try {
                if ($user->profile_photo && Storage::disk('public')->exists('profile-photos/' . $user->profile_photo)) {
                    Storage::disk('public')->delete('profile-photos/' . $user->profile_photo);
                }
                $user->profile_photo = null;
            } catch (\Exception $e) {
                return back()->withErrors(['profile_photo' => 'Gagal hapus foto: ' . $e->getMessage()])->withInput();
            }
        }

        // Update user data
        $user->name = $validated['name'];
        $user->nik = $validated['nik'];

        // Update password if provided
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        // Save changes
        try {
            $user->save();
            return back()->with('status', 'Profil berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->withErrors(['general' => 'Gagal menyimpan perubahan: ' . $e->getMessage()])->withInput();
        }
    }    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Delete profile photo if exists
        if ($user->profile_photo && Storage::disk('public')->exists('profile-photos/' . $user->profile_photo)) {
            Storage::disk('public')->delete('profile-photos/' . $user->profile_photo);
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
