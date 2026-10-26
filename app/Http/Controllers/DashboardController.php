<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Purchase;


class DashboardController extends Controller
{
    public function index()
    {
        $user = request()->user();

        $userRole = $user?->role?->name;

        // Admin dashboard
        if ($userRole === 'Admin') {
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
            )->where('quantity', '>', 0)->count();

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

        // Sales User dashboard
        if ($userRole === 'Sales User') {
            $totalCustomers = Customer::count();

            $totalSales = Invoice::sum('total_amount');

            $todaySales = Invoice::whereDate(
                'invoice_date',
                today()
            )->sum('total_amount');

            $outstandingPayments = Invoice::sum('balance_amount');

            $recentInvoices = Invoice::with('customer')
                ->latest()
                ->take(5)
                ->get();

            return view('dashboard', compact(
                'totalCustomers',
                'totalSales',
                'todaySales',
                'outstandingPayments',
                'recentInvoices'
            ));
        }

        // Inventory User dashboard
        if ($userRole === 'Inventory User') {
            $totalProducts = Product::count();
            $totalSuppliers = Supplier::count();

            $totalPurchases = Purchase::where(
                'status',
                'received'
            )->sum('total_amount');

            $lowStockProducts = Product::whereColumn(
                'quantity',
                '<=',
                'reorder_level'
            )->where('quantity', '>', 0)->count();

            $lowStockList = Product::whereColumn(
                'quantity',
                '<=',
                'reorder_level'
            )
                ->orderBy('quantity')
                ->take(5)
                ->get();

            return view('dashboard', compact(
                'totalProducts',
                'totalSuppliers',
                'totalPurchases',
                'lowStockProducts',
                'lowStockList'
            ));
        }

        abort(403, 'Your account does not have a valid role.');
    }
}
