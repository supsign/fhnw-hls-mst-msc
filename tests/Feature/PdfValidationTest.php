<?php

namespace Tests\Feature;

use Tests\TestCase;

class PdfValidationTest extends TestCase
{
    public function test_missing_fields_return_422(): void
    {
        $this->postJson('/api/pdf')
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'given_name',
                'surname',
                'semester',
                'specialization',
                'study_mode',
                'selected_courses',
            ]);
    }
}
