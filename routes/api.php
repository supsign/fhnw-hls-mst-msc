<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\Controller;
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

Route::controller(Controller::class)->group(function () {
    Route::get('personaldata', 'getPersonalData')->name('get.personalData');
    Route::post('coursedata/{specialization}', 'postCourseData')->name('post.courseData');
    Route::post('thesisdata/{specialization}', 'postThesisData')->name('post.thesisData');
});

Route::controller(AdminController::class)->group(function () {
    Route::post('admin/configuration', 'postConfiguration')->name('admin.post.configuration');
});




