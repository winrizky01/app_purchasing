<?php

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

    // auth
    Route::get('/login', [App\Http\Controllers\AuthController::class, 'login'])->name('login');
    Route::post('/login', [App\Http\Controllers\AuthController::class, 'login_process']);
    Route::get('/register', [App\Http\Controllers\AuthController::class, 'register']);
    Route::get('/forgot_password', [App\Http\Controllers\AuthController::class, 'forgot_password'])->name('forgot_password');
    Route::post('/forgot_password', [App\Http\Controllers\AuthController::class, 'forgot_password_process']);
    Route::get('/reset_password', [App\Http\Controllers\AuthController::class, 'reset_password']);
    Route::post('/reset_password', [App\Http\Controllers\AuthController::class, 'reset_password_process']);
    Route::get('/logout', [App\Http\Controllers\AuthController::class, 'logout']);

    Route::group(['middleware'=>['auth']], function(){
        // dashboard
        Route::get('/', [App\Http\Controllers\DashboardController::class, 'index']);
        Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index']);

        Route::prefix('master')->group(function(){
            Route::prefix('user')->group(function(){
                Route::get('/', [App\Http\Controllers\UserController::class, 'index']);
                Route::get('/create', [App\Http\Controllers\UserController::class, 'create']);
                Route::get('/edit/{id}', [App\Http\Controllers\UserController::class, 'edit']);
                Route::get('/dataTables', [App\Http\Controllers\UserController::class, 'dataTables']);
                Route::get('/select', [App\Http\Controllers\UserController::class, 'select']);

                Route::post('/store', [App\Http\Controllers\UserController::class, 'store']);
                Route::post('/update/{id}', [App\Http\Controllers\UserController::class, 'update']);

                Route::delete('/delete/{id}', [App\Http\Controllers\UserController::class, 'destroy']);
            });
        });

        Route::prefix('setting')->group(function(){
            Route::prefix('general')->group(function(){
                Route::get('/', [App\Http\Controllers\GeneralController::class, 'index']);
                Route::get('/create', [App\Http\Controllers\GeneralController::class, 'create']);
                Route::get('/edit/{id}', [App\Http\Controllers\GeneralController::class, 'edit']);
                Route::get('/dataTables', [App\Http\Controllers\GeneralController::class, 'dataTables']);
                Route::get('/select', [App\Http\Controllers\GeneralController::class, 'select']);

                Route::post('/store', [App\Http\Controllers\GeneralController::class, 'store']);
                Route::post('/update/{id}', [App\Http\Controllers\GeneralController::class, 'update']);

                Route::delete('/delete/{id}', [App\Http\Controllers\GeneralController::class, 'destroy']);
            });

            Route::prefix('role')->group(function(){
                Route::get('/', [App\Http\Controllers\RoleController::class, 'index']);
                Route::get('/create', [App\Http\Controllers\RoleController::class, 'create']);
                Route::get('/edit/{id}', [App\Http\Controllers\RoleController::class, 'edit']);
                Route::get('/dataTables', [App\Http\Controllers\RoleController::class, 'dataTables']);
                Route::get('/select', [App\Http\Controllers\RoleController::class, 'select']);

                Route::post('/store', [App\Http\Controllers\RoleController::class, 'store']);
                Route::post('/update/{id}', [App\Http\Controllers\RoleController::class, 'update']);

                Route::delete('/delete/{id}', [App\Http\Controllers\RoleController::class, 'destroy']);
            });

            Route::prefix('menu')->group(function(){
                Route::prefix('parent')->group(function(){
                    Route::get('/select', [App\Http\Controllers\MenuParentController::class, 'select']);
                });
                Route::prefix('children')->group(function(){
                    Route::get('/select', [App\Http\Controllers\MenuChildrenController::class, 'select']);
                });
                Route::prefix('subchildren')->group(function(){
                    Route::get('/select', [App\Http\Controllers\MenuSubChildrenController::class, 'select']);
                });
            });

        });

    });
