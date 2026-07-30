<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\UserRole;
use App\Models\Orang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['orang', 'roles.role']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('orang', function($q2) use ($search) {
                      $q2->where('nama_lengkap', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('role_id')) {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('role_id', $request->role_id);
            });
        }

        $users = $query->orderBy('updated_at', 'desc')->paginate(15)->withQueryString();
        $roles = Role::orderBy('label')->get();

        return view('admin.pengaturan.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::orderBy('label')->get();
        return view('admin.pengaturan.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:50|unique:users,username',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'orang_id' => 'nullable|exists:orang,id',
            'role_ids' => 'required|array|min:1',
            'role_ids.*' => 'exists:roles,id',
        ]);

        $user = User::create([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'orang_id' => $validated['orang_id'] ?? null,
            'is_active' => $request->has('is_active'),
            'email_verified_at' => now(),
        ]);

        // Assign roles
        foreach ($validated['role_ids'] as $index => $roleId) {
            UserRole::create([
                'user_id' => $user->id,
                'role_id' => $roleId,
                'is_default' => $index === 0,
                'is_active' => true,
            ]);
        }

        return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        $user->load(['orang', 'roles']);
        $roles = Role::orderBy('label')->get();
        $userRoleIds = $user->roles->pluck('role_id')->toArray();
        return view('admin.pengaturan.users.edit', compact('user', 'roles', 'userRoleIds'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:50|unique:users,username,' . $user->id,
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'role_ids' => 'required|array|min:1',
            'role_ids.*' => 'exists:roles,id',
        ]);

        $updateData = [
            'username' => $validated['username'],
            'email' => $validated['email'],
            'is_active' => $request->has('is_active'),
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        // Sync roles: delete old, insert new
        UserRole::where('user_id', $user->id)->delete();
        foreach ($validated['role_ids'] as $index => $roleId) {
            UserRole::create([
                'user_id' => $user->id,
                'role_id' => $roleId,
                'is_default' => $index === 0,
                'is_active' => true,
            ]);
        }

        // Touch user to update updated_at timestamp (e.g. if only roles changed)
        $user->touch();

        return redirect()->route('admin.users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        UserRole::where('user_id', $user->id)->delete();
        $user->delete();
        return back()->with('success', 'User berhasil dihapus.');
    }

    // API: search orang for linking
    public function searchOrang(Request $request)
    {
        $q = $request->get('q');
        if (strlen($q) < 3) return response()->json([]);

        $orangs = Orang::where('nama_lengkap', 'like', "%{$q}%")
            ->orWhere('niup', 'like', "%{$q}%")
            ->take(10)->get()
            ->map(fn($o) => ['id' => $o->id, 'text' => "{$o->nama_lengkap} ({$o->niup})"]);

        return response()->json($orangs);
    }
}
