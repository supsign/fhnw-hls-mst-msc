<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Services\ConfigurationImport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery\MockInterface;
use Tests\Support\ConfigurationWorkbook;
use Tests\Support\RemovesCreatedFiles;
use Tests\TestCase;

/**
 * Der Passwort-Check in PostConfiguration::authorize() ist die einzige
 * Zugriffsschranke der App. ADMIN_PASSWORD/PASSWORD_HASH_SALT sind in
 * phpunit.xml gepinnt; die Initial-Migration seedet daraus die app-Zeile.
 */
class AdminConfigurationTest extends TestCase
{
    use RefreshDatabase;
    use RemovesCreatedFiles;

    protected function postConfiguration(array $data): \Illuminate\Testing\TestResponse
    {
        return $this->post('/api/admin/configuration', $data, ['Accept' => 'application/json']);
    }

    public function test_wrong_password_is_forbidden_and_stores_nothing(): void
    {
        Storage::fake('local');

        $this->postConfiguration([
            'password' => 'falsches-passwort',
            'config_file' => UploadedFile::fake()->create('config.xlsx', 10),
        ])->assertForbidden();

        $this->assertSame(0, Course::count());
        Storage::disk('local')->assertMissing('config/config.xlsx');
    }

    public function test_missing_file_returns_422(): void
    {
        $this->postConfiguration(['password' => 'admin-test-password'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['config_file']);
    }

    public function test_non_xlsx_file_returns_422(): void
    {
        Storage::fake('local');

        $this->postConfiguration([
            'password' => 'admin-test-password',
            'config_file' => UploadedFile::fake()->create('config.txt', 10, 'text/plain'),
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['config_file']);
    }

    /**
     * Eine echte xlsx bis zur Uebergabe an den Import (der selbst nur gegen MySQL
     * laeuft, siehe ConfigurationImportTest): mimes:xlsx prueft den Dateiinhalt,
     * die Datei landet unter config/<Originalname> auf der Default-Disk.
     */
    public function test_xlsx_upload_is_stored_and_handed_to_the_import(): void
    {
        $this->removeFilesCreatedDuringTest('local');
        $path = tempnam(sys_get_temp_dir(), 'phpunit-xlsx-');
        $this->beforeApplicationDestroyed(fn () => unlink($path));
        ConfigurationWorkbook::realistic()->save($path);
        $this->mock(ConfigurationImport::class, function (MockInterface $mock): void {
            $mock->shouldReceive('__invoke')
                ->once()
                ->with('config/phpunit-konfiguration.xlsx')
                ->andReturn((object)['status' => 'success']);
        });

        $this->postConfiguration([
            'password' => 'admin-test-password',
            'config_file' => new UploadedFile($path, 'phpunit-konfiguration.xlsx', null, null, true),
        ])->assertRedirect(config('app.url').'/admin/config');

        $this->assertFileEquals($path, Storage::disk('local')->path('config/phpunit-konfiguration.xlsx'));
    }
}
