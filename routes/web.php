<?php

use App\Models\Course;
use App\Models\Specialization;
use App\Services\GetCourseData;
use App\Services\GetOverlappingCourses;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', fn () => view('config'))->name('config');

Route::get('/test', function () {
	$service = App::make(GetCourseData::class);


	dump(
		$service(Specialization::find(1))
	);




})->name('test');