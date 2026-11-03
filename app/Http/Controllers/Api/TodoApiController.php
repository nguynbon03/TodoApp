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
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TodoApiController extends Controller
{
    /**
     * GET /api/todos — Lấy danh sách công việc (mới nhất xếp trước).
     */
    public function index(): AnonymousResourceCollection
    {
        $todos = Todo::query()->latest()->get();

        return TodoResource::collection($todos);
    }

    /**
     * POST /api/todos — Tạo công việc mới (Validate qua StoreTodoRequest -> Lưu DB qua CreateTodo -> Trả JSON 201 qua TodoResource).
     */
    public function store(StoreTodoRequest $request, CreateTodo $createTodo): JsonResponse
    {
        /** @var string $title */
        $title = $request->validated('title');

        $todo = $createTodo->handle($title);

        return (new TodoResource($todo))->response()->setStatusCode(201);
    }

    /**
     * PATCH /api/todos/{todo} — Cập nhật trạng thái hoàn thành hoặc tiêu đề (Validate qua UpdateTodoRequest).
     */
    public function update(UpdateTodoRequest $request, Todo $todo, UpdateTodo $updateTodo): TodoResource
    {
        /** @var array{title?: string, completed?: bool} $validated */
        $validated = $request->validated();

        $updated = $updateTodo->handle($todo, $validated);

        return new TodoResource($updated);
    }

    /**
     * DELETE /api/todos/{todo} — Xóa công việc qua DeleteTodo.
     */
    public function destroy(Todo $todo, DeleteTodo $deleteTodo): JsonResponse
    {
        $deleteTodo->handle($todo);

        return response()->json(['message' => 'Đã xóa công việc thành công.']);
    }
}
