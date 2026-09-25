<?php

use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\Public\PublicController;
use App\Http\Controllers\User\Category\CategoryController;
use App\Http\Controllers\User\Items\ItemsController;
use App\Http\Controllers\User\Movements\MovementsController;
use App\Http\Controllers\User\Status\StatusController;
use App\Http\Controllers\User\Report\ReportController;
use App\Http\Controllers\ExportController;

Route::get('/', [PublicController::class, 'home'])->name('home');

Route::get('/login', function () {
    return view('login');
})->name('connexion');


// Route pour l'Authentification
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::get('/register', fn() => redirect()->route('connexion'))->name('inscription');
Route::get('/register', fn() => view('register'))->name('inscription');
Route::post('/register', [AuthController::class, 'register'])->name('register');

// Routes d'approbation (Admin)
Route::patch('/users/{user}/approve', [AdminController::class, 'approveUser'])->name('admin.users.approve');
Route::delete('/users/{user}/reject', [AdminController::class, 'rejectUser'])->name('admin.users.reject');

// Route pour un Utilisateur connecté (qui est forcément Admin maintenant)
Route::prefix('/user')->middleware(['auth'])->group(function () {

    Route::get('/index', [DashboardController::class, 'index'])->name('user.dashboard');

    // Mouvements
    Route::get('/movements', [MovementsController::class, 'show'])->name('user.movements.show');
    Route::post('/movements', [MovementsController::class, 'store'])->name('user.movements.store');
    Route::delete('/movements/{id}', [MovementsController::class, 'destroy'])->name('user.movements.destroy');
    Route::put('/movements/{id}', [MovementsController::class, 'update'])->name('user.movements.update');

    // Items / Matériels
    Route::get('/items', [ItemsController::class, 'show'])->name('user.items.show');
    Route::post('/items', [ItemsController::class, 'save'])->name('user.items.save');
    Route::put('/items/{id}', [ItemsController::class, 'update'])->name('items.update');
    Route::delete('/items/{id}', [ItemsController::class, 'destroy'])->name('items.destroy');

    // Catégories
    Route::get('/category', [CategoryController::class, 'show'])->name('user.category.show');
    Route::post('/category', [CategoryController::class, 'save'])->name('user.category.add');
    Route::put('/category/{id}', [CategoryController::class, 'update'])->name('user.category.update');
    Route::delete('/category/{id}', [CategoryController::class, 'delete'])->name('user.category.delete');

    // Statuts
    Route::get('/status', [StatusController::class, 'show'])->name('user.status.show');
    Route::post('/status', [StatusController::class, 'store'])->name('user.status.store');
    Route::put('/status/{status}', [StatusController::class, 'update'])->name('user.status.update');
    Route::delete('/status/{status}', [StatusController::class, 'destroy'])->name('user.status.destroy');

    // Rapports
    Route::get('/reports', [ReportController::class, 'show'])->name('user.reports.show');

    // Export Excel (mouvements)
    Route::get('/materiels/export', [ExportController::class, 'exportMateriels'])->name('materiels.export');
});

// Route pour un Administrateur
Route::prefix('/admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/index', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users.index');
    Route::post('/users', [AdminController::class, 'store'])->name('admin.users.store');
    Route::get('/settings', [AdminController::class, 'settings'])->name('admin.settings');
    Route::get('/codes', [AdminController::class, 'codes'])->name('admin.codes');
    Route::post('/codes/generate', [AdminController::class, 'generateCode'])->name('admin.codes.generate');
});

// Route pour la déconnexion
Route::middleware('auth')->get('/logout', [AuthController::class, 'logout'])->name('logout');