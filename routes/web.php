<?php 
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PeopleController;
use App\Http\Controllers\SetupController;

Route::get('/', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/people', [PeopleController::class, 'index'])->name('people.index');

Route::resource('people', PeopleController::class);

Route::get('/setup', [SetupController::class, 'createTestUser'])->name('setup');

