<?php

namespace Tests\Feature;

use App\Enums\SemesterType;
use App\Enums\StudyMode;
use App\Models\Cluster;
use App\Models\Semester;
use App\Models\Specialization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThesisDataTest extends TestCase
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

    public function test_missing_fields_return_422(): void
    {
        $this->postJson("/api/thesisdata/{$this->specialization->id}")
            ->assertStatus(422)
            ->assertJsonValidationErrors(['double_degree', 'semester', 'study_mode']);
    }

    public function test_returns_thesis_data_for_valid_request(): void
    {
        $semester = Semester::create([
            'type' => SemesterType::AutumnStart->value,
            'start_date' => now()->addMonths(2)->startOfDay(),
        ]);

        $this->postJson("/api/thesisdata/{$this->specialization->id}", [
            'double_degree' => false,
            'semester' => $semester->id,
            'study_mode' => StudyMode::FullTime->value,
        ])
            ->assertOk()
            ->assertJsonStructure(['theses', 'time_frames', 'texts']);
    }
}
