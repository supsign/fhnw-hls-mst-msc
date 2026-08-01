<?php

namespace Tests\Feature;

use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Der Passwort-Check in PostConfiguration::authorize() ist die einzige
 * Zugriffsschranke der App. ADMIN_PASSWORD/PASSWORD_HASH_SALT sind in
 * phpunit.xml gepinnt; die Initial-Migration seedet daraus die app-Zeile.
 */
class AdminConfigurationTest extends TestCase
{
    use RefreshDatabase;

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
}
