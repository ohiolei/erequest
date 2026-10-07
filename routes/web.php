<?php


use App\Http\Controllers\ChatController;
use App\Http\Controllers\Core\ACLController;
use App\Http\Controllers\Core\UserManager\UserManagerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::group(['middleware' => ['auth', 'verified']], function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::group(['prefix' => 'profile'], function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    Route::group(['prefix' => 'core', 'as' => 'core.'], function () {


        Route::group(['prefix' => 'acl', 'as' => 'acl.'], function () {
            Route::middleware(['auth', 'can:manage roles'])
                ->group(function () {
                    Route::get('/roles', [ACLController::class, 'index'])->name('roles.index');
                    Route::post('/roles', [ACLController::class, 'store'])->name('roles.store');
                    Route::patch('/roles/{role}', [ACLController::class, 'update'])->name('roles.update');
                    Route::delete('/roles/{role}', [ACLController::class, 'destroy'])->name('roles.destroy');
                    Route::post('/permissions', [ACLController::class, 'storePermission'])->name('permissions.store');
                    Route::patch('/permissions/{permission}', [ACLController::class, 'updatePermission'])->name('permissions.update');
                    Route::delete('/permissions/{permission}', [ACLController::class, 'destroyPermission'])->name('permissions.destroy');
                });
        });

        Route::group(['prefix' => 'user_manager', 'as' => 'user_manager.'], function () {
            Route::middleware(['auth', 'can:manage users'])
                ->group(function () {
                    Route::get('/users', [UserManagerController::class, 'index'])->name('users.index');
                    Route::post('/users', [UserManagerController::class, 'store'])->name('users.store');
                    Route::patch('/users/{user}', [UserManagerController::class, 'update'])->name('users.update');
                    Route::patch('/users/{user}/roles', [UserManagerController::class, 'updateRoles'])->name('users.roles.update');
                    Route::patch('/users/{user}/permissions', [UserManagerController::class, 'updatePermissions'])->name('users.permissions.update');
                    Route::delete('/users/{user}', [UserManagerController::class, 'destroy'])->name('users.destroy');
                    Route::post('/users/{user}/reset-password', [UserManagerController::class, 'resetPassword'])->name('users.reset-password');
                    Route::post('/users/{user}/reset-2fa', [UserManagerController::class, 'resetTwoFactor'])->name('users.reset-2fa');
                });
        });
    });



});

Route::middleware('auth')->prefix('chats')->name('chats.')->group(function () {
    Route::get('/', [ChatController::class, 'index'])->name('index');
    Route::get('/create', [ChatController::class, 'create'])->name('create');
    Route::post('/', [ChatController::class, 'store'])->name('store');
    Route::get('/{chat}', [ChatController::class, 'show'])->name('show');
    Route::post('/{chat}/messages', [ChatController::class, 'sendMessage'])->name('messages.store');
});

Route::middleware(['auth'])->prefix('admin/chats')->name('admin.chats.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\ChatController::class, 'index'])->name('index');
    Route::get('/{chat}', [\App\Http\Controllers\Admin\ChatController::class, 'show'])->name('show');
    Route::post('/{chat}/messages', [\App\Http\Controllers\Admin\ChatController::class, 'sendMessage'])->name('messages.store');
    Route::patch('/{chat}/status', [\App\Http\Controllers\Admin\ChatController::class, 'updateStatus'])->name('status.update');
});

require __DIR__ . '/auth.php';
