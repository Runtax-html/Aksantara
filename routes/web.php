<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TtsController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\GamificationController;
use Illuminate\Http\Request;

// API Edge-TTS Audio Synthesizer (su-ID-JajangNeural)
Route::match(['get', 'post'], '/api/tts', [TtsController::class, 'synthesize'])->name('api.tts');

Route::get('/', function () {
    return Inertia::render('Landingpage', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
});

Route::get('/dashboard', function (Request $request) {
    $gamification = GamificationController::getGamificationState($request->user());
    return Inertia::render('Dashboard', [
        'gamification' => $gamification,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

// Gamifikasi & Alur Soal API
Route::middleware('auth')->prefix('api/gamification')->name('gamification.')->group(function () {
    Route::get('/state', [GamificationController::class, 'getState'])->name('state');
    Route::post('/complete-level', [GamificationController::class, 'completeLevel'])->name('complete-level');
    Route::post('/practice-writing', [GamificationController::class, 'practiceWriting'])->name('practice-writing');
    Route::post('/read-story', [GamificationController::class, 'readStory'])->name('read-story');
    Route::post('/unlock-story', [GamificationController::class, 'unlockStory'])->name('unlock-story');
    Route::post('/use-hint', [GamificationController::class, 'useHint'])->name('use-hint');
    Route::post('/claim-mission-bonus', [GamificationController::class, 'claimMissionBonus'])->name('claim-mission-bonus');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
