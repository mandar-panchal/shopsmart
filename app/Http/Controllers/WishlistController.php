<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class WishlistController extends Controller
{
    public function index()
    {
        $customerId = Auth::guard('customer')->id();
        
        $wishlistItems = Wishlist::with('product')
            ->where('customer_id', $customerId)
            ->latest()
            ->get();

        return view('customer.wishlist.index', compact('wishlistItems'));
    }

    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $customerId = Auth::guard('customer')->id();
            
            // Check if already in wishlist
            $exists = Wishlist::where('customer_id', $customerId)
                ->where('product_id', $request->product_id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product already in wishlist'
                ], 400);
            }

            Wishlist::create([
                'customer_id' => $customerId,
                'product_id' => $request->product_id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Product added to wishlist'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add to wishlist'
            ], 500);
        }
    }

    public function remove($id)
    {
        try {
            $customerId = Auth::guard('customer')->id();
            
            Wishlist::where('customer_id', $customerId)
                ->where('product_id', $id)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Product removed from wishlist'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove from wishlist'
            ], 500);
        }
    }
}