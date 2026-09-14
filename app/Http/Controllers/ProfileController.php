<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /** "My Profile" hub — basic info, (supplier) business info, and change-password, all on one page, for any role. */
    public function edit()
    {
        $user = Auth::user()->load('supplierProfile');
        return view('profile.edit', compact('user'));
    }

    /** Update name/phone/photo, and — for suppliers — business_name/business_address/bkash_number. */
    public function update(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:20'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ];

        if ($user->isSupplier()) {
            $rules['business_name'] = ['required', 'string', 'max:150'];
            $rules['business_address'] = ['nullable', 'string', 'max:255'];
            $rules['bkash_number'] = ['nullable', 'string', 'max:20'];
        }

        $data = $request->validate($rules);

        $updates = [
            'name' => $data['name'],
            'phone' => $data['phone'] ?? null,
        ];

        // Replace the stored photo (and delete the old one) only when a new
        // file was actually uploaded -- otherwise leave profile_photo as is.
        if ($request->hasFile('photo')) {
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $updates['profile_photo'] = $request->file('photo')->store('profile-photos', 'public');
        }

        $user->update($updates);

        if ($user->isSupplier() && $user->supplierProfile) {
            $user->supplierProfile->update([
                'business_name' => $data['business_name'],
                'business_address' => $data['business_address'] ?? null,
                'bkash_number' => $data['bkash_number'] ?? null,
            ]);
        }

        return back()->with('status', 'Profile updated.');
    }

    /** Remove the current profile photo (falls back to the initial-letter avatar). */
    public function removePhoto()
    {
        $user = Auth::user();

        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
            $user->update(['profile_photo' => null]);
        }

        return back()->with('status', 'Profile photo removed.');
    }

    /** Change password: requires the current password, and the new one must be different from it. */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'different:current_password'],
        ], [
            'password.different' => 'Your new password must be different from your current password.',
        ]);

        Auth::user()->update(['password' => Hash::make($request->password)]);

        return back()->with('status', 'Password updated.');
    }
}
