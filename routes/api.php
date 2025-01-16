<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MarkerController;
use App\Http\Controllers\AuthController;

Route::prefix( 'auth' )->group( function () {
    Route::get( 'register', [ AuthController::class, 'register' ] );
    Route::post( 'register', [ AuthController::class, 'register' ] );
    Route::post( 'login', [ AuthController::class, 'login' ] )->name( 'login' );
    Route::post( 'logout', [ AuthController::class, 'logout' ] );
    Route::post( 'refresh', [ AuthController::class, 'refresh' ] );
    Route::get( 'me', [ AuthController::class, 'me' ] );
    Route::post( 'me', [ AuthController::class, 'me' ] );
} );


Route::group( [ 'middleware' => 'auth:api', 'prefix' => 'markers' ], function ( $router ) {
    Route::get( '/', [ MarkerController::class, 'index' ] )->name( 'markers.index' );
    Route::get( '{markerId}/presents', [ MarkerController::class, 'getPresentsByMarker' ] )->name( 'markers.presents' );
    Route::get( '{markerId}/status', [ MarkerController::class, 'getStatus' ] )->name( 'markers.status' );
    Route::patch( '{markerId}/status', [ MarkerController::class, 'updateStatus' ] )->name( 'markers.update-status' );
} );
