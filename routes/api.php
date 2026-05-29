<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BusinessController;
use App\Http\Controllers\Api\InvestmentController;
use App\Http\Controllers\Api\MidtransWebhookController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\OnboardingController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\PortfolioController;
use App\Http\Controllers\Api\ReferralController;
use App\Http\Controllers\Api\WalletController;
use Illuminate\Support\Facades\Route;

// ─── Public ───────────────────────────────────────────────────────────────────
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);
Route::post('/payments/midtrans/callback', [MidtransWebhookController::class, 'handle']);

// ─── Authenticated ────────────────────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout',       [AuthController::class, 'logout']);
    Route::get('/profile',       [AuthController::class, 'profile']);
    Route::put('/profile',       [AuthController::class, 'updateProfile']);

    // Onboarding (accessible before full verification)
    Route::post('/verification/upload',  [OnboardingController::class, 'uploadId']);
    Route::get('/verification/status',   [OnboardingController::class, 'status']);
    Route::post('/initial-deposit/pay',  [PaymentController::class, 'initialDeposit']);
    Route::post('/payments/manual',      [PaymentController::class, 'uploadProof']);

    // Businesses — public browsing
    Route::get('/businesses',      [BusinessController::class, 'index']);
    Route::get('/businesses/{id}', [BusinessController::class, 'show']);

    // Payments — accessible to any authenticated user
    Route::get('/payments/{transaction}/status', [PaymentController::class, 'checkStatus']);
    Route::get('/payments/history',              [PaymentController::class, 'history']);

    // Balance & history — accessible to any authenticated user
    Route::get('/balance',         [WalletController::class, 'index']);
    Route::get('/balance/history', [WalletController::class, 'history']);

    // Referral — any authenticated user can share their code
    Route::get('/referral/code',    [ReferralController::class, 'code']);
    Route::get('/referral/tree',    [ReferralController::class, 'tree']);
    Route::get('/referral/rewards', [ReferralController::class, 'rewards']);

    // Notifications — any authenticated user
    Route::get('/notifications',                [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read',     [NotificationController::class, 'markRead']);
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead']);

    // ─── Verified investors only ──────────────────────────────────────────────
    Route::middleware('verified.investor')->group(function () {

        // Investments
        Route::post('/businesses/{businessId}/invest', [InvestmentController::class, 'store']);
        Route::get('/investments',                     [InvestmentController::class, 'index']);
        Route::get('/investments/{id}',                [InvestmentController::class, 'show']);

        // Portfolio
        Route::get('/portfolio',      [PortfolioController::class, 'index']);
        Route::get('/portfolio/{id}', [PortfolioController::class, 'show']);

        // Installment payment
        Route::post('/payments/installment/{investment}', [PaymentController::class, 'payInstallment']);

        // Withdrawals
        Route::post('/withdrawal/request', [WalletController::class, 'withdraw']);
        Route::get('/withdrawal/history',  [WalletController::class, 'withdrawalHistory']);
    });
});
