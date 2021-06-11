<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScrapingController;
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


Route::get('/scraping/moeda', [ScrapingController::class, 'getCotacaoMoeda'])->name('scraping');
Route::get('/car/versions', [ScrapingController::class, 'getCarVersions'])->name('scraping');
Route::get('/car/models', [ScrapingController::class, 'getCarModels'])->name('scraping');
Route::get('/car/categories', [ScrapingController::class, 'getCarCategories'])->name('scraping');
Route::get('/car/versions/model', [ScrapingController::class, 'saveModelVersions'])->name('scraping');
