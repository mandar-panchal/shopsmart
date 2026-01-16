<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Cart;
use App\Models\Wishlist;
use App\Models\ProductView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerProductController extends Controller
{
    public function index()
    {
        return view('customer.products.index');
    }

    public function fetchProducts(Request $request)
    {
        try {
            $query = Product::where('is_active', 1)
                ->where('stock', '>', 0);

            // Search
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('sku', 'LIKE', "%{$search}%")
                      ->orWhere('description', 'LIKE', "%{$search}%");
                });
            }

            // Price range filter
            if ($request->filled('min_price')) {
                $query->where('price', '>=', $request->min_price);
            }
            if ($request->filled('max_price')) {
                $query->where('price', '<=', $request->max_price);
            }

            // Sorting
            switch ($request->sort_by) {
                case 'price_low':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_high':
                    $query->orderBy('price', 'desc');
                    break;
                case 'name_asc':
                    $query->orderBy('name', 'asc');
                    break;
                case 'name_desc':
                    $query->orderBy('name', 'desc');
                    break;
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
            }

            $perPage = $request->per_page ?? 12;
            $products = $query->paginate($perPage);

            // Get wishlist and cart items for authenticated user
            $wishlistIds = [];
            $cartItems = [];
            
            if (Auth::guard('customer')->check()) {
                $customerId = Auth::guard('customer')->id();
                $wishlistIds = Wishlist::where('customer_id', $customerId)
                    ->pluck('product_id')
                    ->toArray();
                
                $cartItems = Cart::where('customer_id', $customerId)
                    ->pluck('quantity', 'product_id')
                    ->toArray();
            }

            $productsData = $products->map(function ($product) use ($wishlistIds, $cartItems) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'price' => $product->price,
                    'formatted_price' => $product->formatted_price,
                    'stock' => $product->stock,
                    'description' => $product->description,
                    'image_url' => $product->image_path ? asset('storage/' . $product->image_path) : null,
                    'in_wishlist' => in_array($product->id, $wishlistIds),
                    'cart_quantity' => $cartItems[$product->id] ?? 0,
                ];
            });

            return response()->json([
                'success' => true,
                'products' => $productsData,
                'pagination' => [
                    'total' => $products->total(),
                    'per_page' => $products->perPage(),
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'from' => $products->firstItem(),
                    'to' => $products->lastItem(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch products'
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $product = Product::where('is_active', 1)->findOrFail($id);

            // Track product view
            ProductView::create([
                'product_id' => $product->id,
                'customer_id' => Auth::guard('customer')->id(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            $inWishlist = false;
            $cartQuantity = 0;

            if (Auth::guard('customer')->check()) {
                $customerId = Auth::guard('customer')->id();
                $inWishlist = Wishlist::where('customer_id', $customerId)
                    ->where('product_id', $product->id)
                    ->exists();
                
                $cartItem = Cart::where('customer_id', $customerId)
                    ->where('product_id', $product->id)
                    ->first();
                
                $cartQuantity = $cartItem ? $cartItem->quantity : 0;
            }

            return response()->json([
                'success' => true,
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'price' => $product->price,
                    'formatted_price' => $product->formatted_price,
                    'stock' => $product->stock,
                    'description' => $product->description,
                    'image_url' => $product->image_path ? asset('storage/' . $product->image_path) : null,
                    'in_wishlist' => $inWishlist,
                    'cart_quantity' => $cartQuantity,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }
    }
}