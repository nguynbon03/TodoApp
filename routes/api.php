<?php

use App\Http\Controllers\Api\TodoApiController;
use Illuminate\Support\Facades\Route;

Route::apiResource('todos', TodoApiController::class)->only([
    'index',
    'store',
    'update',
    'destroy',
]);
