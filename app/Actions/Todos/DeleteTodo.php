<?php

namespace App\Actions\Todos;

use App\Models\Todo;

class DeleteTodo
{
    /**
     * Delete the given todo item.
     */
    public function handle(Todo $todo): void
    {
        $todo->delete();
    }
}
