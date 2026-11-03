<?php

namespace Database\Seeders;

use App\Models\Todo;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Todo::factory()->create([
            'title' => 'Thiết kế kiến trúc 5 lớp Clean Architecture',
            'completed' => true,
        ]);

        Todo::factory()->create([
            'title' => 'Tích hợp Vue 3 + Inertia.js + REST API',
            'completed' => false,
        ]);

        Todo::factory()->create([
            'title' => 'Kiểm thử toàn diện với PestPHP và PHPStan',
            'completed' => false,
        ]);
    }
}
