<?php

namespace Tests\Support;

use Illuminate\Support\Facades\Storage;

/**
 * Fuer Tests, die bewusst ueber die echten Disks laufen (PDF-Ablage,
 * Konfigurations-Upload): entfernt am Testende alles, was waehrend des Tests
 * auf diesen Disks neu entstanden ist. Vorhandene Dateien bleiben unberuehrt.
 */
trait RemovesCreatedFiles
{
    protected function removeFilesCreatedDuringTest(string ...$disks): void
    {
        $existing = [];

        foreach ($disks as $disk) {
            $existing[$disk] = [
                'files' => Storage::disk($disk)->allFiles(),
                'directories' => Storage::disk($disk)->allDirectories(),
            ];
        }

        $this->beforeApplicationDestroyed(function () use ($existing): void {
            foreach ($existing as $disk => ['files' => $files, 'directories' => $directories]) {
                $storage = Storage::disk($disk);
                $storage->delete(array_values(array_diff($storage->allFiles(), $files)));

                foreach (array_diff($storage->allDirectories(), $directories) as $directory) {
                    $storage->deleteDirectory($directory);
                }
            }
        });
    }
}
