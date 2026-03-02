<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\EndorsementController;
use App\Http\Controllers\HoaxClaimController;
use App\Http\Controllers\LabRoomController;
use App\Http\Controllers\ModerationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PolicyBriefController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\VoteController;
use Illuminate\Support\Facades\Route;

// Authentication Routes (guest only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login-anonim', [AuthController::class, 'loginAnonim'])->name('login.anonim');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Authenticated routes
Route::middleware(['auth', 'profile.complete'])->group(function () {
    // Main Pages
    Route::get('/', [PageController::class, 'home'])->name('home');
    Route::get('/profile', [PageController::class, 'profile'])->name('profile');

    // Hoax Buster
    Route::get('/hoax-buster', [HoaxClaimController::class, 'index'])->name('hoax-buster');
    Route::post('/hoax-buster', [HoaxClaimController::class, 'store'])->name('hoax-buster.store');
    Route::get('/hoax-buster/{hoaxClaim}', [HoaxClaimController::class, 'show'])->name('hoax-buster.show');
    Route::post('/hoax-buster/{hoaxClaim}/verdict', [HoaxClaimController::class, 'submitVerdict'])->name('hoax-buster.verdict');

    // L.A.B Room
    Route::get('/lab-room', [LabRoomController::class, 'index'])->name('lab-room.index');
    Route::post('/lab-room', [LabRoomController::class, 'store'])->name('lab-room.store');
    Route::get('/lab-room/{labRoom}', [LabRoomController::class, 'show'])->name('lab-room.show');
    Route::post('/lab-room/{labRoom}/join', [LabRoomController::class, 'join'])->name('lab-room.join');
    Route::post('/lab-room/{labRoom}/leave', [LabRoomController::class, 'leave'])->name('lab-room.leave');
    Route::post('/lab-room/{labRoom}/advance-phase', [LabRoomController::class, 'advancePhase'])->name('lab-room.advance');
    Route::post('/lab-room/{labRoom}/sources', [LabRoomController::class, 'addSource'])->name('lab-room.sources');
    Route::post('/lab-room/{labRoom}/discussions', [LabRoomController::class, 'addDiscussion'])->name('lab-room.discussions');
    Route::post('/lab-room/{labRoom}/brief', [LabRoomController::class, 'submitBrief'])->name('lab-room.brief');

    // Policy Lab
    Route::get('/policy-lab', [PolicyBriefController::class, 'index'])->name('policy-lab.index');
    Route::get('/policy-lab/create', [PolicyBriefController::class, 'create'])->name('policy-lab.create');
    Route::post('/policy-lab', [PolicyBriefController::class, 'store'])->name('policy-lab.store');
    Route::get('/policy-lab/{policyBrief}', [PolicyBriefController::class, 'show'])->name('policy-lab.show');
    Route::get('/policy-lab/{policyBrief}/edit', [PolicyBriefController::class, 'edit'])->name('policy-lab.edit');
    Route::put('/policy-lab/{policyBrief}', [PolicyBriefController::class, 'update'])->name('policy-lab.update');
    Route::post('/policy-lab/{policyBrief}/endorse', [PolicyBriefController::class, 'endorse'])->name('policy-lab.endorse');

    // Posting
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

    // Voting (AJAX)
    Route::post('/posts/{post}/vote', [VoteController::class, 'store'])->name('posts.vote');

    // Endorsement (AJAX) — artikel posts only
    Route::post('/posts/{post}/endorse', [EndorsementController::class, 'toggle'])->name('posts.endorse');

    // Comments (AJAX)
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('posts.comments.store');
    Route::delete('/posts/{post}/comments/{comment}', [CommentController::class, 'destroy'])->name('posts.comments.destroy');
    Route::post('/posts/{post}/comments/{comment}/top', [CommentController::class, 'toggleTop'])->name('posts.comments.top');

    // Reporting posts (AJAX)
    Route::post('/posts/{post}/report', [ReportController::class, 'store'])->name('posts.report');

    // Notifications (AJAX)
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.readAll');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');

    // Reports (legacy modal)
    Route::post('/report', [PageController::class, 'submitReport'])->name('report.submit');
});

// Moderation (agents only)
Route::middleware(['auth', 'profile.complete', 'agent'])->group(function () {
    Route::get('/moderation', [ModerationController::class, 'index'])->name('moderation.index');
    Route::post('/moderation/{post}/approve', [ModerationController::class, 'approve'])->name('moderation.approve');
    Route::post('/moderation/{post}/reject', [ModerationController::class, 'reject'])->name('moderation.reject');
    Route::post('/moderation/briefs/{policyBrief}/approve', [ModerationController::class, 'approveBrief'])->name('moderation.brief.approve');
    Route::post('/moderation/briefs/{policyBrief}/reject', [ModerationController::class, 'rejectBrief'])->name('moderation.brief.reject');

    // Hoax moderation
    Route::post('/moderation/claims/{hoaxClaim}/approve', [ModerationController::class, 'approveClaim'])->name('moderation.claim.approve');
    Route::post('/moderation/claims/{hoaxClaim}/reject', [ModerationController::class, 'rejectClaim'])->name('moderation.claim.reject');
    Route::post('/moderation/verdicts/{hoaxVerdict}/approve', [ModerationController::class, 'approveVerdict'])->name('moderation.verdict.approve');
    Route::post('/moderation/verdicts/{hoaxVerdict}/reject', [ModerationController::class, 'rejectVerdict'])->name('moderation.verdict.reject');
});

// Profile edit (auth but no profile.complete check — so new users can access)
Route::middleware('auth')->group(function () {
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});
