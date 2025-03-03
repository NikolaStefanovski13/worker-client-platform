<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Dashboard route
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/test-view', function () {
    return view('test-view', ['message' => 'This is a test!']);
});

// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Service areas for workers
    Route::post('/profile/service-areas', [ProfileController::class, 'addServiceArea'])->name('profile.service-areas.store');
    Route::delete('/profile/service-areas/{serviceArea}', [ProfileController::class, 'removeServiceArea'])->name('profile.service-areas.destroy');
});

// Job routes
Route::middleware('auth')->group(function () {
    Route::get('/jobs', [JobController::class, 'index'])->name('jobs.index');
    Route::get('/jobs/create', [JobController::class, 'create'])->middleware('user.type:client')->name('jobs.create');
    Route::post('/jobs', [JobController::class, 'store'])->middleware('user.type:client')->name('jobs.store');
    Route::get('/jobs/{job}', [JobController::class, 'show'])->name('jobs.show');
    Route::get('/jobs/{job}/edit', [JobController::class, 'edit'])->middleware('user.type:client')->name('jobs.edit');
    Route::patch('/jobs/{job}', [JobController::class, 'update'])->middleware('user.type:client')->name('jobs.update');
    Route::patch('/jobs/{job}/complete', [JobController::class, 'complete'])->middleware('user.type:client')->name('jobs.complete');
});

// Job application routes
Route::middleware('auth')->group(function () {
    Route::post('/jobs/{job}/apply', [JobApplicationController::class, 'store'])->middleware('user.type:worker')->name('job-applications.store');
    Route::patch('/job-applications/{application}/accept', [JobApplicationController::class, 'accept'])->middleware('user.type:client')->name('job-applications.accept');
    Route::patch('/job-applications/{application}/reject', [JobApplicationController::class, 'reject'])->middleware('user.type:client')->name('job-applications.reject');
});

// Message routes
Route::middleware('auth')->group(function () {
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{job}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{job}', [MessageController::class, 'store'])->name('messages.store');
});

// Payment routes
Route::middleware('auth')->group(function () {
    Route::post('/payments/{job}', [PaymentController::class, 'store'])->middleware('user.type:client')->name('payments.store');
    Route::patch('/payments/{payment}/release', [PaymentController::class, 'release'])->middleware('user.type:client')->name('payments.release');
});

// Review routes
Route::middleware('auth')->group(function () {
    Route::post('/jobs/{job}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
});

// Search routes
Route::middleware('auth')->group(function () {
    Route::get('/search', [SearchController::class, 'index'])->name('search.index');
    Route::get('/search/workers', [SearchController::class, 'workers'])->name('search.workers');
});

require __DIR__ . '/auth.php';
