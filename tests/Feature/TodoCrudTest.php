<?php

use App\Models\Todo;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('authenticated user sees only their own todos on the dashboard', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $myTodo = Todo::factory()->for($user)->create(['title' => 'Viết code Laravel chuẩn']);
    Todo::factory()->for($otherUser)->create(['title' => 'Việc của người khác']);

    $response = $this
        ->actingAs($user)
        ->get(route('dashboard', ['current_team' => $user->currentTeam]));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Dashboard')
        ->has('todos', 1)
        ->where('todos.0.id', $myTodo->id)
        ->where('todos.0.title', 'Viết code Laravel chuẩn')
        ->where('todos.0.completed', false),
    );
});

test('authenticated user can create a todo item', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from(route('dashboard'))
        ->post(route('todos.store'), [
            'title' => '  Học Vue 3 + Inertia v3  ',
        ]);

    $response->assertRedirect(route('dashboard'));

    $this->assertDatabaseHas('todos', [
        'user_id' => $user->id,
        'title' => 'Học Vue 3 + Inertia v3',
        'completed' => false,
    ]);
});

test('todo title is required and cannot exceed 255 characters', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('todos.store'), ['title' => ''])
        ->assertSessionHasErrors('title');

    $this->actingAs($user)
        ->post(route('todos.store'), ['title' => str_repeat('a', 256)])
        ->assertSessionHasErrors('title');
});

test('authenticated user can toggle completed and update title of their todo', function () {
    $user = User::factory()->create();
    $todo = Todo::factory()->for($user)->create([
        'title' => 'Công việc ban đầu',
        'completed' => false,
    ]);

    $this->actingAs($user)
        ->patch(route('todos.update', $todo), [
            'completed' => true,
            'title' => 'Công việc đã cập nhật',
        ])
        ->assertRedirect();

    expect($todo->fresh())
        ->completed->toBeTrue()
        ->title->toBe('Công việc đã cập nhật');
});

test('authenticated user can delete their own todo', function () {
    $user = User::factory()->create();
    $todo = Todo::factory()->for($user)->create();

    $this->actingAs($user)
        ->delete(route('todos.destroy', $todo))
        ->assertRedirect();

    $this->assertDatabaseMissing('todos', ['id' => $todo->id]);
});

test('user cannot update or delete another users todo (anti-idor)', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    $todo = Todo::factory()->for($owner)->create(['title' => 'Bí mật']);

    $this->actingAs($intruder)
        ->patch(route('todos.update', $todo), ['title' => 'Hack'])
        ->assertForbidden();

    $this->actingAs($intruder)
        ->delete(route('todos.destroy', $todo))
        ->assertForbidden();

    $this->assertDatabaseHas('todos', [
        'id' => $todo->id,
        'title' => 'Bí mật',
    ]);
});

test('authenticated user can perform full crud via routes/api.php endpoints', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $otherTodo = Todo::factory()->for($otherUser)->create(['title' => 'Other']);

    // 1. POST /api/todos
    $createResponse = $this->actingAs($user)
        ->postJson('/api/todos', ['title' => 'Viết API bằng Axios'])
        ->assertCreated();

    $todoId = $createResponse->json('data.id');
    expect($createResponse->json('data.title'))->toBe('Viết API bằng Axios');

    // 2. GET /api/todos
    $this->actingAs($user)
        ->getJson('/api/todos')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $todoId);

    // 3. PATCH /api/todos/{todo}
    $this->actingAs($user)
        ->patchJson("/api/todos/{$todoId}", [
            'title' => 'Đã cập nhật qua Axios',
            'completed' => true,
        ])
        ->assertOk()
        ->assertJsonPath('data.title', 'Đã cập nhật qua Axios')
        ->assertJsonPath('data.completed', true);

    // 4. Anti-IDOR on /api/todos/{todo}
    $this->actingAs($user)
        ->patchJson("/api/todos/{$otherTodo->id}", ['title' => 'Hack'])
        ->assertForbidden();

    $this->actingAs($user)
        ->deleteJson("/api/todos/{$otherTodo->id}")
        ->assertForbidden();

    // 5. DELETE /api/todos/{todo}
    $this->actingAs($user)
        ->deleteJson("/api/todos/{$todoId}")
        ->assertOk();

    $this->assertDatabaseMissing('todos', ['id' => $todoId]);
});
