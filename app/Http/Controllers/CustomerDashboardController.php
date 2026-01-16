<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Cart;
use App\Models\Wishlist;
use App\Models\ProductView;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomerDashboardController extends Controller
{
    public function index()
    {
        $customerId = Auth::guard('customer')->id();

        // Stats
        $totalOrders = Order::where('customer_id', $customerId)->count();
        $totalSpent = Order::where('customer_id', $customerId)
            ->where('payment_status', 'paid')
            ->sum('final_amount');
        $cartCount = Cart::where('customer_id', $customerId)->count();
        $wishlistCount = Wishlist::where('customer_id', $customerId)->count();

        // Recent orders
        $recentOrders = Order::where('customer_id', $customerId)
            ->with('items.product')
            ->latest()
            ->take(5)
            ->get();

        // Order status distribution
        $ordersByStatus = Order::where('customer_id', $customerId)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return view('customer.dashboard', compact(
            'totalOrders',
            'totalSpent',
            'cartCount',
            'wishlistCount',
            'recentOrders',
            'ordersByStatus'
        ));
    }

    public function getAnalytics()
    {
        try {
            $customerId = Auth::guard('customer')->id();

            // Monthly spending chart
            $monthlySpending = Order::where('customer_id', $customerId)
                ->where('payment_status', 'paid')
                ->whereYear('created_at', date('Y'))
                ->select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('SUM(final_amount) as total')
                )
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->pluck('total', 'month')
                ->toArray();

            // Fill missing months with 0
            $monthlyData = [];
            for ($i = 1; $i <= 12; $i++) {
                $monthlyData[] = [
                    'month' => date('M', mktime(0, 0, 0, $i, 1)),
                    'amount' => $monthlySpending[$i] ?? 0
                ];
            }

            // Order status breakdown
            $statusBreakdown = Order::where('customer_id', $customerId)
                ->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get();

            // Top purchased products
            $topProducts = DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->where('orders.customer_id', $customerId)
                ->select(
                    'products.name',
                    DB::raw('SUM(order_items.quantity) as total_quantity'),
                    DB::raw('SUM(order_items.subtotal) as total_spent')
                )
                ->groupBy('products.id', 'products.name')
                ->orderByDesc('total_quantity')
                ->take(5)
                ->get();

            // Recent activity
            $recentActivity = ProductView::where('customer_id', $customerId)
                ->with('product')
                ->latest()
                ->take(10)
                ->get()
                ->map(function($view) {
                    return [
                        'product_name' => $view->product->name,
                        'viewed_at' => $view->created_at->diffForHumans()
                    ];
                });

            return response()->json([
                'success' => true,
                'monthly_spending' => $monthlyData,
                'status_breakdown' => $statusBreakdown,
                'top_products' => $topProducts,
                'recent_activity' => $recentActivity
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch analytics'
            ], 500);
        }
    }
}