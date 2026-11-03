<?php

namespace App\Http\Controllers;

use App\Actions\Todos\CreateTodo;
use App\Actions\Todos\DeleteTodo;
use App\Actions\Todos\UpdateTodo;
use App\Http\Requests\Todos\StoreTodoRequest;
use App\Http\Requests\Todos\UpdateTodoRequest;
use App\Models\Todo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TodoController extends Controller
{
    /**
     * Store a newly created todo item.
     */
    public function store(StoreTodoRequest $request, CreateTodo $createTodo): RedirectResponse
    {
        $createTodo->handle($request->user(), $request->validated('title'));

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Đã thêm công việc mới.')]);

        return back();
    }

    /**
     * Update the specified todo item (toggle completed or edit title).
     */
    public function update(UpdateTodoRequest $request, Todo $todo, UpdateTodo $updateTodo): RedirectResponse
    {
        $updateTodo->handle($todo, $request->validated());

        return back();
    }

    /**
     * Remove the specified todo item.
     */
    public function destroy(Request $request, Todo $todo, DeleteTodo $deleteTodo): RedirectResponse
    {
        abort_if($todo->user_id !== $request->user()?->id, 403);

        $deleteTodo->handle($todo);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Đã xóa công việc.')]);

        return back();
    }
}
