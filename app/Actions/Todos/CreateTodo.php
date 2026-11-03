<?php

namespace App\Actions\Todos;

use App\Models\Todo;
use App\Models\User;

class CreateTodo
{
    /**
     * Create a new todo item for the given user.
     */
    public function handle(User $user, string $title): Todo
    {
        return $user->todos()->create([
            'title' => trim($title),
            'completed' => false,
        ]);
    }
}
