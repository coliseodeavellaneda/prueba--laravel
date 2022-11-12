<?php

use App\Http\Controllers\GoogleAnalyticsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HighchartController;
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

Route::get('/', [GoogleAnalyticsController::class, 'store']);
Route::get('/show', [GoogleAnalyticsController::class, 'index']);

