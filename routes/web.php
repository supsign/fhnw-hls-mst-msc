<?php

use App\Http\Controllers\Admin\ConfigurationController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Admin\TestController;
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

Route::get('/', [HomeController::class, 'show'])->name('home');

Route::get('admin/config', [ConfigurationController::class, 'show'])->name('admin.config.show');
Route::post('admin/config', [ConfigurationController::class, 'post'])->name('admin.config.post');

Route::get('/test', [TestController::class, 'test'])->name('test');