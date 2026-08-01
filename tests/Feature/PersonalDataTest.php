<?php

namespace Tests\Feature;

use App\Models\Cluster;
use App\Models\PageContent;
use App\Models\Specialization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PersonalDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_expected_json_structure(): void
    {
        $cluster = Cluster::create(['name' => 'Cluster A']);
        Specialization::create([
            'cluster_id' => $cluster->id,
            'name' => 'Medical Informatics',
            'short_name' => 'MI',
        ]);
        PageContent::create(['name' => 'intro_title', 'content' => 'Willkommen']);

        $this->getJson('/api/personaldata')
            ->assertOk()
            ->assertJsonStructure([
                'semesters',
                'studyMode' => ['studyModes'],
                'specializations',
                'texts',
            ])
            // GetUpcomingSemesters legt fehlende Semester selbst an
            ->assertJsonCount(8, 'semesters')
            ->assertJsonCount(1, 'specializations');
    }
}
