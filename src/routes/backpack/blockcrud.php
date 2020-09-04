<?php

/*
|--------------------------------------------------------------------------
| Backpack\BlockCRUD Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are
| handled by the Backpack\BlockCRUD package.
|
*/

Route::group([
    'prefix' => config('backpack.base.route_prefix', 'admin'), 
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
    'namespace' => 'Admin',
], function () {
    Route::crud('blocks', '\Backpack\BlockCRUD\app\Http\Controllers\Admin\BlockItemCrudController');
});
