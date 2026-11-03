<?php
use App\Http\Controllers\SiteController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;
Route::get('/', [SiteController::class, 'home'])->name('home');
Route::get('/modeles', [SiteController::class, 'index'])->name('cars.index');
Route::get('/modeles/{car}', [SiteController::class, 'show'])->name('cars.show');
Route::get('/demande-essai', [SiteController::class, 'contact'])->name('contact');
Route::post('/demande-essai', [SiteController::class, 'store'])
    ->middleware('throttle:test-drive')
    ->name('contact.store');

// Pages du header (KpHeader)
Route::get('/showroom', [SiteController::class, 'showroom'])->name('showroom');
Route::get('/essai', [SiteController::class, 'contact'])->name('essai');
Route::get('/opportunities', [SiteController::class, 'opportunities'])->name('opportunities');
Route::permanentRedirect('/opportunites', '/opportunities');
Route::get('/privilege', [\App\Http\Controllers\PrivilegeController::class, 'show'])->name('privilege');
Route::post('/privilege/contact', [\App\Http\Controllers\PrivilegeController::class, 'store'])->middleware('throttle:5,1')->name('privilege.store');
Route::get('/sira', [\App\Http\Controllers\SiraController::class, 'show'])->name('sira');
Route::post('/sira/inscription', [\App\Http\Controllers\SiraController::class, 'store'])->middleware('throttle:5,1')->name('sira.store');
Route::permanentRedirect('/page/opportunities', '/opportunities');
Route::get('/sav', [\App\Http\Controllers\SavController::class, 'show'])->name('sav');
Route::post('/sav/ecrire', [\App\Http\Controllers\SavController::class, 'store'])->middleware('throttle:5,1')->name('sav.store');
Route::permanentRedirect('/service-apres-vente', '/sav');
Route::permanentRedirect('/page/sav', '/sav');
Route::get('/contact', [\App\Http\Controllers\ContactController::class, 'show'])->name('contact.page');
Route::post('/contact', [\App\Http\Controllers\ContactController::class, 'store'])->middleware('throttle:5,1')->name('contact.send');
Route::permanentRedirect('/page/contact', '/contact');
Route::view('/kpandji-automobiles', 'brand')->name('automobiles');
Route::permanentRedirect('/page/kpandji-automobiles', '/kpandji-automobiles');
Route::view('/ecologie', 'ecologie')->name('ecologie');
Route::permanentRedirect('/page/ecologie', '/ecologie');
Route::permanentRedirect('/eco-kpandji', '/ecologie');
Route::view('/emplois', 'emplois')->name('emplois');
Route::post('/emplois/candidature', [\App\Http\Controllers\EmploisController::class, 'store'])->middleware('throttle:5,1')->name('emplois.store');
Route::permanentRedirect('/page/emplois', '/emplois');
Route::permanentRedirect('/candidature', '/emplois');
Route::permanentRedirect('/kpandji-emplois', '/emplois');

// ===== Connexion des clients, managers et administrateurs =====
Route::get('/connexion', [\App\Http\Controllers\AccountAuthController::class, 'show'])->name('connexion');
Route::post('/connexion', [\App\Http\Controllers\AccountAuthController::class, 'login'])->middleware('throttle:10,1')->name('account.login');
Route::post('/deconnexion', [\App\Http\Controllers\AccountAuthController::class, 'logout'])->middleware('auth')->name('account.logout');

// ===== Anciennes adresses : back-office et espace manager désormais sous Filament (/backoffice, /manager) =====
Route::permanentRedirect('/admin', '/backoffice');
Route::permanentRedirect('/admin/{path}', '/backoffice')->where('path', '.*');
Route::permanentRedirect('/espace-manager/conversations/{conversation}', '/manager/conversations/{conversation}')->whereNumber('conversation');
Route::permanentRedirect('/espace-manager', '/manager');
Route::permanentRedirect('/espace-manager/{path}', '/manager')->where('path', '.*');

// ===== Espaces Manager & Client =====
use App\Http\Controllers\Client\ClientController as Cli;
use App\Http\Controllers\SpaceAccountController as SpaceAcc;
Route::get('/invitation/{token}', [SpaceAcc::class, 'show'])->middleware('signed')->name('invitation.show');
Route::post('/invitation/{token}', [SpaceAcc::class, 'accept'])->middleware(['signed', 'throttle:10,1'])->name('invitation.accept');
Route::middleware('auth')->group(function () {
    Route::get('/compte/en-attente', [SpaceAcc::class, 'waiting'])->name('space.waiting');
    Route::get('/mon-compte/mot-de-passe', [SpaceAcc::class, 'password'])->name('space.password');
    Route::put('/mon-compte/mot-de-passe', [SpaceAcc::class, 'updatePassword'])->middleware('throttle:10,1')->name('space.password.update');
});
Route::prefix('mon-espace')->name('client.')->middleware('role:client')->group(function () {
    Route::get('/', [Cli::class, 'dashboard'])->name('dashboard');
    Route::get('/gains', [Cli::class, 'gains'])->name('gains');
    Route::get('/gains.csv', [Cli::class, 'csv'])->name('csv');
    Route::get('/opportunites', [Cli::class, 'opportunities'])->name('opportunities');
    Route::get('/messages', [Cli::class, 'messages'])->name('messages');
    Route::post('/messages', [Cli::class, 'request'])->middleware('throttle:10,1')->name('request');
    Route::post('/messages/tout-lu', [Cli::class, 'readAll'])->name('messages.read');
    Route::get('/messages/{key}', [Cli::class, 'thread'])->where('key', '[a-z]+-[0-9]+')->name('thread');
    Route::post('/messages/{key}', [Cli::class, 'followUp'])->where('key', '[a-z]+-[0-9]+')->middleware('throttle:10,1')->name('thread.reply');
});
Route::bind('inv', fn($v) => \App\Models\ClientInvitation::findOrFail($v));

// ===== Pages créées dans l'admin (après toutes les routes existantes) =====
Route::get('/page/{slug}', [PageController::class, 'show'])->where('slug', '[a-z0-9-]+')->name('page.show');
Route::get('/{slug}', [PageController::class, 'show'])->where('slug', '[a-z0-9]+(?:-[a-z0-9]+)*')->name('page.catchall');
