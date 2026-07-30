<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    public function index()
    {
        $medias = Media::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.media.index', compact('medias'));
    }

    public function create()
    {
        return view('admin.media.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'tipe' => 'required|in:IMAGE,VIDEO',
            'kategori' => 'nullable|string|max:50',
            'deskripsi' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $data = $request->only(['judul', 'tipe', 'kategori', 'deskripsi']);
        $data['is_active'] = $request->has('is_active');

        if ($request->tipe === 'IMAGE') {
            $request->validate([
                'file_image' => 'required|image|mimes:jpg,jpeg,png,webp',
            ]);

            $file = $request->file('file_image');
            $processedPath = $this->processAndStoreImage($file);

            if (!$processedPath) {
                return back()->with('error', 'Gagal memproses gambar. Pastikan format benar.')->withInput();
            }

            // Check final size
            if (Storage::disk('public')->size($processedPath) > 1024 * 1024) {
                Storage::disk('public')->delete($processedPath);
                return back()->with('error', 'Gambar setelah dikompresi masih melebihi 1 MB. Silakan gunakan gambar dengan dimensi lebih kecil.')->withInput();
            }

            $data['url'] = $processedPath;
        } else {
            $request->validate([
                'url_video' => 'required|url',
            ]);
            $data['url'] = $request->url_video;
        }

        Media::create($data);

        return redirect()->route('admin.media.index')->with('success', 'Media berhasil ditambahkan!');
    }

    public function edit(Media $medium)
    {
        $media = $medium;
        return view('admin.media.edit', compact('media'));
    }

    public function update(Request $request, Media $medium)
    {
        $media = $medium;
        $request->validate([
            'judul' => 'required|string|max:255',
            'tipe' => 'required|in:IMAGE,VIDEO',
            'kategori' => 'nullable|string|max:50',
            'deskripsi' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $data = $request->only(['judul', 'tipe', 'kategori', 'deskripsi']);
        $data['is_active'] = $request->has('is_active');

        if ($request->tipe === 'IMAGE') {
            if ($request->hasFile('file_image')) {
                $file = $request->file('file_image');
                $processedPath = $this->processAndStoreImage($file);

                if (!$processedPath) {
                    return back()->with('error', 'Gagal memproses gambar.')->withInput();
                }

                if (Storage::disk('public')->size($processedPath) > 1024 * 1024) {
                    Storage::disk('public')->delete($processedPath);
                    return back()->with('error', 'Gambar setelah dikompresi masih melebihi 1 MB.')->withInput();
                }

                // Delete old file
                if ($media->tipe === 'IMAGE' && $media->url) {
                    Storage::disk('public')->delete($media->url);
                }
                $data['url'] = $processedPath;
            } else {
                // Keep old URL if type was already IMAGE
                if ($media->tipe !== 'IMAGE') {
                    return back()->with('error', 'File gambar wajib diunggah jika tipe diubah ke IMAGE.')->withInput();
                }
                $data['url'] = $media->url;
            }
        } else {
            $request->validate([
                'url_video' => 'required|url',
            ]);
            $data['url'] = $request->url_video;

            // Delete old file if it was IMAGE
            if ($media->tipe === 'IMAGE' && $media->url) {
                Storage::disk('public')->delete($media->url);
            }
        }

        $media->update($data);

        return redirect()->route('admin.media.index')->with('success', 'Media berhasil diperbarui!');
    }

    public function destroy(Media $medium)
    {
        $media = $medium;
        if ($media->tipe === 'IMAGE' && $media->url) {
            Storage::disk('public')->delete($media->url);
        }
        $media->delete();

        return redirect()->route('admin.media.index')->with('success', 'Media berhasil dihapus!');
    }

    /**
     * Process and store image using GD.
     */
    private function processAndStoreImage($file)
    {
        $extension = $file->getClientOriginalExtension();
        $filename = Str::random(20) . '.jpg'; // Store as jpg for better compression
        $path = 'media/' . $filename;

        // Load image
        $image = null;
        if (in_array(strtolower($extension), ['jpg', 'jpeg'])) {
            $image = imagecreatefromjpeg($file->getRealPath());
        } elseif (strtolower($extension) === 'png') {
            $image = imagecreatefrompng($file->getRealPath());
        } elseif (strtolower($extension) === 'webp') {
            $image = imagecreatefromwebp($file->getRealPath());
        }

        if (!$image) return null;

        // Resize if too large (max 1600px width)
        $width = imagesx($image);
        $height = imagesy($image);
        $max_width = 1600;

        if ($width > $max_width) {
            $new_width = $max_width;
            $new_height = floor($height * ($max_width / $width));
            $tmp_img = imagecreatetruecolor($new_width, $new_height);
            
            // Handle transparency for PNG/WebP if needed, but we save as JPG
            imagecopyresampled($tmp_img, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
            imagedestroy($image);
            $image = $tmp_img;
        }

        // Capture output in a buffer
        ob_start();
        imagejpeg($image, null, 80); // 80 quality
        $image_data = ob_get_clean();
        imagedestroy($image);

        // Store
        Storage::disk('public')->put($path, $image_data);

        return $path;
    }
}
