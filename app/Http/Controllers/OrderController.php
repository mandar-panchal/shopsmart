<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function index()
    {
        return view('customer.orders.index');
    }

    public function checkout()
    {
        $customerId = Auth::guard('customer')->id();
        
        // Check if cart has items
        $cartItems = Cart::where('customer_id', $customerId)->count();
        
        if ($cartItems === 0) {
            return redirect()->route('customer.cart')->with('error', 'Your cart is empty');
        }
        
        return view('customer.checkout.index');
    }

    public function fetchOrders(Request $request)
    {
        try {
            $customerId = Auth::guard('customer')->id();
            
            $query = Order::with('items.product')
                ->where('customer_id', $customerId);

            // Filter by status
            if ($request->filled('status') && $request->status != 'all') {
                $query->where('status', $request->status);
            }

            // Search
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where('order_number', 'LIKE', "%{$search}%");
            }

            // Sorting
            $sortBy = $request->sort_by ?? 'latest';
            switch ($sortBy) {
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'amount_high':
                    $query->orderBy('final_amount', 'desc');
                    break;
                case 'amount_low':
                    $query->orderBy('final_amount', 'asc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
            }

            $perPage = $request->per_page ?? 10;
            $orders = $query->paginate($perPage);

            $ordersData = $orders->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'total_amount' => $order->total_amount,
                    'final_amount' => $order->final_amount,
                    'status' => $order->status,
                    'payment_status' => $order->payment_status,
                    'items_count' => $order->items->count(),
                    'created_at' => $order->created_at->format('M d, Y h:i A'),
                    'status_badge' => $order->status_badge,
                    'payment_status_badge' => $order->payment_status_badge,
                ];
            });

            return response()->json([
                'success' => true,
                'orders' => $ordersData,
                'pagination' => [
                    'total' => $orders->total(),
                    'per_page' => $orders->perPage(),
                    'current_page' => $orders->currentPage(),
                    'last_page' => $orders->lastPage(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch orders'
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $customerId = Auth::guard('customer')->id();
            
            $order = Order::with('items.product')
                ->where('customer_id', $customerId)
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'order' => $order
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shipping_address' => 'required|string',
            'billing_address' => 'nullable|string',
            'payment_method' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $customerId = Auth::guard('customer')->id();
            
            // Get cart items
            $cartItems = Cart::with('product')
                ->where('customer_id', $customerId)
                ->get();

            if ($cartItems->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your cart is empty'
                ], 400);
            }

            // Calculate totals
            $totalAmount = 0;
            foreach ($cartItems as $item) {
                if (!$item->product->is_active) {
                    return response()->json([
                        'success' => false,
                        'message' => $item->product->name . ' is no longer available'
                    ], 400);
                }

                if ($item->product->stock < $item->quantity) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Insufficient stock for ' . $item->product->name
                    ], 400);
                }

                $totalAmount += $item->subtotal;
            }

            // Create order
            $order = Order::create([
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'customer_id' => $customerId,
                'total_amount' => $totalAmount,
                'tax_amount' => $totalAmount * 0.18, // 18% tax
                'discount_amount' => 0,
                'final_amount' => $totalAmount * 1.18,
                'status' => 'pending',
                'payment_status' => 'pending',
                'payment_method' => $request->payment_method,
                'shipping_address' => $request->shipping_address,
                'billing_address' => $request->billing_address ?? $request->shipping_address,
                'notes' => $request->notes,
            ]);

            // Create order items and update stock
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'product_sku' => $item->product->sku,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->product->price,
                    'subtotal' => $item->subtotal,
                ]);

                // Update product stock
                $item->product->decrement('stock', $item->quantity);
            }

            // Clear cart
            Cart::where('customer_id', $customerId)->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully',
                'order' => $order
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function cancel($id)
    {
        try {
            DB::beginTransaction();

            $customerId = Auth::guard('customer')->id();
            
            $order = Order::where('customer_id', $customerId)
                ->findOrFail($id);

            if (!in_array($order->status, ['pending', 'processing'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'This order cannot be cancelled'
                ], 400);
            }

            // Restore stock
            foreach ($order->items as $item) {
                $item->product->increment('stock', $item->quantity);
            }

            $order->update([
                'status' => 'cancelled',
                'cancelled_at' => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order cancelled successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel order'
            ], 500);
        }
    }
}