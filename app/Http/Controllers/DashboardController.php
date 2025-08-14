<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Invoice;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalCategories = Category::count();
        $totalBrands = Category::count();
        $totalProducts = Product::count();
        $totalOrders = Invoice::count();

        $todayOrders = Invoice::whereDate('created_at', today())->count();

        return response()->json([
            'status' => 'success',
            'message' => [
                'totalCategories' => $totalCategories,
                'totalBrands' => $totalBrands,
                'totalProducts' => $totalProducts,
                'totalOrders' => $totalOrders,
                'todayOrders' => $todayOrders
            ],
        ], 200);
    }
}
