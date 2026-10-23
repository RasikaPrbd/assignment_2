<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class StockReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'supplier']);

        // Search by product name or code
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by stock status
        if ($request->stock_status === 'low') {
            $query->whereColumn('quantity', '<=', 'reorder_level')
                ->where('quantity', '>', 0);
        } elseif ($request->stock_status === 'out') {
            $query->where('quantity', '<=', 0);
        } elseif ($request->stock_status === 'available') {
            $query->where('quantity', '>', 0)
                ->whereColumn('quantity', '>', 'reorder_level');
        }

        $products = $query->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        // Overall stock summary
        $totalProducts = Product::count();

        $totalStockUnits = Product::sum('quantity');

        $lowStockProducts = Product::whereColumn(
            'quantity',
            '<=',
            'reorder_level'
        )->where('quantity', '>', 0)->count();

        $outOfStockProducts = Product::where('quantity', '<=', 0)->count();

        $totalStockValue = (float) Product::selectRaw(
            'COALESCE(SUM(cost_price * quantity), 0) as stock_value'
        )->value('stock_value');

        $categories = Category::orderBy('name')->get();

        return view('reports.stock', compact(
            'products',
            'categories',
            'totalProducts',
            'totalStockUnits',
            'lowStockProducts',
            'outOfStockProducts',
            'totalStockValue'
        ));
    }
}
