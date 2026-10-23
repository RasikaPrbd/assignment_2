<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with('customer', 'user')
            ->latest()
            ->paginate(10);

        return view('invoices.index', compact('invoices'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $products = Product::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('invoices.create', compact('customers', 'products'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_date' => ['required', 'date'],

            'discount_type' => ['required', 'in:percentage,fixed'],
            'discount_value' => ['required', 'numeric', 'min:0'],

            'tax_rate' => ['required', 'numeric', 'min:0', 'max:100'],

            'payment_method' => [
                'required',
                'in:cash,card,bank_transfer,other',
            ],

            'paid_amount' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => [
                'required',
                'distinct',
                'exists:products,id',
            ],
            'items.*.quantity' => [
                'required',
                'integer',
                'min:1',
            ],
        ]);

        // Keep the sale details temporarily while collecting customer details.
        session()->put('sale_draft', $validated);

        return redirect()
            ->route('customers.create', ['return_to' => 'checkout'])
            ->with('success', 'Sale details saved temporarily. Add customer details next.');
    }


    public function show(Invoice $invoice)
    {
        $invoice->load('customer', 'user', 'items.product', 'payments');

        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        return redirect()->route('invoices.show', $invoice);
    }

    public function update(Request $request, Invoice $invoice)
    {
        abort(403, 'Editing invoices is disabled to protect stock and payment records.');
    }

    public function destroy(Invoice $invoice)
    {
        abort(403, 'Deleting invoices is disabled to protect stock and payment records.');
    }


    public function paymentStatus(Request $request)
    {
        $query = Invoice::with('customer', 'user')
            ->latest();

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($customerQuery) use ($search) {
                        $customerQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if (in_array($request->status, ['paid', 'partially_paid', 'unpaid'], true)) {
            $query->where('status', $request->status);
        }

        $invoices = $query->paginate(10)->withQueryString();

        $totalInvoices = Invoice::count();
        $paidInvoices = Invoice::where('status', 'paid')->count();
        $partialInvoices = Invoice::where('status', 'partially_paid')->count();
        $unpaidInvoices = Invoice::where('status', 'unpaid')->count();

        return view('invoices.payment-status', compact(
            'invoices',
            'totalInvoices',
            'paidInvoices',
            'partialInvoices',
            'unpaidInvoices'
        ));
    }
}
