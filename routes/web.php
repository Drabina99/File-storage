<?php

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

Route::get('/disk/{path}/download_link', 'App\Http\Controllers\FileController@link_download')->name("disk.link_download");
Route::get('/disk/{disk}/download', 'App\Http\Controllers\FileController@download')->name("disk.download")->middleware('auth');
Route::get('/disk/{disk}/link', 'App\Http\Controllers\FileController@link')->middleware('auth')->name("disk.link");
Route::get('/disk/upload', 'App\Http\Controllers\FileController@upload')->middleware('auth')->name("disk.upload");
Route::get('/disk/shared', 'App\Http\Controllers\FileController@shared')->middleware('auth')->name("disk.shared");
Route::get('/disk/{disk}/delete','App\Http\Controllers\FileController@delete')->middleware('auth')->name('disk.delete');
Route::get('/disk/{disk}/soft_delete','App\Http\Controllers\FileController@soft_delete')->middleware('auth')->name('disk.soft_delete');
Route::get('/disk/notifications', 'App\Http\Controllers\FileController@notifications')->middleware('auth')->name("disk.notifications");

Route::get('/disk/{disk}/share', 'App\Http\Controllers\FileController@share')->middleware('auth')->name("disk.share");


Route::post('/disk/{disk}/share', 'App\Http\Controllers\FileController@share_my_file')->middleware('auth')->name("disk.share");

Route::resource('/disk', App\Http\Controllers\FileController::class)->middleware('auth');
Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::get('/logout', function () {
    Auth::guard()->logout();
    return view('welcome');
});

require __DIR__.'/auth.php';
