<?php

Route::group(['middleware' => 'web', 'prefix' => \Helper::getSubdirectory(), 'namespace' => 'Modules\MaisenExtendetMailVar\Http\Controllers'], function()
{
    Route::get('/', 'MaisenExtendetMailVarController@index');
});
