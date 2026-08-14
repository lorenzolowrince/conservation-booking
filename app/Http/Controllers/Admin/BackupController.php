<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Backup;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Support\Facades\Artisan;

class BackupController extends Controller
{
    public function index()
    {
        $backups = Backup::latest()->paginate(15);

        $nextScheduled = now()->setTime(0, 0, 0);
        if ($nextScheduled->isPast()) {
            $nextScheduled->addDay();
        }

        return view('admin.backup.index', compact('backups', 'nextScheduled'));
    }

    public function create(): RedirectResponse
    {
        $exitCode = Artisan::call('backup:run', ['--type' => 'manual']);

        if ($exitCode === 0) {
            return redirect()->route('admin.backup.index')
                ->with('success', 'Backup created successfully.');
        }

        return redirect()->route('admin.backup.index')
            ->with('error', 'Backup failed. Please check the logs for details.');
    }

    public function download(Backup $backup): BinaryFileResponse|RedirectResponse
    {
        if (! $backup->fileExists()) {
            return redirect()->route('admin.backup.index')
                ->with('error', 'Backup file no longer exists on the server.');
        }

        $filePath = storage_path('app/' . $backup->path);

        return response()->download($filePath, $backup->filename, [
            'Content-Type'        => 'application/zip',
            'Content-Disposition' => 'attachment; filename="' . $backup->filename . '"',
        ]);
    }

    public function destroy(Backup $backup): RedirectResponse
    {
        $filePath = storage_path('app/' . $backup->path);

        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $backup->delete();

        return redirect()->route('admin.backup.index')
            ->with('success', 'Backup deleted successfully.');
    }
}
