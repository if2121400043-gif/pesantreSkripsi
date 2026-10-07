<?php

namespace App\Http\Controllers\PortalCalon;

use App\Http\Controllers\Controller;
use App\Models\CalonSantri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PortalCalonController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        
        // Find CalonSantri by matching the username with no_pendaftaran
        $calonSantri = CalonSantri::where('no_pendaftaran', $user->username)->first();

        // If not found, perhaps they were registered differently, fallback to email
        if (!$calonSantri) {
            $calonSantri = CalonSantri::where('email', $user->email)->first();
        }

        return view('portal_calon.dashboard', compact('calonSantri', 'user'));
    }
}
