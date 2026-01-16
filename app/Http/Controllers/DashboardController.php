<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Cart;
use App\Models\Wishlist;
use App\Models\ProductView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    // Dashboard - Analytics
    public function dashboardAnalytics()
    {
        $pageConfigs = ['pageHeader' => false];
        return view('/content/dashboard/dashboard-analytics', ['pageConfigs' => $pageConfigs]);
    }

    // Dashboard - Ecommerce
    public function dashboardEcommerce()
    {
        $pageConfigs = ['pageHeader' => false];

        // Get today's date range
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth();

        // ============ KEY METRICS ============
        
        // Total Customers
        $totalCustomers = Customer::count();
        $newCustomersToday = Customer::whereDate('created_at', $today)->count();
        $newCustomersThisMonth = Customer::whereBetween('created_at', [$thisMonth, now()])->count();
        
        // Total Orders
        $totalOrders = Order::count();
        $ordersToday = Order::whereDate('created_at', $today)->count();
        $ordersThisMonth = Order::whereBetween('created_at', [$thisMonth, now()])->count();
        
        // Revenue
        $totalRevenue = Order::where('payment_status', 'paid')->sum('final_amount');
        $revenueToday = Order::where('payment_status', 'paid')
            ->whereDate('created_at', $today)
            ->sum('final_amount');
        $revenueThisMonth = Order::where('payment_status', 'paid')
            ->whereBetween('created_at', [$thisMonth, now()])
            ->sum('final_amount');
        
        // Average Order Value
        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
        
        // Total Products
        $totalProducts = Product::count();
        $activeProducts = Product::where('is_active', 1)->count();
        $lowStockProducts = Product::where('stock', '<', 10)->where('stock', '>', 0)->count();
        $outOfStockProducts = Product::where('stock', 0)->count();

        // ============ ORDER STATUS BREAKDOWN ============
        $orderStatusBreakdown = Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        // ============ PAYMENT STATUS ============
        $paymentStatusBreakdown = Order::select('payment_status', DB::raw('count(*) as count'))
            ->groupBy('payment_status')
            ->get();

        // ============ MONTHLY SALES (Last 12 Months) ============
        $monthlySales = Order::select(
                DB::raw('DATE_FORMAT(created_at, "%b %Y") as month'),
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(final_amount) as total_revenue')
            )
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->where('payment_status', 'paid')
            ->groupBy('month')
            ->orderBy(DB::raw('MIN(created_at)'))
            ->get();

        // ============ TOP SELLING PRODUCTS ============
        $topProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.payment_status', 'paid')
            ->select(
                'products.id',
                'products.name',
                'products.sku',
                'products.price',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.subtotal) as total_revenue')
            )
            ->groupBy('products.id', 'products.name', 'products.sku', 'products.price')
            ->orderByDesc('total_sold')
            ->limit(10)
            ->get();

        // ============ RECENT CUSTOMERS ============
        $recentCustomers = Customer::with(['otps' => function($query) {
                $query->latest()->limit(1);
            }])
            ->withCount(['orders' => function($query) {
                $query->where('payment_status', 'paid');
            }])
            ->withSum(['orders' => function($query) {
                $query->where('payment_status', 'paid');
            }], 'final_amount')
            ->latest()
            ->limit(10)
            ->get();

        // ============ CUSTOMER ACTIVITY ============
        $customerActivity = [
            'active_carts' => Cart::distinct('customer_id')->count('customer_id'),
            'wishlist_items' => Wishlist::count(),
            'product_views_today' => ProductView::whereDate('created_at', $today)->count(),
        ];

        // ============ TOP CUSTOMERS BY SPENDING ============
        $topCustomers = Customer::select('customers.*')
            ->join('orders', 'customers.id', '=', 'orders.customer_id')
            ->where('orders.payment_status', 'paid')
            ->groupBy('customers.id', 'customers.name', 'customers.email', 'customers.phone', 'customers.created_at', 'customers.updated_at', 'customers.email_verified_at', 'customers.remember_token')
            ->selectRaw('customers.*, COUNT(orders.id) as total_orders, SUM(orders.final_amount) as total_spent')
            ->orderByDesc('total_spent')
            ->limit(10)
            ->get();

        // ============ DAILY SALES (Last 30 Days) ============
        $dailySales = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as orders'),
                DB::raw('SUM(final_amount) as revenue')
            )
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->where('payment_status', 'paid')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // ============ GROWTH METRICS ============
        $lastMonthOrders = Order::whereBetween('created_at', [
            Carbon::now()->subMonth()->startOfMonth(),
            Carbon::now()->subMonth()->endOfMonth()
        ])->count();

        $lastMonthRevenue = Order::where('payment_status', 'paid')
            ->whereBetween('created_at', [
                Carbon::now()->subMonth()->startOfMonth(),
                Carbon::now()->subMonth()->endOfMonth()
            ])
            ->sum('final_amount');

        $orderGrowth = $lastMonthOrders > 0 
            ? (($ordersThisMonth - $lastMonthOrders) / $lastMonthOrders) * 100 
            : 0;

        $revenueGrowth = $lastMonthRevenue > 0 
            ? (($revenueThisMonth - $lastMonthRevenue) / $lastMonthRevenue) * 100 
            : 0;

        // ============ CUSTOMER REGISTRATION TREND (Last 12 Months) ============
        $customerRegistrations = Customer::select(
                DB::raw('DATE_FORMAT(created_at, "%b %Y") as month'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('month')
            ->orderBy(DB::raw('MIN(created_at)'))
            ->get();

        return view('/content/dashboard/dashboard-ecommerce', [
            'pageConfigs' => $pageConfigs,
            
            // Metrics
            'totalCustomers' => $totalCustomers,
            'newCustomersToday' => $newCustomersToday,
            'newCustomersThisMonth' => $newCustomersThisMonth,
            'totalOrders' => $totalOrders,
            'ordersToday' => $ordersToday,
            'ordersThisMonth' => $ordersThisMonth,
            'totalRevenue' => $totalRevenue,
            'revenueToday' => $revenueToday,
            'revenueThisMonth' => $revenueThisMonth,
            'avgOrderValue' => $avgOrderValue,
            'totalProducts' => $totalProducts,
            'activeProducts' => $activeProducts,
            'lowStockProducts' => $lowStockProducts,
            'outOfStockProducts' => $outOfStockProducts,
            
            // Analytics Data
            'orderStatusBreakdown' => $orderStatusBreakdown,
            'paymentStatusBreakdown' => $paymentStatusBreakdown,
            'monthlySales' => $monthlySales,
            'topProducts' => $topProducts,
            'recentCustomers' => $recentCustomers,
            'customerActivity' => $customerActivity,
            'topCustomers' => $topCustomers,
            'dailySales' => $dailySales,
            'customerRegistrations' => $customerRegistrations,
            
            // Growth Metrics
            'orderGrowth' => $orderGrowth,
            'revenueGrowth' => $revenueGrowth,
        ]);
    }

    // API endpoint for charts data
    public function getChartData(Request $request)
    {
        $type = $request->get('type');

        switch ($type) {
            case 'monthly_sales':
                $data = Order::select(
                        DB::raw('MONTH(created_at) as month'),
                        DB::raw('SUM(final_amount) as revenue')
                    )
                    ->whereYear('created_at', date('Y'))
                    ->where('payment_status', 'paid')
                    ->groupBy('month')
                    ->orderBy('month')
                    ->get();
                break;

            case 'order_status':
                $data = Order::select('status', DB::raw('count(*) as count'))
                    ->groupBy('status')
                    ->get();
                break;

            case 'top_products':
                $data = DB::table('order_items')
                    ->join('products', 'order_items.product_id', '=', 'products.id')
                    ->select(
                        'products.name',
                        DB::raw('SUM(order_items.quantity) as total_sold')
                    )
                    ->groupBy('products.id', 'products.name')
                    ->orderByDesc('total_sold')
                    ->limit(5)
                    ->get();
                break;

            default:
                $data = [];
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}