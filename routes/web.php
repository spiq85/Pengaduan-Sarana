<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\InputAspirationController;
use App\Http\Controllers\Admin\AspirationProgressController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\FeedbackController;
use App\Http\Controllers\Ketua\ApprovalController;
use App\Http\Controllers\Ketua\ReportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Student\AuthController as StudentAuthController;
use App\Http\Controllers\Student\StudentDashboardController;
use App\Http\Controllers\Student\NotificationController;
use App\Http\Controllers\Student\AspirationInteractionController;
use App\Http\Controllers\Student\InputAspirationController as StudentAspirationController;

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
            Route::post('/aspirations/{input}/send-to-ketua', [InputAspirationController::class, 'sendToKetua'])->name('aspirations.send');
            Route::get('/aspirations/export', [InputAspirationController::class, 'export'])->name('aspirations.export');
            Route::get('/aspirations/export-pdf', [InputAspirationController::class, 'exportPdf'])->name('aspirations.export.pdf');

            // Progress Update
            Route::patch('/aspirations/{aspiration}/progress', [AspirationProgressController::class, 'update'])
                ->name('aspirations.progress.update')
                ->middleware('permission:update progress');

            // Resources
            Route::post('/aspirations/{aspiration}/feedback', [FeedbackController::class, 'store'])
                ->name('aspirations.feedback.store');

            Route::resource('students', StudentController::class);
            Route::resource('categories', CategoryController::class);
        });

    // Ketua Yayasan Specific Routes
    Route::middleware(['role:ketua_yayasan'])
        ->prefix('ketua')
        ->name('ketua.')
        ->group(function () {
            Route::get('/aspirations', [ApprovalController::class, 'index'])->name('aspirations.index');
            Route::get('/aspirations/{input}', [ApprovalController::class, 'show'])->name('aspirations.show');
            Route::post('/aspirations/{input}/approve', [ApprovalController::class, 'approve'])->name('aspirations.approve');
            Route::post('/aspirations/{input}/reject', [ApprovalController::class, 'reject'])->name('aspirations.reject');
            Route::post('/aspirations/{aspiration}/feedback', [FeedbackController::class, 'store'])
                ->name('aspirations.feedback.store');

            // Reports
            Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
            Route::get('/reports/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.export-pdf');
        });
});