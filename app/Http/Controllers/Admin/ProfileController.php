<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function index()
    {
        return view('admin.profile.index');
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'email'            => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'current_password' => ['nullable', 'required_with:new_password', 'current_password'],
            'new_password'     => ['nullable', 'string', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
            'profile_photo'    => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if (!empty($validated['new_password'])) {
            $user->password = \Illuminate\Support\Facades\Hash::make($validated['new_password']);
        }

        if ($request->hasFile('profile_photo')) {
            // Hapus foto lama jika ada
            if ($user->profile_photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->profile_photo)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->profile_photo);
            }
            
            $path = $request->file('profile_photo')->store('avatars', 'public');
            $user->profile_photo = $path;
        }

        $user->save();

        return back()->with('success', 'Profil admin berhasil diperbarui.');
    }
}
