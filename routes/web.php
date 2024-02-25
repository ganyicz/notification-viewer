<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Livewire\Volt\Volt;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::redirect('/', '/notifications');

Volt::route('/notifications', 'notifications')->name('notifications.index');

Route::get('/notifications/{notification}', function ($notification) {
    return File::get(storage_path('notifications/' . $notification . '.html'));
})->name('notifications.view');

// Added to avoid route not found errors in auth notifications
Route::get('/reset-password')->name('password.reset');
