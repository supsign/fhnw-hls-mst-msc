<?php

namespace Tests\Feature;

use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Tests\Support\RemovesCreatedFiles;
use Tests\TestCase;

/**
 * Die PDFs enthalten Personendaten und liegen unter einer oeffentlichen URL;
 * delete:pdfs entfernt sie nach 24 Stunden.
 */
class DeletePdfsCommandTest extends TestCase
{
    use RemovesCreatedFiles;

    public function test_pdfs_older_than_24_hours_are_deleted(): void
    {
        $now = Carbon::parse('2026-06-15 10:00:00');
        $this->travelTo($now);
        Storage::fake('local');
        $this->removeFilesCreatedDuringTest('local');

        $ages = [
            'public/muster_anna_as26_2026-06-14.pdf' => 25 * 3600,
            'public/meier_peter_as26_2026-06-14.pdf' => 24 * 3600,
            'public/keller_sara_as26_2026-06-14.pdf' => 24 * 3600 - 60,
            'public/rossi_nina_as26_2026-06-15.pdf' => 3600,
            'public/.gitignore' => 48 * 3600,
            'public/notes.txt' => 48 * 3600,
            // Unterordner durchsucht der Befehl nicht
            'public/archive/old_plan_as25.pdf' => 48 * 3600,
        ];

        foreach ($ages as $file => $age) {
            Storage::disk('local')->put($file, 'content');
            touch(Storage::disk('local')->path($file), $now->getTimestamp() - $age);
        }

        $this->artisan('delete:pdfs')->assertExitCode(0);

        $remaining = Storage::disk('local')->allFiles('public');
        sort($remaining);

        $this->assertSame([
            'public/.gitignore',
            'public/archive/old_plan_as25.pdf',
            'public/keller_sara_as26_2026-06-14.pdf',
            'public/notes.txt',
            'public/rossi_nina_as26_2026-06-15.pdf',
        ], $remaining);
    }

    public function test_cleanup_is_scheduled_every_fifteen_minutes(): void
    {
        $events = array_values(array_filter(
            $this->app->make(Schedule::class)->events(),
            fn (Event $event): bool => str_contains((string)$event->command, 'delete:pdfs')
        ));

        $this->assertCount(1, $events);
        $this->assertSame('*/15 * * * *', $events[0]->expression);
    }
}
