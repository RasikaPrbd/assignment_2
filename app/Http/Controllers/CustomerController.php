<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::latest()->paginate(10);

        return view('customers.index', compact('customers'));
    }

    public function quickCreate()
    {
        return view('customers.quick-create');
    }

    public function create(Request $request)
    {
        if (
            $request->query('return_to') !== 'checkout' ||
            !session()->has('sale_draft')
        ) {
            return redirect()->route('customers.index');
        }

        return view('customers.create');
    }


    public function findMatches(Request $request)
    {
        $validated = $request->validate([
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
        ]);

        $phone = preg_replace(
            '/[\s\-()+]/',
            '',
            $validated['phone'] ?? ''
        );

        $email = strtolower(trim($validated['email'] ?? ''));

        if ($phone === '' && $email === '') {
            return response()->json(['customers' => []]);
        }

        $query = Customer::query();

        $query->where(function ($q) use ($phone, $email) {
            if ($phone !== '') {
                $q->whereRaw(
                    "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(phone, ' ', ''), '-', ''), '(', ''), ')', ''), '+', '') = ?",
                    [$phone]
                );
            }

            if ($email !== '') {
                $method = $phone !== '' ? 'orWhereRaw' : 'whereRaw';

                $q->{$method}(
                    'LOWER(email) = ?',
                    [$email]
                );
            }
        });

        $customers = $query
            ->where('is_active', true)
            ->limit(10)
            ->get([
                'id',
                'customer_code',
                'name',
                'phone',
                'email',
                'address',
            ]);

        return response()->json([
            'customers' => $customers,
        ]);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'existing_customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:15', 'regex:/^\+?[0-9]+$/',],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
            'return_to' => ['nullable', 'in:checkout'],
        ], [
            'phone.regex' => 'The phone number may contain only digits (0-9) and an optional + at the beginning.',
        ]);

        $returnTo = $validated['return_to'] ?? null;
        $existingCustomerId = $validated['existing_customer_id'] ?? null;

        unset($validated['return_to']);
        unset($validated['existing_customer_id']);

        // Save a customer from the Customers page.
        if ($returnTo !== 'checkout') {
            $customerCode = null;

            do {
                $customerCode = 'CUS-' . Str::upper(Str::random(8));
            } while (Customer::where('customer_code', $customerCode)->exists());

            $validated['customer_code'] = $customerCode;

            Customer::create($validated);

            return redirect()
                ->route('customers.index')
                ->with('success', 'Customer created successfully.');
        }

        // Continue the existing invoice checkout workflow.
        if (!session()->has('sale_draft')) {
            return redirect()
                ->route('customers.index')
                ->with('error', 'Your sale session has expired. Please create the invoice again.');
        }

        // The sale draft must exist before completing checkout.
        $draft = session('sale_draft');

        if (!is_array($draft) || empty($draft['items'])) {
            return redirect()->route('invoices.create')
                ->withErrors([
                    'sale' => 'Your sale session expired. Please add the products again.',
                ]);
        }

        $invoice = DB::transaction(function () use ($validated, $draft, $existingCustomerId) {
            do {
                $customerCode = 'CUS-' . Str::upper(Str::random(8));
            } while (Customer::where('customer_code', $customerCode)->exists());

            $validated['customer_code'] = $customerCode;

            if ($existingCustomerId) {
                $customer = Customer::where('is_active', true)
                    ->lockForUpdate()
                    ->find($existingCustomerId);

                if (!$customer) {
                    throw ValidationException::withMessages([
                        'existing_customer_id' =>
                        'The selected customer is inactive or unavailable.',
                    ]);
                }
            } else {
                $customer = Customer::create($validated);
            }


            $subtotal = 0;
            $lines = [];

            foreach ($draft['items'] as $item) {
                $product = Product::whereKey($item['product_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                $quantity = (int) $item['quantity'];

                if (!$product->is_active) {
                    throw ValidationException::withMessages([
                        'items' => "Product {$product->name} is inactive.",
                    ]);
                }

                if ($product->quantity < $quantity) {
                    throw ValidationException::withMessages([
                        'items' => "Insufficient stock for {$product->name}. Available: {$product->quantity}.",
                    ]);
                }

                $unitPrice = (float) $product->selling_price;
                $lineTotal = round($unitPrice * $quantity, 2);

                $lines[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'line_total' => $lineTotal,
                ];

                $subtotal = round($subtotal + $lineTotal, 2);
            }

            $discountType = $draft['discount_type'];
            $discountValue = (float) $draft['discount_value'];

            if ($discountType === 'percentage' && $discountValue > 100) {
                throw ValidationException::withMessages([
                    'discount_value' => 'Percentage discount cannot exceed 100%.',
                ]);
            }

            $discountAmount = $discountType === 'percentage'
                ? round($subtotal * $discountValue / 100, 2)
                : round(min($discountValue, $subtotal), 2);

            $taxRate = (float) $draft['tax_rate'];
            $taxableAmount = max(0, $subtotal - $discountAmount);
            $taxAmount = round($taxableAmount * $taxRate / 100, 2);
            $total = round($taxableAmount + $taxAmount, 2);

            $paidAmount = round((float) $draft['paid_amount'], 2);

            if ($paidAmount > $total) {
                throw ValidationException::withMessages([
                    'paid_amount' => 'Paid amount cannot exceed the invoice total.',
                ]);
            }

            $balance = round($total - $paidAmount, 2);

            $status = $paidAmount <= 0
                ? 'unpaid'
                : ($balance <= 0 ? 'paid' : 'partially_paid');

            do {
                $invoiceNumber = 'INV-' . Str::upper(Str::random(10));
            } while (Invoice::where('invoice_number', $invoiceNumber)->exists());

            $invoice = Invoice::create([
                'customer_id' => $customer->id,
                'user_id' => Auth::id(),
                'invoice_number' => $invoiceNumber,
                'invoice_date' => $draft['invoice_date'],
                'subtotal' => $subtotal,
                'discount_type' => $discountType,
                'discount_value' => $discountValue,
                'discount_amount' => $discountAmount,
                'tax_rate' => $taxRate,
                'tax_amount' => $taxAmount,
                'total_amount' => $total,
                'paid_amount' => $paidAmount,
                'balance_amount' => $balance,
                'status' => $status,
                'notes' => $draft['notes'] ?? null,
            ]);

            foreach ($lines as $line) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $line['product']->id,
                    'quantity' => $line['quantity'],
                    'unit_price' => $line['unit_price'],
                    'discount_amount' => 0,
                    'line_total' => $line['line_total'],
                ]);

                $line['product']->decrement('quantity', $line['quantity']);
            }

            if ($paidAmount > 0) {
                do {
                    $paymentNumber = 'PAY-' . Str::upper(Str::random(10));
                } while (Payment::where('payment_number', $paymentNumber)->exists());

                Payment::create([
                    'invoice_id' => $invoice->id,
                    'user_id' => Auth::id(),
                    'payment_number' => $paymentNumber,
                    'payment_date' => now()->toDateString(),
                    'amount' => $paidAmount,
                    'payment_method' => $draft['payment_method'],
                    'notes' => 'Payment recorded during invoice checkout.',
                ]);
            }

            return $invoice;
        });

        session()->forget('sale_draft');

        return redirect()
            ->route('invoices.show', $invoice)
            ->with('success', 'Sale completed successfully.');
    }



    public function show(Customer $customer)
    {
        return redirect()->route('customers.index');
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([

            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:15', 'regex:/^\+?[0-9]+$/',],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ], [
            'phone.regex' => 'The phone number may contain only digits (0-9) and an optional + at the beginning.',
        ]);

        $customer->update($validated);

        return redirect()->route('customers.index')
            ->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        try {
            $customer->delete();

            return redirect()->route('customers.index')
                ->with('success', 'Customer deleted successfully.');
        } catch (QueryException $e) {
            return redirect()->route('customers.index')
                ->with('error', 'Cannot delete this customer because they are linked to existing invoices.');
        }
    }
}
