<?php

use App\Http\Api\Drug\Actions\CreateDrugAction;
use App\Http\Api\Drug\Actions\GetDrugAction;
use App\Http\Api\Drug\Actions\GetDrugListAction;

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

Route::get('/csrf_token', \App\Http\Api\Common\Action\GetCsrfTokenAction::class);

Route:: group([], function() {
   /* Drugs */
    Route::group([
        'prefix' => 'drugs'
    ], function() {
        Route::get('/', GetDrugListAction::class)->name('api.drugs.index');
        Route::post('/', CreateDrugAction::class)->name('api.drugs.create');
        Route::get('/{drugId}', GetDrugAction::class)->name('api.drugs.show');
    });
});
