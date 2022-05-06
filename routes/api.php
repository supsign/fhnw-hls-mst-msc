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

Route::get('personaldataform', [Controller::class, 'getPersonalDataForm'])->name('get.form.personalData');
Route::get('coursedataform/{specialization}', [Controller::class, 'getCourseDataForm'])->name('get.form.courseData');
Route::get('thesisdataform/{specialization}', [Controller::class, 'getThesisDataForm'])->name('get.form.thesisData');