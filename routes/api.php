<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MarkerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;


Route::prefix( 'auth' )->group( function () {
    //Route::get('check-username', [AuthController::class, 'checkUsername']);
    //третий аргумент - не название метода
    Route::get('checkusername', [AuthController::class, 'checkusername']);    
    Route::get('checknickname', [AuthController::class, 'checknickname']);    
    Route::get('changepassword', [AuthController::class, 'changepassword']);
 
    Route::get( 'register', [ AuthController::class, 'register' ] );
    Route::post( 'register', [ AuthController::class, 'register' ] );
    Route::post( 'login', [ AuthController::class, 'login' ] );//->name( 'login' );
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

Route::group( [ 'middleware' => 'auth:api', 'prefix' => 'user' ], function ( $router ) {
    // Coins routes
    Route::get( '/coins', [ UserController::class, 'getCoins' ] )->name( 'user.coins' );
    Route::patch( '/coins', [ UserController::class, 'updateCoins' ] )->name( 'user.update-coins' );
    
    // Timer routes
    Route::get( '/timer', [ UserController::class, 'getTimer' ] )->name( 'user.timer' );
    Route::patch( '/timer', [ UserController::class, 'setTimer' ] )->name( 'user.set-timer' );
    Route::post( '/timer/reset', [ UserController::class, 'resetTimer' ] )->name( 'user.reset-timer' );
    //both
    Route::get( '/timer_and_coins', [ UserController::class, 'getTimerAndCoins' ] );//->name( 'user.timer' );
} );
