<?php

use App\Http\Controllers\Api\TodoApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('/user', fn (Request $request) => response()->json([
        'data' => [
            'id' => $request->user()->id,
            'name' => $request->user()->name,
            'email' => $request->user()->email,
        ],
    ]))->name('api.user');

    Route::get('/todos', [TodoApiController::class, 'index'])->name('api.todos.index');
    Route::post('/todos', [TodoApiController::class, 'store'])->name('api.todos.store');
    Route::patch('/todos/{todo}', [TodoApiController::class, 'update'])->name('api.todos.update');
    Route::delete('/todos/{todo}', [TodoApiController::class, 'destroy'])->name('api.todos.destroy');
});
