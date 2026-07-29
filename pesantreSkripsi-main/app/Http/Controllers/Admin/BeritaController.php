<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BeritaController extends Controller
{
    public function index()
    {
        $beritas = Berita::with('penulis')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.berita.index', compact('beritas'));
    }

    public function create()
    {
        return view('admin.berita.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'ringkasan' => 'nullable|string|max:500',
            'konten' => 'required|string',
            'gambar_cover' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_published' => 'nullable|boolean',
        ]);

        $data = $request->only(['judul', 'ringkasan', 'konten']);
        $data['slug'] = Str::slug($request->judul);
        $data['is_published'] = $request->has('is_published');
        $data['penulis_id'] = auth()->id();

        if ($data['is_published']) {
            $data['published_at'] = now();
        }

        if ($request->hasFile('gambar_cover')) {
            $data['gambar_cover'] = $request->file('gambar_cover')->store('berita', 'public');
        }

        // Ensure unique slug
        $count = Berita::where('slug', $data['slug'])->count();
        if ($count > 0) {
            $data['slug'] .= '-' . ($count + 1);
        }

        Berita::create($data);

        return redirect()->route('admin.berita.index')->with('success', 'Berita berhasil dipublikasikan!');
    }

    public function edit(Berita $beritum)
    {
        // Laravel pluralizes 'berita' -> 'beritum' for route model binding
        $berita = $beritum;
        return view('admin.berita.edit', compact('berita'));
    }

    public function update(Request $request, Berita $beritum)
    {
        $berita = $beritum;

        $request->validate([
            'judul' => 'required|string|max:255',
            'ringkasan' => 'nullable|string|max:500',
            'konten' => 'required|string',
            'gambar_cover' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_published' => 'nullable|boolean',
        ]);

        $data = $request->only(['judul', 'ringkasan', 'konten']);
        $data['is_published'] = $request->has('is_published');

        if ($berita->isDirty('judul') || $berita->judul !== $request->judul) {
            $data['slug'] = Str::slug($request->judul);
            $count = Berita::where('slug', $data['slug'])->where('id', '!=', $berita->id)->count();
            if ($count > 0) {
                $data['slug'] .= '-' . ($count + 1);
            }
        }

        if ($data['is_published'] && !$berita->published_at) {
            $data['published_at'] = now();
        }

        if ($request->hasFile('gambar_cover')) {
            // hapus cover lama jika ada
            if ($berita->gambar_cover) {
                Storage::disk('public')->delete($berita->gambar_cover);
            }
            $data['gambar_cover'] = $request->file('gambar_cover')->store('berita', 'public');
        }

        $berita->update($data);

        return redirect()->route('admin.berita.index')->with('success', 'Berita berhasil diperbarui!');
    }

    public function destroy(Berita $beritum)
    {
        $berita = $beritum;

        if ($berita->gambar_cover) {
            Storage::disk('public')->delete($berita->gambar_cover);
        }

        $berita->delete();

        return redirect()->route('admin.berita.index')->with('success', 'Berita berhasil dihapus!');
    }
}
