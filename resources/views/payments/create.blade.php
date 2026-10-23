@extends('layouts.app')

@section('title', 'Record Payment')
@section('page-title', 'Record Payment')

@section('content')
    <div class="page-heading">
        <div>
            <h1>Record Payment</h1>
            <p class="welcome-text">
                Record a payment against an outstanding invoice.
            </p>
        </div>
    </div>

    <div class="form-card">
        <form action="{{ route('payments.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="invoice_id">Invoice <span>*</span></label>


                <select name="invoice_id" id="invoice_id" class="form-control" required>
                    <option value="">Select an invoice</option>

                    @foreach ($invoices as $invoice)
                        <option value="{{ $invoice->id }}" data-balance="{{ $invoice->balance_amount }}"
                            @selected((string) old('invoice_id', $selectedInvoiceId ?? '') === (string) $invoice->id)>
                            {{ $invoice->invoice_number }}
                            - {{ $invoice->customer->name ?? 'N/A' }}
                            - Outstanding: Rs. {{ number_format((float) $invoice->balance_amount, 2) }}
                        </option>
                    @endforeach
                </select>


                @error('invoice_id')
                    <span class="field-error">{{ $message }}</span>
                @enderror

                @if ($invoices->isEmpty())
                    <small>No outstanding invoices are available.</small>
                @endif
            </div>

            <div class="form-group">
                <label for="payment_date">Payment Date <span>*</span></label>

                <input type="date" name="payment_date" id="payment_date"
                    value="{{ old('payment_date', now()->format('Y-m-d')) }}" required>

                @error('payment_date')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="amount">Payment Amount (Rs.) <span>*</span></label>

                <input type="number" name="amount" id="amount" value="{{ old('amount') }}" min="0.01"
                    step="0.01" required>

                <small id="balance-info">
                    Select an invoice to view its outstanding balance.
                </small>

                @error('amount')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="payment_method">Payment Method <span>*</span></label>

                <select name="payment_method" id="payment_method" required>
                    <option value="">Select a method</option>

                    <option value="cash" @selected(old('payment_method') === 'cash')>
                        Cash
                    </option>

                    <option value="card" @selected(old('payment_method') === 'card')>
                        Card
                    </option>

                    <option value="bank_transfer" @selected(old('payment_method') === 'bank_transfer')>
                        Bank Transfer
                    </option>

                    <option value="other" @selected(old('payment_method') === 'other')>
                        Other
                    </option>
                </select>

                @error('payment_method')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="reference">Reference</label>

                <input type="text" name="reference" id="reference" value="{{ old('reference') }}" maxlength="255"
                    placeholder="Transaction reference (optional)">

                @error('reference')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="notes">Notes</label>

                <textarea name="notes" id="notes" rows="3" placeholder="Additional notes (optional)">{{ old('notes') }}</textarea>

                @error('notes')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="primary-btn">
                    Record Payment
                </button>

                <a href="{{ route('payments.index') }}" class="secondary-btn">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    <script>
        const invoiceSelect = document.getElementById('invoice_id');
        const amountInput = document.getElementById('amount');
        const balanceInfo = document.getElementById('balance-info');

        function updateInvoiceBalance() {
            const selectedOption =
                invoiceSelect.options[invoiceSelect.selectedIndex];

            const balance = selectedOption?.dataset.balance;

            if (balance !== undefined && balance !== '') {
                const formattedBalance = Number(balance).toLocaleString(
                    'en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }
                );

                balanceInfo.textContent =
                    'Outstanding balance: Rs. ' + formattedBalance;

                amountInput.max = balance;
            } else {
                balanceInfo.textContent =
                    'Select an invoice to view its outstanding balance.';

                amountInput.removeAttribute('max');
            }
        }

        invoiceSelect.addEventListener('change', updateInvoiceBalance);

        updateInvoiceBalance();
    </script>
@endsection
