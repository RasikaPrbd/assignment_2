<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $salesQuery = Invoice::query();
        $purchaseQuery = Purchase::query();

        if ($startDate) {
            $salesQuery->whereDate('invoice_date', '>=', $startDate);
            $purchaseQuery->whereDate('purchase_date', '>=', $startDate);
        }

        if ($endDate) {
            $salesQuery->whereDate('invoice_date', '<=', $endDate);
            $purchaseQuery->whereDate('purchase_date', '<=', $endDate);
        }

        $salesSummary = [
            'total_sales' => (clone $salesQuery)
                ->whereIn('status', ['paid', 'partially_paid'])
                ->sum('total_amount'),

            'invoice_count' => (clone $salesQuery)->count(),

            'paid_amount' => (clone $salesQuery)->sum('paid_amount'),

            'outstanding_amount' => (clone $salesQuery)
                ->sum('balance_amount'),
        ];

        $purchaseSummary = [
            'total_purchases' => (clone $purchaseQuery)
                ->where('status', 'received')
                ->sum('total_amount'),

            'purchase_count' => (clone $purchaseQuery)->count(),
        ];

        $salesCostQuery = DB::table('invoice_items')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->join('products', 'invoice_items.product_id', '=', 'products.id');

        if ($startDate) {
            $salesCostQuery->whereDate('invoices.invoice_date', '>=', $startDate);
        }

        if ($endDate) {
            $salesCostQuery->whereDate('invoices.invoice_date', '<=', $endDate);
        }

        $salesCost = $salesCostQuery
            ->whereIn('invoices.status', ['paid', 'partially_paid'])
            ->sum(DB::raw('invoice_items.quantity * products.cost_price'));

        $salesRevenue = $salesSummary['total_sales'];

        $grossProfit = $salesRevenue - $salesCost;

        return view('reports.index', compact(
            'startDate',
            'endDate',
            'salesSummary',
            'purchaseSummary',
            'salesRevenue',
            'salesCost',
            'grossProfit'
        ));
    }
}
