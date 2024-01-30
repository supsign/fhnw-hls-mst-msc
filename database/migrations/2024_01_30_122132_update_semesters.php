<?php

use App\Enums\SemesterType;
use App\Models\Semester;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        $semesters = Semester::whereYear('start_date', '>', 2023)->get();

        foreach ($semesters AS $semester) {
            $semester->start_date = $semester->type === SemesterType::AutumnStart
                ? $semester->start_date->year.'-10-01'
                : $semester->start_date->year.'-03-01';

            $semester->save();
        }
    }
};
