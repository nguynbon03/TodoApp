<?php

namespace App\Http\Controllers;

use App\Http\Resources\TodoResource;
use App\Models\Todo;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the standalone Todo dashboard.
     */
    public function __invoke(Request $request): Response
    {
        $todos = TodoResource::collection(
            Todo::query()->latest()->get()
        )->resolve($request);

        return Inertia::render('Dashboard', [
            'todos' => $todos,
        ]);
    }
}
