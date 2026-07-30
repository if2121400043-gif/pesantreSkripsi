<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\DokumenPsb;

class CleanupOrphanedDocuments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cleanup-orphaned-documents {--force : Force delete without confirmation} {--dry-run : Only list the files, do not delete}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find and delete orphaned PSB documents in storage that are not referenced in the database.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Scanning for orphaned PSB documents...');

        $localOrphans = [];
        $publicOrphans = [];

        // Scan local disk (private storage)
        if (Storage::disk('local')->exists('psb/dokumen')) {
            $localFiles = Storage::disk('local')->allFiles('psb/dokumen');
            $dbPaths = DokumenPsb::whereNotNull('file_path')->pluck('file_path')->toArray();
            
            // Normalize database paths (remove any public/ prefix) for local comparison
            $normalizedDbPaths = array_map(function ($path) {
                return str_starts_with($path, 'public/') ? substr($path, 7) : $path;
            }, $dbPaths);

            $localOrphans = array_diff($localFiles, $normalizedDbPaths);
        }

        // Scan public disk (public storage link)
        if (Storage::disk('public')->exists('psb/dokumen')) {
            $publicFiles = Storage::disk('public')->allFiles('psb/dokumen');
            $dbPaths = DokumenPsb::whereNotNull('file_path')->pluck('file_path')->toArray();
            
            // Normalize database paths for public comparison (files on public disk shouldn't have public/ prefix either)
            $normalizedDbPaths = array_map(function ($path) {
                return str_starts_with($path, 'public/') ? substr($path, 7) : $path;
            }, $dbPaths);

            $publicOrphans = array_diff($publicFiles, $normalizedDbPaths);
        }

        $localCount = count($localOrphans);
        $publicCount = count($publicOrphans);
        $totalCount = $localCount + $publicCount;

        if ($totalCount === 0) {
            $this->info('No orphaned documents found.');
            return 0;
        }

        $this->warn("Found {$totalCount} orphaned file(s) in storage ({$localCount} in private local storage, {$publicCount} in public storage).");

        if ($this->option('dry-run')) {
            foreach ($localOrphans as $file) {
                $this->line("[Private Local] Orphaned: {$file}");
            }
            foreach ($publicOrphans as $file) {
                $this->line("[Public] Orphaned: {$file}");
            }
            $this->info('Dry-run complete. No files were deleted.');
            return 0;
        }

        $force = $this->option('force');
        if ($force || $this->confirm("Do you want to delete these {$totalCount} orphaned files?")) {
            $deleted = 0;
            
            // Delete from local disk
            foreach ($localOrphans as $file) {
                if (Storage::disk('local')->delete($file)) {
                    $this->line("Deleted [Private Local]: {$file}");
                    $deleted++;
                } else {
                    $this->error("Failed to delete [Private Local]: {$file}");
                }
            }

            // Delete from public disk
            foreach ($publicOrphans as $file) {
                if (Storage::disk('public')->delete($file)) {
                    $this->line("Deleted [Public]: {$file}");
                    $deleted++;
                } else {
                    $this->error("Failed to delete [Public]: {$file}");
                }
            }

            $this->info("Successfully deleted {$deleted} of {$totalCount} orphaned files.");
        } else {
            $this->info('Cleanup cancelled.');
        }

        return 0;
    }
}
