<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TaskProductController extends Controller
{
    /**
     * Display the product listing page
     * Admin only access
     */
    public function index()
    {
        // Check if user is admin
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized access. Admin only.');
        }
        
        return view('products.index');
    }

    /**
     * Fetch products data for card-based display with pagination
     * Admin only access
     */
    public function fetch(Request $request)
    {
        try {
            // Check admin access
            if (!Auth::user()->hasRole('admin')) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
            
            $perPage = $request->input('per_page', 12);
            $search = $request->input('search', '');
            $status = $request->input('status', 'all');
            $sortBy = $request->input('sort_by', 'latest');

            $query = Product::query();

            // Search filter
            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('sku', 'LIKE', "%{$search}%")
                      ->orWhere('description', 'LIKE', "%{$search}%");
                });
            }

            // Status filter
            if ($status !== 'all') {
                $query->where('is_active', $status === 'active' ? 1 : 0);
            }

            // Sorting
            switch ($sortBy) {
                case 'latest':
                    $query->latest();
                    break;
                case 'oldest':
                    $query->oldest();
                    break;
                case 'name_asc':
                    $query->orderBy('name', 'asc');
                    break;
                case 'name_desc':
                    $query->orderBy('name', 'desc');
                    break;
                case 'price_low':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_high':
                    $query->orderBy('price', 'desc');
                    break;
                case 'stock_low':
                    $query->orderBy('stock', 'asc');
                    break;
                case 'stock_high':
                    $query->orderBy('stock', 'desc');
                    break;
            }

            $products = $query->paginate($perPage);

            // Add image URLs to products
            $products->getCollection()->transform(function ($product) {
                if ($product->image_path && Storage::disk('public')->exists($product->image_path)) {
                    $product->image_url = Storage::url($product->image_path);
                } else {
                    $product->image_url = null;
                }
                return $product;
            });

            return response()->json([
                'success' => true,
                'products' => $products->items(),
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                    'from' => $products->firstItem(),
                    'to' => $products->lastItem()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Product Fetch Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch products'
            ], 500);
        }
    }

    /**
     * Store a new product
     * Admin only access
     */
    public function store(Request $request)
    {
        try {
            // Check admin access
            if (!Auth::user()->hasRole('admin')) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            // Validation rules
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|min:3|max:255',
                'sku' => 'required|string|max:100|unique:products,sku',
                'price' => 'required|numeric|min:0.01|max:99999999.99',
                'stock' => 'required|integer|min:0|max:999999',
                'is_active' => 'nullable|boolean',
                'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
                'description' => 'nullable|string|max:1000'
            ], [
                'name.required' => 'Product name is required',
                'name.min' => 'Product name must be at least 3 characters',
                'sku.required' => 'SKU is required',
                'sku.unique' => 'This SKU already exists',
                'price.required' => 'Price is required',
                'price.min' => 'Price must be greater than 0',
                'stock.required' => 'Stock quantity is required',
                'stock.min' => 'Stock cannot be negative',
                'image.required' => 'Product image is required',
                'image.image' => 'File must be an image',
                'image.mimes' => 'Image must be jpg, jpeg, png, or webp',
                'image.max' => 'Image size cannot exceed 2MB'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $data = [
                'name' => $request->name,
                'sku' => strtoupper($request->sku),
                'price' => $request->price,
                'stock' => $request->stock,
                'is_active' => $request->has('is_active') ? filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN) : false,
                'description' => $request->description
            ];

            // Handle image upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = 'product_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('products', $imageName, 'public');
                
                if (!$imagePath) {
                    Log::error('Failed to upload product image');
                    return response()->json(['message' => 'Failed to upload image'], 500);
                }
                
                $data['image_path'] = $imagePath;
                Log::info('Product image uploaded: ' . $imagePath);
            }

            // Create product
            $product = Product::create($data);

            Log::info('Product created successfully', ['product_id' => $product->id]);

            return response()->json([
                'message' => 'Product created successfully',
                'product' => $product
            ], 201);

        } catch (\Exception $e) {
            Log::error('Product Store Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get product details for editing
     * Admin only access
     */
    public function edit($id)
    {
        try {
            // Check admin access
            if (!Auth::user()->hasRole('admin')) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
            
            $product = Product::findOrFail($id);
            
            // Add image URL if exists
            if ($product->image_path && Storage::disk('public')->exists($product->image_path)) {
                $product->image_url = Storage::url($product->image_path);
            }

            return response()->json($product);
            
        } catch (\Exception $e) {
            Log::error('Product Edit Error: ' . $e->getMessage());
            return response()->json(['message' => 'Product not found'], 404);
        }
    }

    /**
     * Update an existing product
     * Admin only access
     */
    public function update(Request $request)
    {
        try {
            // Check admin access
            if (!Auth::user()->hasRole('admin')) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            // Validation rules
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|exists:products,id',
                'name' => 'required|string|min:3|max:255',
                'sku' => 'required|string|max:100|unique:products,sku,' . $request->product_id,
                'price' => 'required|numeric|min:0.01|max:99999999.99',
                'stock' => 'required|integer|min:0|max:999999',
                'is_active' => 'nullable|boolean',
                'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
                'description' => 'nullable|string|max:1000'
            ], [
                'name.required' => 'Product name is required',
                'name.min' => 'Product name must be at least 3 characters',
                'sku.required' => 'SKU is required',
                'sku.unique' => 'This SKU already exists',
                'price.required' => 'Price is required',
                'price.min' => 'Price must be greater than 0',
                'stock.required' => 'Stock quantity is required',
                'stock.min' => 'Stock cannot be negative',
                'image.image' => 'File must be an image',
                'image.mimes' => 'Image must be jpg, jpeg, png, or webp',
                'image.max' => 'Image size cannot exceed 2MB'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            
            $product = Product::findOrFail($request->product_id);
            
            $data = [
                'name' => $request->name,
                'sku' => strtoupper($request->sku),
                'price' => $request->price,
                'stock' => $request->stock,
                'is_active' => $request->has('is_active') ? filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN) : false,
                'description' => $request->description
            ];

            // Handle image upload if new image provided
            if ($request->hasFile('image')) {
                // Delete old image
                if ($product->image_path && Storage::disk('public')->exists($product->image_path)) {
                    Storage::disk('public')->delete($product->image_path);
                }

                // Upload new image
                $image = $request->file('image');
                $imageName = 'product_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('products', $imageName, 'public');
                
                if ($imagePath) {
                    $data['image_path'] = $imagePath;
                    Log::info('Product image updated: ' . $imagePath);
                }
            }

            // Update product
            $product->update($data);

            Log::info('Product updated successfully', ['product_id' => $product->id]);

            return response()->json([
                'message' => 'Product updated successfully',
                'product' => $product
            ]);

        } catch (\Exception $e) {
            Log::error('Product Update Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get product details for viewing
     * Admin only access
     */
    public function view($id)
    {
        try {
            // Check admin access
            if (!Auth::user()->hasRole('admin')) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
            
            $product = Product::findOrFail($id);
            
            // Add image URL if exists
            if ($product->image_path && Storage::disk('public')->exists($product->image_path)) {
                $product->image_url = Storage::url($product->image_path);
            }

            return response()->json($product);
            
        } catch (\Exception $e) {
            Log::error('Product View Error: ' . $e->getMessage());
            return response()->json(['message' => 'Product not found'], 404);
        }
    }

    /**
     * Soft delete a product
     * Admin only access
     */
    public function destroy($id)
    {
        try {
            // Check admin access
            if (!Auth::user()->hasRole('admin')) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
            
            $product = Product::findOrFail($id);
            
            // Soft delete (image remains in storage)
            $product->delete();

            Log::info('Product soft deleted', ['product_id' => $id]);

            return response()->json(['message' => 'Product deleted successfully']);

        } catch (\Exception $e) {
            Log::error('Product Delete Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    

    /**
     * Toggle product active status via AJAX
     * Admin only access
     */
    public function toggleStatus(Request $request, $id)
    {
        try {
            // Check admin access
            if (!Auth::user()->hasRole('admin')) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
            
            $product = Product::findOrFail($id);
            $product->is_active = !$product->is_active;
            $product->save();

            Log::info('Product status toggled', [
                'product_id' => $id, 
                'new_status' => $product->is_active
            ]);

            return response()->json([
                'message' => 'Status updated successfully',
                'is_active' => $product->is_active
            ]);

        } catch (\Exception $e) {
            Log::error('Product Status Toggle Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }
}