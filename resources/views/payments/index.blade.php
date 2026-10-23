@extends('layouts.app')

@section('title', 'Payments')
@section('page-title', 'Payments')

@section('content')
    <div class="payments-page">

        {{-- Page Header --}}
        <div class="payments-heading">
            <div>
                <span class="payments-eyebrow">FINANCIAL MANAGEMENT</span>
                <h1>Payment Transactions</h1>
                <p>View and manage recorded customer payments.</p>
            </div>

            <a href="{{ route('payments.create') }}" class="payments-primary-btn">
                <span>+</span> Record Payment
            </a>
        </div>

        {{-- Payment Overview --}}
        <section class="payment-overview">
            <div class="overview-heading">
                <div>
                    <h2>Transaction Overview</h2>
                    <p>Summary of recorded payments</p>
                </div>

                <span class="transaction-count">
                    {{ number_format($payments->total()) }} records
                </span>
            </div>

            <div class="overview-details">
                <div class="overview-item">
                    <span class="overview-icon">↗</span>
                    <div>
                        <span class="overview-label">Total Transactions</span>
                        <strong>{{ number_format($payments->total()) }}</strong>
                    </div>
                </div>

                <div class="overview-item amount-overview">
                    <span class="overview-icon">Rs.</span>
                    <div>
                        <span class="overview-label">Amount on This Page</span>
                        <strong>
                            Rs. {{ number_format((float) $payments->sum('amount'), 2) }}
                        </strong>
                    </div>
                </div>
            </div>

            <p class="overview-note">
                The amount shown is for the currently displayed page of payment records.
            </p>
        </section>

        {{-- Payment Records --}}
        <section class="payment-records">
            <div class="records-heading">
                <div>
                    <h2>All Transactions</h2>
                    <p>Payment references, customers and received amounts</p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="payments-table">
                    <thead>
                        <tr>
                            <th>Payment Reference</th>
                            <th>Invoice</th>
                            <th>Customer</th>
                            <th>Payment Date</th>
                            <th class="amount-column">Amount Received</th>
                            <th>Method</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($payments as $payment)
                            <tr>
                                <td>
                                    <a href="{{ route('payments.show', $payment) }}" class="payment-reference">
                                        {{ $payment->payment_number }}
                                    </a>
                                    <span class="reference-caption">Payment ID</span>
                                </td>

                                <td>
                                    @if ($payment->invoice)
                                        <a href="{{ route('invoices.show', $payment->invoice) }}" class="invoice-reference">
                                            {{ $payment->invoice->invoice_number }}
                                        </a>
                                    @else
                                        <span class="muted-value">N/A</span>
                                    @endif
                                </td>

                                <td>
                                    <span class="customer-name">
                                        {{ $payment->invoice?->customer?->name ?? 'N/A' }}
                                    </span>
                                </td>

                                <td>
                                    {{ $payment->payment_date?->format('d M Y') ?? 'N/A' }}
                                </td>

                                <td class="amount-column">
                                    <strong class="payment-amount">
                                        Rs. {{ number_format((float) $payment->amount, 2) }}
                                    </strong>
                                </td>

                                <td>
                                    @php
                                        $method = strtolower($payment->payment_method ?? 'other');

                                        $methodClass = match ($method) {
                                            'cash' => 'method-cash',
                                            'card' => 'method-card',
                                            'bank_transfer' => 'method-bank',
                                            default => 'method-other',
                                        };
                                    @endphp

                                    <span class="payment-method {{ $methodClass }}">
                                        <span class="method-dot"></span>
                                        {{ ucwords(str_replace('_', ' ', $method)) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="payments-empty">
                                        <span class="empty-symbol">₨</span>
                                        <strong>No payment records yet</strong>
                                        <p>Recorded payments will appear here.</p>

                                        <a href="{{ route('payments.create') }}" class="payments-primary-btn">
                                            Record First Payment
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($payments->hasPages())
                <div class="payments-pagination">
                    {{ $payments->links() }}
                </div>
            @endif
        </section>

    </div>
@endsection
