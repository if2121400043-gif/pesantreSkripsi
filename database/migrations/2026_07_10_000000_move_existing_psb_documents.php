<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Storage;
use App\Models\DokumenPsb;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Fetch all documents from the database
        $documents = DokumenPsb::all();

        foreach ($documents as $doc) {
            // Check if the file path starts with 'public/' (legacy format stored on local disk)
            if (str_starts_with($doc->file_path, 'public/')) {
                // Check if file exists on the default local disk
                if (Storage::disk('local')->exists($doc->file_path)) {
                    // Get file content
                    $content = Storage::disk('local')->get($doc->file_path);

                    // Determine the new path on the public disk (strip 'public/' prefix)
                    $newPath = str_replace('public/', '', $doc->file_path);

                    // Write to public disk (Flysystem automatically handles directory creation)
                    Storage::disk('public')->put($newPath, $content);

                    // Update the path in the database
                    $doc->update([
                        'file_path' => $newPath
                    ]);

                    // Delete the old file from local disk
                    Storage::disk('local')->delete($doc->file_path);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // One-way migration. Rolling back is not needed as files should remain on the public disk.
    }
};
