<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Table;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function index(): JsonResponse
    {
        $tables = Table::with(['orders' => function ($q) {
            $q->whereIn('status', ['pending', 'preparing', 'ready'])->latest();
        }])->get();

        return response()->json([
            'success' => true,
            'data' => $tables,
        ]);
    }

    public function updateStatus(Request $request, Table $table): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:available,occupied,reserved',
        ]);

        $table->update(['status' => $validated['status']]);

        return response()->json([
            'success' => true,
            'message' => 'Table status updated successfully',
            'data' => $table,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'table_number' => 'required|string|max:50',
            'capacity' => 'required|integer|min:1',
            'location' => 'required|string|max:100',
            'status' => 'nullable|in:available,occupied,reserved',
        ]);

        $table = Table::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Table created successfully',
            'data' => $table,
        ], 201);
    }
}
