<?php

use App\Models\Semester;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        $semesters = Semester::whereMonth('start_date', 9)->get();

        foreach ($semesters AS $semester) {
            $semester->start_date = $semester->start_date->addMonth();
            $semester->save();
        }

        $semesters = Semester::whereMonth('start_date', 2)->get();

        foreach ($semesters AS $semester) {
            $semester->start_date = $semester->start_date->addMonth();
            $semester->save();
        }
    }
};
