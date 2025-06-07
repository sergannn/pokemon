<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CoordinateImportController;
use App\Http\Controllers\MarkerController;

use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;

Route::get( '/import-coordinates', [ CoordinateImportController::class, 'import' ] );
Route::get('/import-progress', [CoordinateImportController::class, 'progressListener'])->name('import.progress');

//Route::get( '{markerId}/presents', [ MarkerController::class, 'getPresentsByMarkerWeb' ] )->name( 'markers.presents' );
  
Route::get( '/', function () {
    return view( 'map' );
} );

Route::get( '/import', function () {
    return view( 'import' );
} );




Route::get( '/webmarkers', [ MarkerController::class, 'index' ] )//'webindex'])
->name( 'markers.webindex' );

