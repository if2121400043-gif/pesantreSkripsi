<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BeritaController extends Controller
{
    public function index(Request $request)
    {
        $query = Berita::with('penulis');

        // Filter berdasarkan tipe
        if ($request->filled('tipe') && in_array($request->tipe, ['berita', 'pengumuman'])) {
            $query->where('tipe', $request->tipe);
        }

        // Filter berdasarkan status
        if ($request->filled('status')) {
            if ($request->status === 'published') {
                $query->where('is_published', true);
            } elseif ($request->status === 'draft') {
                $query->where('is_published', false);
            } elseif ($request->status === 'pinned') {
                $query->where('is_pinned', true);
            }
        }

        // Pencarian berdasarkan judul
        if ($request->filled('search')) {
            $query->where('judul', 'like', '%' . $request->search . '%');
        }

        $beritas = $query->orderBy('is_pinned', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        // Hitung statistik
        $stats = [
            'total' => Berita::count(),
            'berita' => Berita::berita()->count(),
            'pengumuman' => Berita::pengumuman()->count(),
            'published' => Berita::published()->count(),
            'draft' => Berita::where('is_published', false)->count(),
            'pinned' => Berita::pinned()->count(),
        ];

        return view('admin.berita.index', compact('beritas', 'stats'));
    }

    public function create()
    {
        return view('admin.berita.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'tipe' => 'required|in:berita,pengumuman',
            'ringkasan' => 'nullable|string|max:500',
            'konten' => 'required|string',
            'gambar_cover' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_published' => 'nullable|boolean',
            'is_pinned' => 'nullable|boolean',
        ]);

        $data = $request->only(['judul', 'tipe', 'ringkasan', 'konten']);
        $data['slug'] = Str::slug($request->judul);
        $data['is_published'] = $request->has('is_published');
        $data['is_pinned'] = $request->has('is_pinned');
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

        $label = $data['tipe'] === 'pengumuman' ? 'Pengumuman' : 'Berita';
        return redirect()->route('admin.berita.index')->with('success', "{$label} berhasil dipublikasikan!");
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
            'tipe' => 'required|in:berita,pengumuman',
            'ringkasan' => 'nullable|string|max:500',
            'konten' => 'required|string',
            'gambar_cover' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_published' => 'nullable|boolean',
            'is_pinned' => 'nullable|boolean',
        ]);

        $data = $request->only(['judul', 'tipe', 'ringkasan', 'konten']);
        $data['is_published'] = $request->has('is_published');
        $data['is_pinned'] = $request->has('is_pinned');

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

        $label = $data['tipe'] === 'pengumuman' ? 'Pengumuman' : 'Berita';
        return redirect()->route('admin.berita.index')->with('success', "{$label} berhasil diperbarui!");
    }

    public function destroy(Berita $beritum)
    {
        $berita = $beritum;

        if ($berita->gambar_cover) {
            Storage::disk('public')->delete($berita->gambar_cover);
        }

        $berita->delete();

        return redirect()->route('admin.berita.index')->with('success', 'Berhasil dihapus!');
    }

    /**
     * Toggle publish status via AJAX
     */
    public function togglePublish(Berita $beritum)
    {
        $berita = $beritum;
        $berita->is_published = !$berita->is_published;
        
        if ($berita->is_published && !$berita->published_at) {
            $berita->published_at = now();
        }
        
        $berita->save();

        return back()->with('success', $berita->is_published ? 'Berhasil dipublikasikan!' : 'Disimpan sebagai draft.');
    }

    /**
     * Toggle pin status via AJAX
     */
    public function togglePin(Berita $beritum)
    {
        $berita = $beritum;
        $berita->is_pinned = !$berita->is_pinned;
        $berita->save();

        return back()->with('success', $berita->is_pinned ? 'Disematkan di atas!' : 'Sematan dihapus.');
    }
}

