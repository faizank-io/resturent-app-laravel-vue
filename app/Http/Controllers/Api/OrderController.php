<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\Table;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Order::with(['table', 'items.menuItem']);

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('order_type') && $request->order_type) {
            $query->where('order_type', $request->order_type);
        }

        $orders = $query->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $orders,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_name' => 'nullable|string|max:255',
            'table_id' => 'nullable|exists:restaurant_tables,id',
            'order_type' => 'required|in:dine_in,takeaway,delivery',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.menu_item_id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $orderNumber = 'ORD-' . strtoupper(Str::random(6));
        $subtotal = 0;

        $order = Order::create([
            'order_number' => $orderNumber,
            'table_id' => $validated['table_id'] ?? null,
            'customer_name' => $validated['customer_name'] ?? 'Walk-in Guest',
            'order_type' => $validated['order_type'],
            'status' => 'pending',
            'notes' => $validated['notes'] ?? null,
            'subtotal' => 0,
            'delivery_fee' => 0,
            'tax_amount' => 0,
            'total_amount' => 0,
        ]);

        foreach ($validated['items'] as $item) {
            $menuItem = MenuItem::findOrFail($item['menu_item_id']);
            $itemSubtotal = $menuItem->price * $item['quantity'];
            $subtotal += $itemSubtotal;

            $order->items()->create([
                'menu_item_id' => $menuItem->id,
                'quantity' => $item['quantity'],
                'unit_price' => $menuItem->price,
                'subtotal' => $itemSubtotal,
            ]);
        }

        $deliveryFee = ($validated['order_type'] === 'delivery') ? 4.50 : 0.00;
        $taxAmount = round($subtotal * 0.05, 2); // 5% tax rate
        $totalAmount = $subtotal + $deliveryFee + $taxAmount;

        $order->update([
            'subtotal' => $subtotal,
            'delivery_fee' => $deliveryFee,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
        ]);

        // If table assigned, set table status to occupied
        if ($validated['table_id'] && $validated['order_type'] === 'dine_in') {
            Table::where('id', $validated['table_id'])->update(['status' => 'occupied']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Order created successfully',
            'data' => $order->load(['table', 'items.menuItem']),
        ], 201);
    }

    public function updateStatus(Request $request, Order $order): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,preparing,ready,completed,cancelled',
        ]);

        $order->update(['status' => $validated['status']]);

        // If completed or cancelled and has a table, free up the table
        if (in_array($validated['status'], ['completed', 'cancelled']) && $order->table_id) {
            Table::where('id', $order->table_id)->update(['status' => 'available']);
        }

        return response()->json([
            'success' => true,
            'message' => "Order status updated to {$validated['status']}",
            'data' => $order->load(['table', 'items.menuItem']),
        ]);
    }
}
