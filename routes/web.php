<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::redirect('/', '/admin/auth/login');

Route::prefix('admin')->group(function() {
    /* Auth */
    Route::prefix('auth')->group(function () {
        Route::get('/login', 'Admin\Auth\LoginController@showLoginForm')->name('admin.auth.login');
        Route::post('/login', 'Admin\Auth\LoginController@login')->name('admin.auth.login.post');
        Route::post('/logout', 'Admin\Auth\LoginController@logout')->name('admin.auth.logout');
    });

    Route::group(['middleware' => 'auth:web'], function () {
        /* Top Page */
        Route::get('/top', 'Admin\HomeController@index')->name('admin.top_page');

        /* Admin Users */
        Route::prefix('admin_users')->group(function () {
            Route::get('/','Admin\AdminUserController@index')->name('admin.admin_users.index');
            Route::get('/create','Admin\AdminUserController@create')->name('admin.admin_users.create');
            Route::post('/','Admin\AdminUserController@store')->name('admin.admin_users.store');
            Route::get('/{adminUser}/edit','Admin\AdminUserController@edit')->where('user', '[0-9]+')->name('admin.admin_users.edit');
            Route::put('/{adminUser}','Admin\AdminUserController@update')->where('user', '[0-9]+')->name('admin.admin_users.update');
            Route::delete('/{adminUser}','Admin\AdminUserController@destroy')->where('user', '[0-9]+')->name('admin.admin_users.destroy');
        });

        /* Drugs */
        Route::prefix('drugs')->group(function () {
            Route::get('/', 'Admin\DrugController@index')->name('admin.drugs.index');
            Route::get('/create', 'Admin\DrugController@create')->name('admin.drugs.create');
            Route::post('/', 'Admin\DrugController@store')->name('admin.drugs.store');
            Route::get('/edit/{drug}', 'Admin\DrugController@edit')->name('admin.drugs.edit');
            Route::post('/update/{drug}', 'Admin\DrugController@update')->name('admin.drugs.update');
            Route::post('/{drug}', 'Admin\DrugController@delete')->name('admin.drugs.delete');
        });

        Route::prefix('medication_histories')->group(function () {
            Route::get('/', 'Admin\MedicationHistoryController@index')->name('admin.medication_histories.index');
            Route::get('/edit/{medicationHistory}', 'Admin\MedicationHistoryController@edit')->name('admin.medication_histories.edit');
            Route::post('/update/{medicationHistory}', 'Admin\MedicationHistoryController@update')->name('admin.medication_histories.update');
        });
    });
});
