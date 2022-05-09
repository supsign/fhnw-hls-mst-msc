<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DeletePdfs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:pdfs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'deletes old Pdf files form storage';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        foreach (Storage::files('public') AS $file) {
            if (!str_contains($file, '.pdf')) {
                continue;
            }

            if (now()->getTimestamp() - Storage::lastModified($file) < 24 * 60 * 60) {
                continue;
            }

            Storage::delete($file);
        }

        return 0;
    }
}
