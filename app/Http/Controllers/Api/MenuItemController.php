<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MenuItemController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = MenuItem::with('category');

        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('search') && !empty($request->search)) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $items = $query->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $items,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|string',
            'is_available' => 'boolean',
            'prep_time_minutes' => 'integer|min:1',
        ]);

        $menuItem = MenuItem::create($validated);
        $menuItem->load('category');

        return response()->json([
            'success' => true,
            'message' => 'Menu item created successfully',
            'data' => $menuItem,
        ], 201);
    }

    public function show(MenuItem $menuItem): JsonResponse
    {
        $menuItem->load('category');

        return response()->json([
            'success' => true,
            'data' => $menuItem,
        ]);
    }

    public function update(Request $request, MenuItem $menuItem): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => 'sometimes|required|exists:categories,id',
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
            'image' => 'nullable|string',
            'is_available' => 'sometimes|boolean',
            'prep_time_minutes' => 'sometimes|integer|min:1',
        ]);

        $menuItem->update($validated);
        $menuItem->load('category');

        return response()->json([
            'success' => true,
            'message' => 'Menu item updated successfully',
            'data' => $menuItem,
        ]);
    }

    public function toggleAvailability(MenuItem $menuItem): JsonResponse
    {
        $menuItem->update(['is_available' => !$menuItem->is_available]);

        return response()->json([
            'success' => true,
            'message' => 'Availability status updated',
            'data' => $menuItem,
        ]);
    }

    public function destroy(MenuItem $menuItem): JsonResponse
    {
        $menuItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Menu item deleted successfully',
        ]);
    }
}
