<?php

use App\Http\Controllers\Api\FileController;
use App\Http\Controllers\Api\FolderController;
use App\Http\Controllers\Api\TokenAuthController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\EmailVerificationNotificationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::prefix('v1')->group(static function () {

    /**
     * User auth/registration resources
     */
    Route::middleware('guest')->group(
        static function() {
            $limiter = config('fortify.limiters.login');

            Route::post('/token', [TokenAuthController::class, 'store'])->middleware(
                array_filter([$limiter ? 'throttle:'.$limiter : null])
            );

            Route::post('/registration', [UserController::class, 'store'])->name('auth.registration');

            /**
             * Public link download endpoint (probably could be put in other place instead of /api/v1/*)
             */
	        Route::get('/files/public/{file:uuid}/{hash}', [FileController::class, 'publicDownload'])->name('files.public.link');

        }
    );

    Route::middleware('auth:sanctum')->group(
        static function () {

            /*
             * User resources
             *
             */
            Route::delete('/token', [TokenAuthController::class, 'destroy'])->name('auth.logout');
            Route::get('/profile', [UserController::class, 'show'])->name('auth.profile');
            Route::patch('/profile', [UserController::class, 'update'])->name('auth.profile.update');
            Route::get('/profile/set-password', [UserController::class, 'setPassword'])->name('auth.password.update');

            /*
             * In case when production will be deployed and User will need verification via link sent to mail
             * (by default it is not configured to make easier testing but can be returned back by adding fortify EmailVerify Action)
             */
            Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
                 ->middleware(['auth', 'throttle:6,1'])
                 ->name('verification.send');


            /*
             * File resources
             */
            Route::apiResource('files', FileController::class);

            Route::group(['prefix' => 'files'], function (){
                Route::get('/{file:uuid}/link', [FileController::class, 'generatePublicLink'])->name('files.public.link.create');
                Route::get('/{file}/download', [FileController::class, 'download'])->name('files.download');
            });

            /*
             * Folder resources
             */
            Route::apiResource('folders', FolderController::class);
        }
    );



});
