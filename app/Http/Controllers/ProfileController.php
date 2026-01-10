<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Teacher;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $teacher = null;

        if ($user->role === 'teacher') {
            $teacher = Teacher::where('user_id', $user->id)->first();
        }

        return view('profile.edit', compact('user', 'teacher'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6|confirmed',
        ];

        if ($user->role === 'teacher') {
            $rules['nip'] = 'required|string|max:20|unique:teachers,nip,' . ($user->teacher->id ?? 0);
            $rules['phone'] = 'nullable|string|max:20';
            $rules['address'] = 'nullable|string';
            $rules['gender'] = 'required|in:L,P';
        }

        $request->validate($rules);

        // Update User Data
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        // Update Teacher Data if applicable
        if ($user->role === 'teacher') {
            $teacherData = [
                'nip' => $request->nip,
                'name' => $request->name, // Sync name
                'phone' => $request->phone,
                'address' => $request->address,
                'gender' => $request->gender,
            ];

            Teacher::updateOrCreate(
                ['user_id' => $user->id],
                $teacherData
            );
        }

        return redirect()->route('profile.edit')->with('success', 'Profil berhasil diperbarui.');
    }
}
