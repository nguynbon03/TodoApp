<?php

namespace App\Actions\Todos;

use App\Models\Todo;

class CreateTodo
{
    /**
     * Create a new todo item.
     */
    public function handle(string $title): Todo
    {
        return Todo::query()->create([
            'title' => trim($title),
            'completed' => false,
        ]);
    }
}
