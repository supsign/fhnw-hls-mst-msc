<?php

use App\Http\Controllers\Api\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('personaldata', [Controller::class, 'getPersonalData'])->name('get.personalData');
Route::get('coursedata/{specialization}', [Controller::class, 'getCourseData'])->name('get.courseData');
Route::get('thesisdata/{specialization}', [Controller::class, 'getThesisData'])->name('get.thesisData');