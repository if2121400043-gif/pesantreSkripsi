<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\UserRole;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user()->load('orang');
        return view('auth.profil', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'username' => 'required|string|max:50|unique:users,username,' . $user->id,
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update($validated);

        return back()->with('success', 'Informasi profil berhasil diperbarui.');
    }

    public function editPassword()
    {
        return view('auth.ganti_password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = auth()->user();
        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Password berhasil diperbarui.');
    }

    public function editRole()
    {
        $userRoles = auth()->user()->roles()->with('role')->where('is_active', true)->get();
        return view('auth.ganti_peran', compact('userRoles'));
    }

    public function updateRole(Request $request)
    {
        $request->validate([
            'user_role_id' => 'required|exists:user_role,id',
        ]);

        $user = auth()->user();
        $selectedUserRole = UserRole::where('user_id', $user->id)
            ->where('id', $request->user_role_id)
            ->firstOrFail();

        // Set all user roles default = false
        UserRole::where('user_id', $user->id)->update(['is_default' => false]);
        
        // Set selected role default = true
        $selectedUserRole->update(['is_default' => true]);

        return redirect($selectedUserRole->role->redirect_url)->with('success', 'Peran aktif berhasil diubah ke ' . $selectedUserRole->role->label);
    }
}
