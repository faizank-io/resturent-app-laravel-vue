<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index(): JsonResponse
    {
        $staffs = Staff::latest()->get();

        return response()->json([
            'success' => true,
            'data' => $staffs,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:staffs,email',
            'phone' => 'nullable|string|max:50',
            'role' => 'required|in:Manager,Head Chef,Chef,Waiter,Cashier,Bartender',
            'status' => 'nullable|in:on_shift,active,off_duty',
            'shift' => 'nullable|in:Morning,Evening,Night,Full-Day',
            'hourly_rate' => 'nullable|numeric|min:0',
            'avatar' => 'nullable|string',
        ]);

        if (empty($validated['avatar'])) {
            $validated['avatar'] = 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=300&q=80';
        }

        $staff = Staff::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Staff member added successfully',
            'data' => $staff,
        ], 201);
    }

    public function updateStatus(Request $request, Staff $staff): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:on_shift,active,off_duty',
        ]);

        $staff->update(['status' => $validated['status']]);

        return response()->json([
            'success' => true,
            'message' => 'Staff status updated successfully',
            'data' => $staff,
        ]);
    }

    public function destroy(Staff $staff): JsonResponse
    {
        $staff->delete();

        return response()->json([
            'success' => true,
            'message' => 'Staff member removed successfully',
        ]);
    }
}
