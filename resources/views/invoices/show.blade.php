 @extends('layouts.app')

@section('title', 'Invoice Details')

@section('content')
    <div class="page-heading">
        <div>
            <h1>Invoice Details</h1>
            <p>View sale, payment and outstanding balance.</p>
        </div>

        <div class="form-actions no-print">
            <button type="button" onclick="window.print()" class="primary-btn">
                Print Bill
            </button>

            <a href="{{ route('invoices.create') }}" class="secondary-btn">
                New Sale
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert-success no-print">
            {{ session('success') }}
        </div>
    @endif

    <div class="form-card invoice-paper">
        <div class="invoice-header">
            <div>
                <h1>SALES INVOICE</h1>
                <p>Invoice No: <strong>{{ $invoice->invoice_number }}</strong></p>
                <p>Date: {{ $invoice->invoice_date->format('Y-m-d') }}</p>
                @if ($invoice->due_date)
                    <p>Due Date: {{ $invoice->due_date->format('Y-m-d') }}</p>
                @endif
            </div>

            <div>
                <h2>Customer</h2>
                <p>{{ $invoice->customer->name }}</p>
                <p>{{ $invoice->customer->phone }}</p>
                @if ($invoice->customer->email)
                    <p>{{ $invoice->customer->email }}</p>
                @endif
                @if ($invoice->customer->address)
                    <p>{{ $invoice->customer->address }}</p>
                @endif
            </div>
        </div>

        <div class="table-card">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Unit Price</th>
                        <th>Quantity</th>
                        <th>Line Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($invoice->items as $item)
                        <tr>
                            <td>{{ $item->product->name }}</td>
                            <td>Rs. {{ number_format($item->unit_price, 2) }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>Rs. {{ number_format($item->line_total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="invoice-totals">
            <p>
                Subtotal:
                <strong>Rs. {{ number_format($invoice->subtotal, 2) }}</strong>
            </p>

            <p>
                Discount:
                <strong>Rs. {{ number_format($invoice->discount_amount, 2) }}</strong>
            </p>

            <p>
                Tax ({{ number_format($invoice->tax_rate, 2) }}%):
                <strong>Rs. {{ number_format($invoice->tax_amount, 2) }}</strong>
            </p>

            <h2>
                Grand Total:
                Rs. {{ number_format($invoice->total_amount, 2) }}
            </h2>

            <p>
                Paid Amount:
                <strong>Rs. {{ number_format($invoice->paid_amount, 2) }}</strong>
            </p>

            <h3>
                Outstanding Balance:
                Rs. {{ number_format($invoice->balance_amount, 2) }}
            </h3>

            <p>
                Payment Status:
                <strong>{{ ucfirst(str_replace('_', ' ', $invoice->status)) }}</strong>
            </p>
        </div>

        @if ($invoice->payments->isNotEmpty())
            <h3>Payment Details</h3>

            <div class="table-card">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Payment No.</th>
                            <th>Date</th>
                            <th>Method</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoice->payments as $payment)
                            <tr>
                                <td>{{ $payment->payment_number }}</td>
                                <td>{{ $payment->payment_date->format('Y-m-d') }}</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                                <td>Rs. {{ number_format($payment->amount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @if ($invoice->notes)
            <div class="invoice-notes">
                <strong>Notes:</strong>
                <p>{{ $invoice->notes }}</p>
            </div>
        @endif

        <div class="invoice-footer">
            <p>Thank you for your business!</p>
        </div>
    </div>

    <style>
        .invoice-header {
            display: flex;
            justify-content: space-between;
            gap: 24px;
            flex-wrap: wrap;
            margin-bottom: 28px;
        }

        .invoice-totals {
            max-width: 420px;
            margin: 24px 0 24px auto;
            text-align: right;
        }

        .invoice-totals p {
            margin: 10px 0;
        }

        .invoice-footer {
            margin-top: 40px;
            text-align: center;
            border-top: 1px solid #ddd;
            padding-top: 16px;
        }

        @media print {
            body {
                background: #fff !important;
            }

            .no-print,
            aside,
            nav,
            header {
                display: none !important;
            }

            .invoice-paper {
                border: none !important;
                box-shadow: none !important;
                width: 100% !important;
                max-width: none !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .data-table {
                width: 100%;
                border-collapse: collapse;
            }

            .data-table th,
            .data-table td {
                border: 1px solid #ddd;
                padding: 8px;
            }
        }
    </style>
@endsection
