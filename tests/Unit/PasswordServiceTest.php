<?php

namespace Tests\Unit;

use App\Services\PasswordService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PasswordServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_hash_is_deterministic(): void
    {
        $this->assertSame(
            PasswordService::hash('geheim'),
            PasswordService::hash('geheim')
        );
    }

    public function test_hash_uses_salt_from_env(): void
    {
        $this->assertSame(
            hash('sha3-512', 'geheim'.env('PASSWORD_HASH_SALT')),
            PasswordService::hash('geheim')
        );
    }

    public function test_check_accepts_the_seeded_admin_password(): void
    {
        $this->assertTrue(PasswordService::check('admin-test-password'));
    }

    public function test_check_rejects_a_wrong_password(): void
    {
        $this->assertFalse(PasswordService::check('falsch'));
    }
}
