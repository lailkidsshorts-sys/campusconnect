<?php

use App\Http\Controllers\Admin\AnalyticsController as AdminAnalyticsController;
use App\Http\Controllers\Admin\ComplaintController as AdminComplaintController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\AiController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Student\ComplaintController as StudentComplaintController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - CampusConnect
|--------------------------------------------------------------------------
*/

// Root Redirect
Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();
        return $user->isStudent()
            ? redirect()->route('student.dashboard')
            : redirect()->route('admin.dashboard');
    }
    return redirect()->route('login');
});

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');

    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::get('/signup', [RegisterController::class, 'showRegistrationForm'])->name('signup');
    Route::post('/register', [RegisterController::class, 'register']);
    Route::post('/signup', [RegisterController::class, 'register'])->name('signup.submit');

    Route::get('/forgot-password', function () {
        return redirect()->route('login')->with('info', 'Please contact the Campus Administration desk to reset your credentials.');
    })->name('password.forgot');
});

// Authenticated Logout
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

// Real-Time Notification System Endpoints
Route::middleware('auth')->prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/unread', [NotificationController::class, 'unread'])->name('unread');
    Route::post('/mark-read', [NotificationController::class, 'markAllRead'])->name('mark_read');
});

// AI Smart Complaint Assistant (Gemini AI Integration)
Route::middleware('auth')->post('/ai/analyze-complaint', [AiController::class, 'analyze'])->name('ai.analyze');
Route::middleware(['auth', 'role:dept_admin,super_admin'])->post('/ai/generate-reply', [AiController::class, 'generateReply'])->name('ai.reply');

// Student Portal Routes (Server-side Role Check)
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');

    Route::get('/complaints', [StudentComplaintController::class, 'index'])->name('complaints.index');
    Route::get('/complaints/create', [StudentComplaintController::class, 'create'])->name('complaints.create');
    Route::post('/complaints', [StudentComplaintController::class, 'store'])->name('complaints.store');
    Route::get('/complaints/{complaint}', [StudentComplaintController::class, 'show'])->name('complaints.show');

    // Dynamic AJAX endpoint for category -> issue type dependent dropdown
    Route::get('/categories/{category}/issue-types', [StudentComplaintController::class, 'getIssueTypes'])->name('categories.issue_types');

    // Student Resolution Feedback
    Route::post('/complaints/{complaint}/feedback', [FeedbackController::class, 'store'])->name('complaints.feedback');
});

// Admin Portal Routes (Dept Admin & Super Admin)
Route::middleware(['auth', 'role:dept_admin,super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/complaints', [AdminComplaintController::class, 'index'])->name('complaints.index');
    Route::get('/complaints/export-csv', [AdminComplaintController::class, 'exportCsv'])->name('complaints.export');
    Route::get('/complaints/{complaint}', [AdminComplaintController::class, 'show'])->name('complaints.show');
    Route::post('/complaints/{complaint}/status', [AdminComplaintController::class, 'updateStatus'])->name('complaints.update_status');

    Route::get('/analytics', [AdminAnalyticsController::class, 'index'])->name('analytics');
});
