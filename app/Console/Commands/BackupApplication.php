<?php

namespace App\Console\Commands;

use App\Models\Backup;
use Illuminate\Console\Command;
use ZipArchive;

class BackupApplication extends Command
{
    protected $signature = 'backup:run {--type=scheduled : The backup type (manual or scheduled)}';
    protected $description = 'Create a full backup of the application files and database';

    // Directories/files to exclude from the application backup
    private array $excludeDirs = ['vendor', 'node_modules', '.git', 'storage/app/backups'];

    public function handle(): int
    {
        $type = $this->option('type');
        $this->info("Starting {$type} backup...");

        $backupDir = storage_path('app/backups');
        if (! is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename  = "backup_{$type}_{$timestamp}.zip";
        $fullPath  = $backupDir . DIRECTORY_SEPARATOR . $filename;
        $relativePath = 'backups/' . $filename;

        // Record start in DB
        $backup = Backup::create([
            'filename' => $filename,
            'path'     => $relativePath,
            'size'     => 0,
            'type'     => $type,
            'status'   => 'pending',
        ]);

        try {
            $zip = new ZipArchive();
            if ($zip->open($fullPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                throw new \RuntimeException("Cannot create ZIP file at {$fullPath}");
            }

            // 1. Add the SQLite database file
            $this->info('Adding database...');
            $dbPath = database_path('database.sqlite');
            if (file_exists($dbPath)) {
                $zip->addFile($dbPath, 'database/database.sqlite');
            }

            // 2. Add application files
            $this->info('Adding application files...');
            $basePath = base_path();
            $this->addDirectoryToZip($zip, $basePath, $basePath);

            $zip->close();

            $size = file_exists($fullPath) ? filesize($fullPath) : 0;

            $backup->update([
                'size'   => $size,
                'status' => 'completed',
            ]);

            $this->info("Backup completed: {$filename} (" . $this->formatBytes($size) . ")");

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $backup->update([
                'status' => 'failed',
                'notes'  => $e->getMessage(),
            ]);

            $this->error("Backup failed: " . $e->getMessage());

            return self::FAILURE;
        }
    }

    private function addDirectoryToZip(ZipArchive $zip, string $dir, string $basePath): void
    {
        $items = scandir($dir);
        if ($items === false) {
            return;
        }

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $fullItemPath = $dir . DIRECTORY_SEPARATOR . $item;
            $relativePath = ltrim(str_replace($basePath, '', $fullItemPath), DIRECTORY_SEPARATOR . '/');

            // Skip excluded directories
            if ($this->shouldExclude($relativePath)) {
                continue;
            }

            if (is_dir($fullItemPath)) {
                $zip->addEmptyDir($relativePath);
                $this->addDirectoryToZip($zip, $fullItemPath, $basePath);
            } elseif (is_file($fullItemPath)) {
                $zip->addFile($fullItemPath, $relativePath);
            }
        }
    }

    private function shouldExclude(string $relativePath): bool
    {
        $relativePath = str_replace('\\', '/', $relativePath);

        foreach ($this->excludeDirs as $excluded) {
            $excluded = str_replace('\\', '/', $excluded);
            if ($relativePath === $excluded || str_starts_with($relativePath, $excluded . '/')) {
                return true;
            }
        }

        return false;
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        }
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }
}
