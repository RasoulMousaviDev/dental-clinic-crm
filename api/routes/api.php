<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\LeadSourceController;
use App\Http\Controllers\OTPCodeController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PatientStatusController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::controller(AuthController::class)->prefix('auth')->group(function () {
        Route::post('login', 'login')->withoutMiddleware('auth:api');
        Route::post('me', 'me');
        Route::post('refresh', 'refresh');
        Route::post('logout', 'logout');
    });

    Route::controller(OTPCodeController::class)->prefix('otp-code')->withoutMiddleware('auth:api')->group(function () {
        Route::post('generate', 'generate');
        Route::post('verify', 'verify');
    });

    Route::controller(PasswordController::class)->prefix('password')->group(function () {
        Route::post('reset', 'reset');
        Route::post('change', 'change');
    });

    Route::controller(UserController::class)->prefix('users')->group(function () {
        Route::get('', 'index');
        Route::post('', 'store');
        Route::patch('/{user}', 'update');
        Route::delete('/{user}', 'destroy');
    });

    Route::get('roles', [RoleController::class, 'index']);

    Route::controller(PatientController::class)->prefix('patients')->group(function () {
        Route::get('', 'index');
        Route::post('', 'store');
        Route::patch('/{patient}', 'update');
        Route::delete('/{patient}', 'destroy');
    });

    Route::get('provinces', [ProvinceController::class, 'index']);
    Route::get('cities', [CityController::class, 'index']);
    Route::get('lead-sources', [LeadSourceController::class, 'index']);
    Route::get('patient-statuses', [PatientStatusController::class, 'index']);
});
