<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100|unique:permissions,nama',
            'label' => 'required|string|max:150',
            'grup' => 'required|string|max:50',
        ]);

        Permission::create($validated);

        return back()->with('success', 'Hak akses (Permission) berhasil ditambahkan.');
    }

    public function destroy(Permission $permission)
    {
        // Detach from all roles first
        $permission->roles()->detach();
        $permission->delete();

        return back()->with('success', 'Hak akses (Permission) berhasil dihapus.');
    }
}
