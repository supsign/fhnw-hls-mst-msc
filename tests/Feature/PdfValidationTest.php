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

    public function test_ids_and_study_mode_must_be_integers(): void
    {
        $this->postJson('/api/pdf', [
            'given_name' => 'Anna',
            'surname' => 'Muster',
            'semester' => 'AS26',
            'specialization' => 'ACB',
            'study_mode' => 'full-time',
            'selected_courses' => [['courses' => [111], 'semesterId' => 1]],
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['semester', 'specialization', 'study_mode'])
            ->assertJsonMissingValidationErrors(['given_name', 'surname', 'selected_courses']);
    }
}
