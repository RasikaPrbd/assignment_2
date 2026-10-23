<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with([
            'invoice.customer',
            'user'
        ])
            ->latest()
            ->paginate(10);

        return view('payments.index', compact('payments'));
    }


    public function create(Request $request)
    {
        $invoices = Invoice::with('customer')
            ->where('balance_amount', '>', 0)
            ->orderBy('invoice_date')
            ->get();

        $selectedInvoiceId = $request->query('invoice_id');

        return view('payments.create', compact(
            'invoices',
            'selectedInvoiceId'
        ));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_id' => ['required', 'exists:invoices,id'],
            'payment_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'payment_method' => [
                'required',
                'in:cash,card,bank_transfer,other',
            ],
            'reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($validated) {
            $invoice = Invoice::whereKey($validated['invoice_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $balance = (float) $invoice->balance_amount;
            $amount = (float) $validated['amount'];

            if ($balance <= 0) {
                throw ValidationException::withMessages([
                    'invoice_id' => 'This invoice has no outstanding balance.',
                ]);
            }

            if ($amount > $balance) {
                throw ValidationException::withMessages([
                    'amount' => 'Payment cannot exceed the outstanding balance.',
                ]);
            }

            Payment::create([
                'invoice_id' => $invoice->id,
                'user_id' => Auth::id(),
                'payment_number' => 'PAY-' . strtoupper(Str::random(10)),
                'payment_date' => $validated['payment_date'],
                'amount' => $amount,
                'payment_method' => $validated['payment_method'],
                'reference' => $validated['reference'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            $newPaidAmount = (float) $invoice->paid_amount + $amount;
            $newBalance = max(0, (float) $invoice->total_amount - $newPaidAmount);

            $invoice->update([
                'paid_amount' => $newPaidAmount,
                'balance_amount' => $newBalance,
                'status' => $newBalance <= 0 ? 'paid' : 'partially_paid',
            ]);
        });

        return redirect()
            ->route('payments.index')
            ->with('success', 'Payment recorded successfully.');
    }

    public function show(Payment $payment)
    {
        $payment->load('invoice.customer', 'user');

        return view('payments.show', compact('payment'));
    }
}
