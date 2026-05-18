<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\InputAspirationController;
use App\Http\Controllers\Admin\AspirationProgressController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\FeedbackController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Student\AuthController as StudentAuthController;
use App\Http\Controllers\Student\StudentDashboardController;
use App\Http\Controllers\Student\NotificationController;
use App\Http\Controllers\Student\AspirationInteractionController;
use App\Http\Controllers\Student\InputAspirationController as StudentAspirationController;
use App\Models\Student;

/*
|--------------------------------------------------------------------------
| Public & Guest Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('student.login');
});

// Auth Student
Route::get('/student/login', [StudentAuthController::class, 'showLogin'])->name('student.login');
Route::post('/student/login', [StudentAuthController::class, 'login'])->name('student.login.submit');

// Auth Admin & Ketua
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

Route::post('/student/forgot-password', [StudentAuthController::class, 'forgotPassword'])->name('student.forgot-password');


/*
|--------------------------------------------------------------------------
| Student Protected Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth:student')
    ->prefix('student')
    ->name('student.')
    ->group(function () {
        // Dashboard & Profile
        Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
        Route::get('/profile', [StudentDashboardController::class, 'profile'])->name('profile');
        Route::get('/students/{student}', [StudentDashboardController::class, 'showStudentProfile'])->name('students.show');
        Route::get('/global', [StudentDashboardController::class, 'global'])->name('global');
        Route::post('/logout', [StudentAuthController::class, 'logout'])->name('logout');

        // Aspirasi CRUD
        Route::resource('input-aspirations', StudentAspirationController::class);
        Route::post('input-aspirations/{id}/rate', [StudentAspirationController::class, 'rate'])->name('input-aspirations.rate');


        // Interactions (Vote & Comment)
        Route::post('/aspirations/{id}/vote', [AspirationInteractionController::class, 'toggleVote'])->name('aspirations.vote');
        Route::post('/aspirations/{id}/comments', [AspirationInteractionController::class, 'storeComment'])->name('aspirations.comments.store');

        // Notifications
        Route::post('/notifications/{id}/read', [NotificationController::class, 'read'])->name('notifications.read');
    });


/*
|--------------------------------------------------------------------------
| Admin & Ketua (Back-Office) Protected Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    
    // Unified Dashboard for Admin & Ketua
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Admin Specific Routes
    Route::middleware(['role:admin'])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            // Management Aspirasi
            Route::get('/input-aspirations', [InputAspirationController::class, 'index'])->name('aspirations.index');
            Route::get('/input-aspirations/{input}', [InputAspirationController::class, 'show'])->name('aspirations.show');
            Route::post('/aspirations/{input}/approve', [InputAspirationController::class, 'approve'])->name('aspirations.approve');
            Route::post('/aspirations/{input}/keep', [InputAspirationController::class, 'keep'])->name('aspirations.keep');
            Route::post('/aspirations/{input}/release-keep', [InputAspirationController::class, 'releaseKeep'])->name('aspirations.keep.release');
            Route::post('/input-aspirations/{input}/reject', [InputAspirationController::class, 'reject'])->name('aspirations.reject');
            Route::get('/aspirations/export', [InputAspirationController::class, 'export'])->name('aspirations.export');
            Route::get('/aspirations/export-pvedf', [InputAspirationController::class, 'exportPdf'])->name('aspirations.export.pdf');

            Route::post('/aspirations/{aspiration}/progress', [AspirationProgressController::class, 'update'])
                ->name('aspirations.progress.update')
                ->middleware('permission:update progress');



            // Resources
            Route::post('/aspirations/{aspiration}/feedback', [FeedbackController::class, 'store'])
                ->name('aspirations.feedback.store');

            Route::resource('students', StudentController::class);
            Route::resource('categories', CategoryController::class);
            Route::resource('locations', LocationController::class)->except('show');

            Route::get('/password-resets', [StudentController::class, 'passwordResets'])->name('password-resets.index');
            Route::post('/password-resets/{id}/reset', [StudentController::class, 'resetPassword'])->name('password-resets.reset');
        });
});