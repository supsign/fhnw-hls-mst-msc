<?php

namespace Tests\Feature;

use App\Models\Cluster;
use App\Models\Specialization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseDataTest extends TestCase
{
    use RefreshDatabase;
    protected Specialization $specialization;

    protected function setUp(): void
    {
        parent::setUp();

        $cluster = Cluster::create(['name' => 'Cluster A']);
        $this->specialization = Specialization::create([
            'cluster_id' => $cluster->id,
            'name' => 'Medical Informatics',
            'short_name' => 'MI',
        ]);
    }

    public function test_returns_course_data_for_existing_specialization(): void
    {
        $this->postJson("/api/coursedata/{$this->specialization->id}")
            ->assertOk()
            ->assertJsonStructure([
                'courses',
                'optional_courses',
                'semesters',
                'slots',
                'texts',
                'theses',
            ]);
    }

    public function test_unknown_specialization_returns_404(): void
    {
        $this->postJson('/api/coursedata/999999')->assertNotFound();
    }

    public function test_invalid_payload_returns_422(): void
    {
        $this->postJson("/api/coursedata/{$this->specialization->id}", [
            'semester' => 'kein-integer',
            'study_mode' => 'auch-keiner',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['semester', 'study_mode']);
    }
}
