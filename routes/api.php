<?php

use App\Http\Controllers\Api\V1\PortfolioContentController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->name('api.v1.')
    ->middleware('throttle:60,1')
    ->group(function (): void {
        Route::get('portfolio', PortfolioContentController::class)
            ->name('portfolio.show');
    });
