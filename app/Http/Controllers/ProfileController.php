<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('profile.edit', ['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'            => ['required', 'string', 'max:255'],
            'email'           => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone'           => ['nullable', 'string', 'max:20'],
            'dob'             => ['nullable', 'date', 'before:today'],
            'gender'          => ['nullable', 'in:male,female,other'],
            'address'         => ['nullable', 'string', 'max:500'],
            'designation'     => ['nullable', 'string', 'max:50'],
            'specialty'       => ['nullable', 'string', 'max:100'],
            'qualification'   => ['nullable', 'string', 'max:200'],
            'registration_no' => ['nullable', 'string', 'max:50'],
            'bio'             => ['nullable', 'string', 'max:1000'],
            'profile_image'   => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        // Only update fields that were actually present in this request (each tab
        // submits only its own fields; absent fields must not overwrite existing data).
        $toUpdate = array_filter(
            $validated,
            fn($k) => $k !== 'profile_image' && $request->exists($k),
            ARRAY_FILTER_USE_KEY
        );

        if ($request->hasFile('profile_image')) {
            $dir = public_path('profile_images');
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            if ($user->profile_image) {
                $old = $dir . '/' . $user->profile_image;
                if (file_exists($old)) {
                    unlink($old);
                }
            }

            $filename = 'user_' . $user->id . '_' . time() . '.' . $request->file('profile_image')->getClientOriginalExtension();
            $request->file('profile_image')->move($dir, $filename);
            $toUpdate['profile_image'] = $filename;
        }

        $user->update($toUpdate);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect(route('profile.edit') . '#tab-password')
                ->withErrors(['current_password' => 'The current password is incorrect.'])
                ->withInput();
        }

        $user->update(['password' => Hash::make($request->password)]);

        return redirect(route('profile.edit') . '#tab-password')
            ->with('success', 'Password changed successfully.');
    }
}
