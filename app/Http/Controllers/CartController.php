<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    public function index()
    {
        $customerId = Auth::guard('customer')->id();
        
        $cartItems = Cart::with('product')
            ->where('customer_id', $customerId)
            ->get();

        $total = $cartItems->sum('subtotal');

        return view('customer.cart.index', compact('cartItems', 'total'));
    }

    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $customerId = Auth::guard('customer')->id();
            $product = Product::findOrFail($request->product_id);

            // Check if product is active and in stock
            if (!$product->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'This product is not available'
                ], 400);
            }

            if ($product->stock < $request->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient stock. Only ' . $product->stock . ' items available'
                ], 400);
            }

            $cartItem = Cart::where('customer_id', $customerId)
                ->where('product_id', $request->product_id)
                ->first();

            if ($cartItem) {
                $newQuantity = $cartItem->quantity + $request->quantity;
                
                if ($product->stock < $newQuantity) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot add more. Only ' . $product->stock . ' items available'
                    ], 400);
                }

                $cartItem->update(['quantity' => $newQuantity]);
            } else {
                Cart::create([
                    'customer_id' => $customerId,
                    'product_id' => $request->product_id,
                    'quantity' => $request->quantity,
                ]);
            }

            $cartCount = Cart::where('customer_id', $customerId)->count();

            return response()->json([
                'success' => true,
                'message' => 'Product added to cart successfully',
                'cart_count' => $cartCount
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add product to cart'
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $customerId = Auth::guard('customer')->id();
            $cartItem = Cart::where('customer_id', $customerId)
                ->where('id', $id)
                ->firstOrFail();

            $product = $cartItem->product;

            if ($product->stock < $request->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient stock. Only ' . $product->stock . ' items available'
                ], 400);
            }

            $cartItem->update(['quantity' => $request->quantity]);

            return response()->json([
                'success' => true,
                'message' => 'Cart updated successfully',
                'subtotal' => $cartItem->subtotal
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update cart'
            ], 500);
        }
    }

    public function remove($id)
    {
        try {
            $customerId = Auth::guard('customer')->id();
            
            Cart::where('customer_id', $customerId)
                ->where('id', $id)
                ->delete();

            $cartCount = Cart::where('customer_id', $customerId)->count();

            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart',
                'cart_count' => $cartCount
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove item'
            ], 500);
        }
    }

    public function clear()
    {
        try {
            $customerId = Auth::guard('customer')->id();
            Cart::where('customer_id', $customerId)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Cart cleared successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cart'
            ], 500);
        }
    }

    public function getCount()
    {
        $customerId = Auth::guard('customer')->id();
        $count = Cart::where('customer_id', $customerId)->count();

        return response()->json([
            'success' => true,
            'count' => $count
        ]);
    }
}