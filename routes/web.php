<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CoordinateImportController;
use App\Http\Controllers\MarkerController;

use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;

Route::get( '/import-coordinates', [ CoordinateImportController::class, 'import' ] );
Route::get('/import-progress', [CoordinateImportController::class, 'progressListener'])->name('import.progress');

Route::get( '{markerId}/presents', [ MarkerController::class, 'getPresentsByMarkerWeb' ] )->name( 'markers.presents' );
  
Route::get( '/', function () {
    return view( 'map' );
} );

Route::get( '/import', function () {
    return view( 'import' );
} );

//Route::prefix( 'auth' )->group( function () {
//    Route::post( 'login', [ AuthController::class, 'login' ] );
//    Route::post( 'logout', [ AuthController::class, 'logout' ] );
//    Route::post( 'refresh', [ AuthController::class, 'refresh' ] );
//    Route::post( 'me', [ AuthController::class, 'me' ] );
//} );


Route::get( '/markers', [ MarkerController::class, 'index' ] )
    ->name( 'markers.index' );
    Route::get( '/users', [ UserController::class, 'index' ] )
    ->name( 'users.index' );
    Route::get('/users/{user}/presents', [UserController::class, 'show']);

Route::get( '/webmarkers', [ MarkerController::class, 'index' ] )//'webindex'])
->name( 'markers.webindex' );

//Route::get( '/markers/{markerId}/presents', [ MarkerController::class, 'getPresentsByMarker' ] )
//    ->name( 'markers.presents' );
//
//Route::get( '/markers/{markerId}/status', [ MarkerController::class, 'getStatus' ] )
//    ->name( 'markers.status' );
//
//Route::patch( '/markers/{markerId}/status', [ MarkerController::class, 'updateStatus' ] )
//    ->name( 'markers.update-status' );
