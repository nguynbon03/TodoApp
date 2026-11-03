<?php

namespace App\Http\Requests\Todos;

use App\Models\Todo;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTodoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request (Anti-IDOR ownership check).
     */
    public function authorize(): bool
    {
        $todo = $this->route('todo');

        return $todo instanceof Todo
            && $this->user() !== null
            && $todo->user_id === $this->user()->id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'completed' => ['sometimes', 'boolean'],
        ];
    }
}
