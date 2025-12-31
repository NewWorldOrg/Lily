<?php

use App\Http\Api\Drug\Actions\CreateDrugAction;
use App\Http\Api\Drug\Actions\DeleteDrugAction;
use App\Http\Api\Drug\Actions\GetDrugAction;
use App\Http\Api\Drug\Actions\GetDrugListAction;
use App\Http\Api\MedicationHistory\Actions\CreateMedicationHistoryAction;
use App\Http\Api\MedicationHistory\Actions\GetMedicationHistoryAction;
use App\Http\Api\MedicationHistory\Actions\GetMedicationHistoryListAction;

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
        Route::get('/{id}', GetDrugAction::class)->name('api.drugs.show');
        Route::delete('/{id}', DeleteDrugAction::class)->name('api.drugs.delete');
    });

    Route::group([
        'prefix' => 'medication_histories'
    ], function() {
        Route::get('/', GetMedicationHistoryListAction::class)->name('api.medication_histories.index');
        Route::post('/', CreateMedicationHistoryAction::class)->name('api.medication_histories.create');
        Route::get('/{id}', GetMedicationHistoryAction::class)->name('api.medication_histories.detail');
    });
});
