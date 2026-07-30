<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')
            ->withCount('userRoles')
            ->orderBy('label')
            ->get();

        $permissions = Permission::orderBy('grup')->orderBy('nama')->get();

        return view('admin.pengaturan.roles.index', compact('roles', 'permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:50|unique:roles,nama',
            'label' => 'required|string|max:100',
            'redirect_url' => 'nullable|string|max:200',
            'deskripsi' => 'nullable|string|max:255',
            'permission_ids' => 'nullable|array',
            'permission_ids.*' => 'exists:permissions,id',
        ]);

        $role = Role::create([
            'nama' => $validated['nama'],
            'label' => $validated['label'],
            'redirect_url' => $validated['redirect_url'] ?? '/admin/dashboard',
            'deskripsi' => $validated['deskripsi'] ?? null,
        ]);

        if (!empty($validated['permission_ids'])) {
            $role->permissions()->sync($validated['permission_ids']);
        }

        return back()->with('success', 'Role berhasil ditambahkan.');
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:50|unique:roles,nama,' . $role->id,
            'label' => 'required|string|max:100',
            'redirect_url' => 'nullable|string|max:200',
            'deskripsi' => 'nullable|string|max:255',
            'permission_ids' => 'nullable|array',
            'permission_ids.*' => 'exists:permissions,id',
        ]);

        $role->update([
            'nama' => $validated['nama'],
            'label' => $validated['label'],
            'redirect_url' => $validated['redirect_url'] ?? $role->redirect_url,
            'deskripsi' => $validated['deskripsi'] ?? null,
        ]);

        $role->permissions()->sync($validated['permission_ids'] ?? []);

        return back()->with('success', 'Role berhasil diperbarui.');
    }

    public function destroy(Role $role)
    {
        if (in_array($role->nama, ['SUPER_ADMIN'])) {
            return back()->with('error', 'Role bawaan sistem tidak dapat dihapus.');
        }

        if ($role->userRoles()->count() > 0) {
            return back()->with('error', 'Role tidak dapat dihapus karena masih digunakan oleh user.');
        }

        $role->permissions()->detach();
        $role->delete();
        return back()->with('success', 'Role berhasil dihapus.');
    }
}
