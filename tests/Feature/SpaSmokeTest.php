<?php

namespace Tests\Feature;

use Tests\TestCase;

class SpaSmokeTest extends TestCase
{
    public function test_root_renders_spa_shell(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertViewIs('app');
    }

    public function test_deep_link_renders_spa_shell(): void
    {
        $this->get('/admin/config')
            ->assertOk()
            ->assertViewIs('app');
    }

    public function test_home_route_is_named(): void
    {
        $this->get(route('home'))->assertOk();
    }
}
