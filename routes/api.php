<?php

use Liberu\EcommerceWarrantyAndClaimsApi\Http\Controllers\WarrantyClaimController;
use Liberu\EcommerceWarrantyAndClaimsApi\Http\Controllers\WarrantyClaimOperationsController;
use Liberu\EcommerceWarrantyAndClaimsApi\Http\Controllers\WarrantyRegistrationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'throttle:api'])->prefix('api/v1/ecommerce/warranty-and-claims')->group(function (): void {
    Route::get('/', [WarrantyClaimController::class, 'index'])->name('ecommerce.warranty-and-claims.index');
    Route::post('/', [WarrantyClaimController::class, 'store'])->name('ecommerce.warranty-and-claims.store');
    Route::get('/registrations', [WarrantyRegistrationController::class, 'index'])->name('ecommerce.warranty-and-claims.registrations.index');
    Route::post('/registrations', [WarrantyRegistrationController::class, 'store'])->name('ecommerce.warranty-and-claims.registrations.store');
    Route::get('/{claim}', [WarrantyClaimController::class, 'show'])->name('ecommerce.warranty-and-claims.show');
    Route::patch('/{claim}', [WarrantyClaimController::class, 'update'])->name('ecommerce.warranty-and-claims.update');
    Route::post('/{claim}/scans', [WarrantyClaimOperationsController::class, 'scans'])->name('ecommerce.warranty-and-claims.scans');
    Route::post('/{claim}/labels', [WarrantyClaimOperationsController::class, 'labels'])->name('ecommerce.warranty-and-claims.labels');
    Route::post('/{claim}/manifests', [WarrantyClaimOperationsController::class, 'manifests'])->name('ecommerce.warranty-and-claims.manifests');
    Route::post('/{claim}/exceptions', [WarrantyClaimOperationsController::class, 'exceptions'])->name('ecommerce.warranty-and-claims.exceptions');
});
