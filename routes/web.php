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

        Route::prefix('purchasing')->group(function(){
            Route::prefix('purchase-request')->group(function(){
                Route::get('/', [App\Http\Controllers\PurchaseRequestController::class, 'index']);
                Route::get('/create', [App\Http\Controllers\PurchaseRequestController::class, 'create']);
                Route::get('/edit/{id}', [App\Http\Controllers\PurchaseRequestController::class, 'edit']);
                Route::get('/dataTables', [App\Http\Controllers\PurchaseRequestController::class, 'dataTables']);
                Route::get('/select', [App\Http\Controllers\PurchaseRequestController::class, 'select']);
                Route::get('/select', [App\Http\Controllers\PurchaseRequestController::class, 'select']);
                Route::get('/print/{id}', [App\Http\Controllers\PurchaseRequestController::class, 'print']);

                Route::post('/store', [App\Http\Controllers\PurchaseRequestController::class, 'store']);
                Route::post('/update/{id}', [App\Http\Controllers\PurchaseRequestController::class, 'update']);

                Route::delete('/delete/{id}', [App\Http\Controllers\PurchaseRequestController::class, 'destroy']);

            });
            Route::prefix('purchase-order')->group(function(){
                Route::get('/', [App\Http\Controllers\PurchaseOrderController::class, 'index']);
                Route::get('/create', [App\Http\Controllers\PurchaseOrderController::class, 'create']);
                Route::get('/edit/{id}', [App\Http\Controllers\PurchaseOrderController::class, 'edit']);
                Route::get('/dataTables', [App\Http\Controllers\PurchaseOrderController::class, 'dataTables']);
                Route::get('/select', [App\Http\Controllers\PurchaseOrderController::class, 'select']);
                Route::get('/select', [App\Http\Controllers\PurchaseOrderController::class, 'select']);
                Route::get('/print/{id}', [App\Http\Controllers\PurchaseOrderController::class, 'print']);

                Route::post('/store', [App\Http\Controllers\PurchaseOrderController::class, 'store']);
                Route::post('/update/{id}', [App\Http\Controllers\PurchaseOrderController::class, 'update']);

                Route::delete('/delete/{id}', [App\Http\Controllers\PurchaseOrderController::class, 'destroy']);
            });
        });

        Route::prefix('inventory')->group(function(){
            Route::prefix('material-request')->group(function(){
                Route::get('/', [App\Http\Controllers\MaterialRequestController::class, 'index']);
                Route::get('/create', [App\Http\Controllers\MaterialRequestController::class, 'create']);
                Route::get('/edit/{id}', [App\Http\Controllers\MaterialRequestController::class, 'edit']);
                Route::get('/dataTables', [App\Http\Controllers\MaterialRequestController::class, 'dataTables']);
                Route::get('/select', [App\Http\Controllers\MaterialRequestController::class, 'select']);
                Route::get('/select', [App\Http\Controllers\MaterialRequestController::class, 'select']);
                Route::get('/print/{id}', [App\Http\Controllers\MaterialRequestController::class, 'print']);
                Route::get('/show/{id}', [App\Http\Controllers\MaterialRequestController::class, 'edit']);

                Route::post('/store', [App\Http\Controllers\MaterialRequestController::class, 'store']);
                Route::post('/update/{id}', [App\Http\Controllers\MaterialRequestController::class, 'update']);

                Route::delete('/delete/{id}', [App\Http\Controllers\MaterialRequestController::class, 'destroy']);
            });

            Route::prefix('material-usage')->group(function(){
                Route::get('/', [App\Http\Controllers\MaterialUsageController::class, 'index']);
                Route::get('/create', [App\Http\Controllers\MaterialUsageController::class, 'create']);
                Route::get('/edit/{id}', [App\Http\Controllers\MaterialUsageController::class, 'edit']);
                Route::get('/dataTables', [App\Http\Controllers\MaterialUsageController::class, 'dataTables']);
                Route::get('/select', [App\Http\Controllers\MaterialUsageController::class, 'select']);
                Route::get('/select', [App\Http\Controllers\MaterialUsageController::class, 'select']);
                Route::get('/print/{id}', [App\Http\Controllers\MaterialUsageController::class, 'print']);

                Route::post('/store', [App\Http\Controllers\MaterialRequestController::class, 'store']);
                Route::post('/update/{id}', [App\Http\Controllers\MaterialRequestController::class, 'update']);

                Route::delete('/delete/{id}', [App\Http\Controllers\MaterialRequestController::class, 'destroy']);
            });

        });

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

            Route::prefix('warehouse')->group(function(){
                Route::get('/', [App\Http\Controllers\WarehouseController::class, 'index']);
                Route::get('/dataTables', [App\Http\Controllers\WarehouseController::class, 'dataTables']);
                Route::get('/select', [App\Http\Controllers\WarehouseController::class, 'select']);

                Route::post('/store', [App\Http\Controllers\WarehouseController::class, 'store']);
                Route::post('/update/{id}', [App\Http\Controllers\WarehouseController::class, 'update']);

                Route::delete('/delete/{id}', [App\Http\Controllers\WarehouseController::class, 'destroy']);
            });

            Route::prefix('product')->group(function(){
                Route::get('/', [App\Http\Controllers\ProductController::class, 'index']);
                Route::get('/create', [App\Http\Controllers\ProductController::class, 'create']);
                Route::get('/edit/{id}', [App\Http\Controllers\ProductController::class, 'edit']);
                Route::get('/dataTables', [App\Http\Controllers\ProductController::class, 'dataTables']);
                Route::get('/select', [App\Http\Controllers\ProductController::class, 'select']);

                Route::post('/store', [App\Http\Controllers\ProductController::class, 'store']);
                Route::post('/update/{id}', [App\Http\Controllers\ProductController::class, 'update']);

                Route::delete('/delete/{id}', [App\Http\Controllers\ProductController::class, 'destroy']);
            });

            Route::prefix('vendor')->group(function(){
                Route::get('/', [App\Http\Controllers\VendorController::class, 'index']);
                Route::get('/create', [App\Http\Controllers\VendorController::class, 'create']);
                Route::get('/edit/{id}', [App\Http\Controllers\VendorController::class, 'edit']);
                Route::get('/dataTables', [App\Http\Controllers\VendorController::class, 'dataTables']);
                Route::get('/select', [App\Http\Controllers\VendorController::class, 'select']);

                Route::post('/store', [App\Http\Controllers\VendorController::class, 'store']);
                Route::post('/update/{id}', [App\Http\Controllers\VendorController::class, 'update']);

                Route::delete('/delete/{id}', [App\Http\Controllers\VendorController::class, 'destroy']);
            });

            Route::prefix('department')->group(function(){
                Route::get('/', [App\Http\Controllers\DepartmentController::class, 'index']);
                Route::get('/dataTables', [App\Http\Controllers\DepartmentController::class, 'dataTables']);
                Route::get('/select', [App\Http\Controllers\DepartmentController::class, 'select']);

                Route::post('/store', [App\Http\Controllers\DepartmentController::class, 'store']);
                Route::post('/update/{id}', [App\Http\Controllers\DepartmentController::class, 'update']);

                Route::delete('/delete/{id}', [App\Http\Controllers\DepartmentController::class, 'destroy']);
            });

            Route::prefix('division')->group(function(){
                Route::get('/', [App\Http\Controllers\DivisionController::class, 'index']);
                Route::get('/dataTables', [App\Http\Controllers\DivisionController::class, 'dataTables']);
                Route::get('/select', [App\Http\Controllers\DivisionController::class, 'select']);

                Route::post('/store', [App\Http\Controllers\DivisionController::class, 'store']);
                Route::post('/update/{id}', [App\Http\Controllers\DivisionController::class, 'update']);

                Route::delete('/delete/{id}', [App\Http\Controllers\DivisionController::class, 'destroy']);
            });
        });

        Route::prefix('setting')->group(function(){
            Route::prefix('general')->group(function(){
                Route::get('/', [App\Http\Controllers\GeneralController::class, 'index']);
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
