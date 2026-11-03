<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\Todos\CreateTodo;
use App\Actions\Todos\DeleteTodo;
use App\Actions\Todos\UpdateTodo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Todos\StoreTodoRequest;
use App\Http\Requests\Todos\UpdateTodoRequest;
use App\Http\Resources\TodoResource;
use App\Models\Todo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TodoApiController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $todos = $request->user()->todos()->latest()->get();

        return TodoResource::collection($todos);
    }

    public function store(StoreTodoRequest $request, CreateTodo $createTodo): JsonResponse
    {
        $todo = $createTodo->handle($request->user(), $request->validated('title'));

        return (new TodoResource($todo))->response()->setStatusCode(201);
    }

    public function update(UpdateTodoRequest $request, Todo $todo, UpdateTodo $updateTodo): TodoResource
    {
        $updated = $updateTodo->handle($todo, $request->validated());

        return new TodoResource($updated);
    }

    public function destroy(Request $request, Todo $todo, DeleteTodo $deleteTodo): JsonResponse
    {
        abort_if($todo->user_id !== $request->user()?->id, 403);

        $deleteTodo->handle($todo);

        return response()->json(['message' => 'Đã xóa công việc thành công.']);
    }
}
