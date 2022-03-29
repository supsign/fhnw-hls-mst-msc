<?php

use App\Http\Controllers\Admin\ConfigurationController;
use App\Http\Controllers\Controller;
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

Route::view('/', 'home')->name('home');

Route::get('admin/config', [ConfigurationController::class, 'show'])->name('admin.config.show');
Route::post('admin/config', [ConfigurationController::class, 'post'])->name('admin.config.post');

Route::get('test', [Controller::class, 'test'])->name('test');