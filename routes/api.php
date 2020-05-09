<?php

use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Api'], static function () {
    Route::get('/balance/{account}', [
        'as' => 'balance:show',
        'uses' => 'AccountController@show'
    ]);

    Route::put('/balance/{account}', [
        'as' => 'balance:update',
        'uses' => 'AccountController@update'
    ]);
});
