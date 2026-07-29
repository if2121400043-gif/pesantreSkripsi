<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ForcePasswordController extends Controller
{
    public function create()
    {
        // If not forced, redirect to dashboard
        if (!auth()->user()->must_change_password) {
            return redirect()->route('dashboard');
        }

        return view('auth.force_password_change');
    }

    public function store(Request $request)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        $user = auth()->user();
        $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        $user->must_change_password = false;
        $user->save();

        $redirectUrl = $user->active_role->role->redirect_url ?? '/';
        return redirect($redirectUrl)->with('success', 'Password berhasil diperbarui. Selamat datang di sistem!');
    }
}