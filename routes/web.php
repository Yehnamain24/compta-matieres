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

Route::get('/register', function () {
    return view('register');
})->name('inscription');

// Route pour l'Authentification
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::patch('/users/{user}/approve', [AdminController::class, 'approveUser'])->name('admin.users.approve');
Route::delete('/users/{user}/reject', [AdminController::class, 'rejectUser'])->name('admin.users.reject');

// Route pour un Utilisateur connecte en tamps que User
Route::prefix('/user')->middleware(['auth'])->group(function(){
    Route::get('/index', [DashboardController::class, 'index'])->name('user.dashboard');
    Route::get('/movements', [MovementsController::class, 'show'])->name('user.movements.show');
    Route::post('/movements', [MovementsController::class, 'store'])->name('user.movements.store');
    Route::delete('/movements/{id}', [MovementsController::class, 'destroy'])->name('user.movements.destroy');

    Route::get('/items', [ItemsController::class, 'show'])->name('user.items.show');
    Route::post('/items', [ItemsController::class, 'save'])->name('user.items.save');

    Route::get('/category', [CategoryController::class, 'show'])->name('user.category.show');
    Route::post('/category', [CategoryController::class, 'save'])->name('user.category.add');
    Route::put('/category/{id}', [CategoryController::class, 'update'])->name('user.category.update');
    Route::delete('/category/{id}', [CategoryController::class, 'delete'])->name('user.category.delete');

   
    Route::get('/status', [StatusController::class, 'show'])->name('user.status.show');
    Route::post('/status', [StatusController::class, 'store'])->name('user.status.store');
    Route::put('/status/{status}', [StatusController::class, 'update'])->name('user.status.update');
    Route::delete('/status/{status}', [StatusController::class, 'destroy'])->name('user.status.destroy');
    

    Route::get('/reports', [ReportController::class, 'show'])->name('user.reports.show');

    Route::get('/materiels/export', [ExportController::class, 'exportMateriels'])->middleware('role:admin,comptable_matieres')->name('materiels.export');
    Route::get('/materiels/export/pdf', [ExportController::class, 'exportMaterielsPDF'])->middleware('role:admin,comptable_matieres')->name('materiels.export.pdf');
});

// Route pour un Adminstrateur
Route::prefix('/admin')->middleware(['auth', 'role:admin'])->group(function(){
    Route::get('/index', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users.index');
    Route::patch('/users/{user}/role', [AdminController::class, 'updateRole'])->name('admin.users.update-role');
    Route::get('/settings', [AdminController::class, 'settings'])->name('admin.settings');
});


// Route pour la deconnection
Route::middleware('auth')->get('/logout', [AuthController::class, 'logout'])->name('logout');

   
