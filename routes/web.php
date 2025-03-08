<?php

/**
 * Web Routes Configuration - HTTP Route Definitions
 *
 * PURPOSE:
 * This file defines all web routes for the application.
 * In Node.js, this would be similar to your Express router definitions
 * or Next.js/Nuxt.js routes.
 *
 * INTERACTIONS:
 * - Maps URLs to controller methods
 * - Defines route names for URL generation
 *
 * ARCHITECTURAL ROLE:
 * Routes in Laravel serve the same purpose as routes in Express.js:
 *
 * Laravel:                      Express.js:
 * -----------                   -----------
 * Route::get('/', [Controller]) app.get('/', controllerFunction)
 * Route::post('/save', [])      app.post('/save', middleware, controller)
 * ->name('route.name')          // Named routes don't exist natively in Express
 *
 * The ->name() method creates a named route that can be referenced
 * in views and controllers with route('name'), similar to how you might
 * use a constant or function in Express.js to generate URLs.
 */

use App\Http\Controllers\SpotifyController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Main dashboard - Shows liked tracks and sync history
Route::get('/', [SpotifyController::class, 'index'])->name('spotify.index');

// OAuth authorization redirect - Starts the Spotify authentication flow
Route::get('/auth', [SpotifyController::class, 'auth'])->name('spotify.auth');

// OAuth callback - Handles the redirect from Spotify after authentication
Route::get('/callback', [SpotifyController::class, 'callback'])->name('spotify.callback');

// Manual sync trigger - Allows users to start a sync manually
Route::get('/sync', [SpotifyController::class, 'sync'])->name('spotify.sync');
