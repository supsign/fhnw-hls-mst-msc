<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        // Views nutzen @vite; ohne diesen Fake braeuchte jeder Test, der eine
        // View rendert, ein gebautes public/build/manifest.json.
        $this->withoutVite();
    }
}
