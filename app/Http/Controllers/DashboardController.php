<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SearchController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

// Authentication routes (Laravel's default)
Auth::routes();

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Profile routes
Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

// Job routes
Route::resource('jobs', JobController::class);
Route::put('/jobs/{job}/status', [JobController::class, 'updateStatus'])->name('jobs.update-status');

// Job application routes
Route::post('/jobs/{job}/apply', [JobApplicationController::class, 'store'])->name('job-applications.store');
Route::put('/job-applications/{jobApplication}/status', [JobApplicationController::class, 'updateStatus'])->name('job-applications.update-status');

// Message routes
Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
Route::get('/messages/{job}', [MessageController::class, 'show'])->name('messages.show');
Route::post('/messages/{job}', [MessageController::class, 'store'])->name('messages.store');

// Payment routes
Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
Route::post('/payments/{job}', [PaymentController::class, 'store'])->name('payments.store');
Route::put('/payments/{payment}/release', [PaymentController::class, 'release'])->name('payments.release');

// Review routes
Route::post('/reviews/{job}', [ReviewController::class, 'store'])->name('reviews.store');

// Search routes
Route::get('/search', [SearchController::class, 'index'])->name('search.index');
