<?php

namespace App\Http\Controllers;

use App\Models\Semester;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function test(): int
    {
        // dump('test');

        $semesters = Semester::orderBy('start_date')->get();    //whereYear('start_date', '>', 2023)->get();

        foreach ($semesters AS $semester) {
            dump(
                $semester->name,
                $semester->is_replanning,
            );
            echo '<hr/>';
        }

        return 1;
    }
}