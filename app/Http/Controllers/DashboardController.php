<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Payment;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProducts = Product::count();
        $totalCustomers = Customer::count();
        $totalSuppliers = Supplier::count();

        $totalSales = Invoice::sum('total_amount');

        $todaySales = Invoice::whereDate(
            'invoice_date',
            today()
        )->sum('total_amount');

        $outstandingPayments = Invoice::sum('balance_amount');

        $lowStockProducts = Product::whereColumn(
            'quantity',
            '<=',
            'reorder_level'
        )->count();

        $lowStockList = Product::whereColumn(
            'quantity',
            '<=',
            'reorder_level'
        )
            ->orderBy('quantity')
            ->take(5)
            ->get();

        $recentInvoices = Invoice::with('customer')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalProducts',
            'totalCustomers',
            'totalSuppliers',
            'totalSales',
            'todaySales',
            'outstandingPayments',
            'lowStockProducts',
            'lowStockList',
            'recentInvoices'
        ));
    }
}
