<?php

use App\Http\Controllers\Api\V1\PortfolioContentController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->name('api.v1.')
    ->middleware('throttle:60,1')
    ->group(function (): void {
        Route::get('portfolio', PortfolioContentController::class)
            ->name('portfolio.bootstrap');

        Route::controller(PortfolioContentController::class)->group(function (): void {
            Route::get('global', 'global')->name('global.show');
            Route::get('landing-page', 'landingPage')->name('landing-page.show');
            Route::get('about-page', 'aboutPage')->name('about-page.show');
            Route::get('projects-page', 'projectsPage')->name('projects-page.show');
            Route::get('project-categories', 'projectCategories')->name('project-categories.index');
            Route::get('projects', 'projects')->name('projects.index');
            Route::get('projects/{slug}', 'project')->name('projects.show');
            Route::get('notes-page', 'notesPage')->name('notes-page.show');
            Route::get('notes-categories', 'notesCategories')->name('notes-categories.index');
            Route::get('notes', 'notes')->name('notes.index');
            Route::get('notes/{slug}', 'note')->name('notes.show');
            Route::get('contact-information', 'contactInformation')->name('contact-information.show');
            Route::get('media-assets', 'mediaAssets')->name('media-assets.index');
        });
    });
