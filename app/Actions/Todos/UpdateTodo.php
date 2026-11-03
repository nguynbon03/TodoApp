<?php

namespace App\Actions\Todos;

use App\Models\Todo;

class UpdateTodo
{
    /**
     * Update the given todo item.
     *
     * @param  array{title?: string, completed?: bool}  $attributes
     */
    public function handle(Todo $todo, array $attributes): Todo
    {
        if (array_key_exists('title', $attributes)) {
            $attributes['title'] = trim($attributes['title']);
        }

        $todo->update($attributes);

        return $todo;
    }
}
