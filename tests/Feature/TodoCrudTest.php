<?php

declare(strict_types=1);

use App\Models\Todo;
use Inertia\Testing\AssertableInertia as Assert;

test('GET / renders the standalone Dashboard page with initial todos', function () {
    $todo = Todo::factory()->create([
        'title' => 'Viết code Laravel 13 chuẩn Clean Architecture',
        'completed' => false,
    ]);

    $response = $this->get('/');

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Dashboard')
        ->has('todos', 1)
        ->where('todos.0.id', $todo->id)
        ->where('todos.0.title', 'Viết code Laravel 13 chuẩn Clean Architecture')
        ->where('todos.0.completed', false),
    );
});

test('GET /dashboard renders the standalone Dashboard page with todos ordered latest first', function () {
    $olderTodo = Todo::factory()->create([
        'title' => 'Việc cũ hơn',
        'completed' => true,
        'created_at' => now()->subMinutes(10),
    ]);
    $newerTodo = Todo::factory()->create([
        'title' => 'Việc mới nhất',
        'completed' => false,
        'created_at' => now(),
    ]);

    $response = $this->get(route('dashboard'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Dashboard')
        ->has('todos', 2)
        ->where('todos.0.id', $newerTodo->id)
        ->where('todos.1.id', $olderTodo->id),
    );
});

test('GET /api/todos returns JSON list of todos wrapped in TodoResource', function () {
    $todo = Todo::factory()->create([
        'title' => 'Học Vue 3.5 + Axios REST API',
        'completed' => false,
    ]);

    $this->getJson('/api/todos')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $todo->id)
        ->assertJsonPath('data.0.title', 'Học Vue 3.5 + Axios REST API')
        ->assertJsonPath('data.0.completed', false)
        ->assertJsonStructure([
            'data' => [
                ['id', 'title', 'completed', 'createdAt'],
            ],
        ]);
});

test('POST /api/todos creates a new todo item with trimmed title', function () {
    $response = $this->postJson('/api/todos', [
        'title' => '  Hoàn thiện 5-Layer Clean Architecture  ',
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.title', 'Hoàn thiện 5-Layer Clean Architecture')
        ->assertJsonPath('data.completed', false);

    $this->assertDatabaseHas('todos', [
        'id' => $response->json('data.id'),
        'title' => 'Hoàn thiện 5-Layer Clean Architecture',
        'completed' => false,
    ]);
});

test('POST /api/todos returns 422 validation error when title is empty or exceeds 255 characters', function () {
    $this->postJson('/api/todos', ['title' => ''])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['title']);

    $this->postJson('/api/todos', ['title' => str_repeat('a', 256)])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['title']);
});

test('PATCH /api/todos/{todo} toggles completed status and updates title', function () {
    $todo = Todo::factory()->create([
        'title' => 'Công việc ban đầu',
        'completed' => false,
    ]);

    $this->patchJson("/api/todos/{$todo->id}", [
        'title' => '  Công việc đã cập nhật  ',
        'completed' => true,
    ])
        ->assertOk()
        ->assertJsonPath('data.id', $todo->id)
        ->assertJsonPath('data.title', 'Công việc đã cập nhật')
        ->assertJsonPath('data.completed', true);

    expect($todo->fresh())
        ->title->toBe('Công việc đã cập nhật')
        ->completed->toBeTrue();
});

test('PATCH /api/todos/{todo} returns 422 validation error when title is empty or exceeds 255 characters', function () {
    $todo = Todo::factory()->create(['title' => 'Hợp lệ']);

    $this->patchJson("/api/todos/{$todo->id}", ['title' => ''])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['title']);

    $this->patchJson("/api/todos/{$todo->id}", ['title' => str_repeat('b', 256)])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['title']);
});

test('DELETE /api/todos/{todo} removes the todo item from database', function () {
    $todo = Todo::factory()->create(['title' => 'Việc cần xóa']);

    $this->deleteJson("/api/todos/{$todo->id}")
        ->assertSuccessful();

    $this->assertDatabaseMissing('todos', [
        'id' => $todo->id,
    ]);
});

test('PATCH and DELETE /api/todos/{todo} return 404 for non-existent todo', function () {
    $this->patchJson('/api/todos/999999', [
        'title' => 'Không tồn tại',
        'completed' => true,
    ])->assertNotFound();

    $this->deleteJson('/api/todos/999999')
        ->assertNotFound();
});
