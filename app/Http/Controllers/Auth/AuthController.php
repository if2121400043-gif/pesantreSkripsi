<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Menampilkan halaman form login
    public function showLogin()
    {
        // CEK SESI: Memeriksa apakah pengguna sudah login sebelumnya.
        if (Auth::check()) {
            $user = Auth::user();
            $redirectUrl = $user->active_role->role->redirect_url ?? '/portal/beranda';
            return redirect($redirectUrl);
        }
        
        // Jika belum login, tampilkan halaman (view) form login.
        return view('auth.login');
    }

    // Memproses data yang dikirim dari form login
    public function login(Request $request)
    {
        // 1. Validasi Input
        $credentials = $request->validate([
            // Input bernama 'email' di form, tapi sebenarnya bisa diisi email ATAU username
            'email' => ['required', 'string'], 
            'password' => ['required'],
        ]);

        // 2. Fitur "Remember Me" (Ingat Saya)
        // Mengambil nilai boolean dari input 'remember' (jika dicentang bernilai true)
        $remember = $request->boolean('remember');

        // 3. LOGIKA FLEKSIBEL (Email atau Username): 
        // Menggunakan filter PHP bawaan untuk mengecek teks yang dimasukkan.
        // Jika formatnya valid seperti email (contoh: a@b.com), maka fieldType = 'email'.
        // Jika tidak valid sebagai email (contoh: 'admin123'), maka fieldType = 'username'.
        $fieldType = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // 4. Proses Autentikasi (Pencocokan ke Database)
        // Auth::attempt() akan mencari user di database yang cocok dengan kriteria berikut:
        // - Kolom (email/username) = input user
        // - Kolom password = password user (di-hash otomatis oleh Laravel)
        // - Kolom 'is_active' HARUS bernilai true (hanya akun aktif yang boleh login)
        if (Auth::attempt([$fieldType => $request->email, 'password' => $request->password, 'is_active' => true], $remember)) {
            
            // 5. Keamanan Sesi (PENTING)
            // Mengubah ID Sesi untuk mencegah serangan "Session Fixation" (peretasan sesi).
            $request->session()->regenerate();

            // 6. Jika berhasil login, arahkan berdasarkan peran pengguna
            return $this->redirectBasedOnRole(Auth::user());
        }

        // 7. Jika Gagal Login
        // Kembalikan pengguna ke halaman sebelumnya (form login) dengan pesan error.
        // 'onlyInput('email')' memastikan kolom email tetap terisi, tapi password dikosongkan.
        return back()->withErrors([
            'email' => 'Aduhhh.. Email atau Password yang kamu masukkan salah nihh!! Yuk coba lagi!!',
        ])->onlyInput('email');
    }

    // Memproses log keluar (logout) pengguna
    public function logout(Request $request)
    {
        // Menghapus status autentikasi pengguna
        Auth::logout();
        
        // Menghapus seluruh data sesi milik pengguna tersebut
        $request->session()->invalidate();
        
        // Memperbarui token CSRF (Cross-Site Request Forgery) demi keamanan form
        $request->session()->regenerateToken();

        // Mengarahkan pengguna kembali ke halaman login
        return redirect('/login');
    }

    // ── Fungsi Bantuan (Helper) Internal ──
    // Fungsi ini dilindungi (protected) karena hanya digunakan di dalam Controller ini saja
    protected function redirectBasedOnRole($user)
    {
        // Mengambil relasi peran aktif dari model User
        $activeRole = $user->active_role;
        
        // Memeriksa apakah user memiliki data 'active_role' dan di dalamnya terdapat data 'role'
        if ($activeRole && $activeRole->role) {
            // redirect()->intended() adalah fitur pintar Laravel.
            // Jika user mencoba mengakses halaman rahasia (misal /admin) lalu ditendang ke /login,
            // 'intended' akan mengarahkan user KEMBALI ke /admin setelah login berhasil.
            // Jika user langsung ke /login, maka akan diarahkan ke 'redirect_url' default dari role-nya.
            return redirect()->intended($activeRole->role->redirect_url);
        }

        // LOGIKA PROTEKSI (Fallback)
        // Jika karena suatu alasan user berhasil login tetapi di database dia tidak punya peran/role,
        // tendang dia kembali ke halaman login dengan pesan error.
        return redirect('/login')->withErrors(['email' => 'Akun kamu tidak memiliki peran yang valid.']);
    }
}