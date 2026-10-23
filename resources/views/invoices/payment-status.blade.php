@extends('layouts.app')

@section('content')
    <div class="page-container payment-management-page">

        {{-- Page Header --}}
        <div class="payment-page-header">
            <div>
                <span class="payment-eyebrow">FINANCIAL MANAGEMENT</span>
                <h1>Payment Status</h1>
                <p>Track invoice payments and manage outstanding balances.</p>
            </div>

            <a href="{{ route('invoices.index') }}" class="btn btn-primary payment-header-button">
                View Invoices
            </a>
        </div>

        {{-- Payment Summary --}}
        <section class="payment-summary-panel">
            <div class="payment-summary-heading">
                <div>
                    <h2>Payment Overview</h2>
                    <p>Current invoice payment status</p>
                </div>

                <span class="payment-summary-total">
                    {{ $totalInvoices }} invoices
                </span>
            </div>

            <div class="payment-status-summary">
                <div class="payment-summary-item">
                    <span class="summary-dot summary-dot-all"></span>
                    <span>Total</span>
                    <strong>{{ $totalInvoices }}</strong>
                </div>

                <div class="payment-summary-item">
                    <span class="summary-dot summary-dot-paid"></span>
                    <span>Paid</span>
                    <strong>{{ $paidInvoices }}</strong>
                </div>

                <div class="payment-summary-item">
                    <span class="summary-dot summary-dot-partial"></span>
                    <span>Partially Paid</span>
                    <strong>{{ $partialInvoices }}</strong>
                </div>

                <div class="payment-summary-item">
                    <span class="summary-dot summary-dot-unpaid"></span>
                    <span>Unpaid</span>
                    <strong>{{ $unpaidInvoices }}</strong>
                </div>
            </div>
        </section>

        {{-- Search and Filters --}}
        <section class="payment-filter-card">
            <div class="payment-section-heading">
                <div>
                    <h2>Find Invoices</h2>
                    <p>Search by invoice number or customer.</p>
                </div>
            </div>

            <form method="GET" action="{{ route('invoices.payment-status') }}" class="payment-filter-form">

                <div class="payment-search-field">
                    <label for="payment-search">Search</label>
                    <input id="payment-search" type="text" name="search" value="{{ request('search') }}"
                        placeholder="Invoice number or customer name" class="form-control">
                </div>

                <div class="payment-status-field">
                    <label for="payment-status-filter">Payment Status</label>
                    <select id="payment-status-filter" name="status" class="form-control">
                        <option value="">All statuses</option>
                        <option value="paid" @selected(request('status') === 'paid')>
                            Paid
                        </option>
                        <option value="partially_paid" @selected(request('status') === 'partially_paid')>
                            Partially Paid
                        </option>
                        <option value="unpaid" @selected(request('status') === 'unpaid')>
                            Unpaid
                        </option>
                    </select>
                </div>

                <div class="payment-filter-actions">
                    <button type="submit" class="btn btn-primary">
                        Search
                    </button>


                    <a href="{{ route('invoices.payment-status') }}" class="payment-reset-link">
                        Reset
                    </a>

                </div>
            </form>
        </section>

        {{-- Invoice Table --}}
        <section class="payment-invoices-panel">
            <div class="payment-table-heading">
                <div>
                    <h2>Invoice Payments</h2>
                    <p>Review paid amounts and remaining balances.</p>
                </div>

                <span class="payment-record-count">
                    {{ $invoices->total() }} records
                </span>
            </div>

            <div class="table-responsive">
                <table class="data-table payment-status-table">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Total Amount</th>
                            <th>Paid Amount</th>
                            <th>Outstanding</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($invoices as $invoice)
                            <tr>
                                <td>
                                    <a class="payment-invoice-number" href="{{ route('invoices.show', $invoice) }}">
                                        {{ $invoice->invoice_number }}
                                    </a>
                                </td>

                                <td>
                                    <span class="payment-customer-name">
                                        {{ $invoice->customer->name ?? 'N/A' }}
                                    </span>
                                </td>

                                <td>
                                    {{ $invoice->invoice_date?->format('Y-m-d') ?? 'N/A' }}
                                </td>

                                <td class="payment-money">
                                    Rs. {{ number_format((float) $invoice->total_amount, 2) }}
                                </td>

                                <td class="payment-money">
                                    Rs. {{ number_format((float) $invoice->paid_amount, 2) }}
                                </td>

                                <td>
                                    <span
                                        class="payment-outstanding
                                    {{ (float) $invoice->balance_amount > 0 ? 'has-balance' : 'no-balance' }}">
                                        Rs. {{ number_format((float) $invoice->balance_amount, 2) }}
                                    </span>
                                </td>

                                <td>
                                    @if ($invoice->status === 'paid')
                                        <span class="payment-status-badge status-paid">
                                            Paid
                                        </span>
                                    @elseif ($invoice->status === 'partially_paid')
                                        <span class="payment-status-badge status-partial">
                                            Partially Paid
                                        </span>
                                    @else
                                        <span class="payment-status-badge status-unpaid">
                                            Unpaid
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    <div class="payment-row-actions">
                                        <a href="{{ route('invoices.show', $invoice) }}" class="payment-action-link">
                                            View
                                        </a>

                                        @if ((float) $invoice->balance_amount > 0)
                                            <a href="{{ route('payments.create', ['invoice_id' => $invoice->id]) }}"
                                                class="payment-action-link payment-action-primary">
                                                Record Payment
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center payment-empty-state">
                                    <strong>No invoices found</strong>
                                    <p>Try changing your search or payment status filter.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($invoices->hasPages())
                <div class="payment-pagination">
                    {{ $invoices->links() }}
                </div>
            @endif
        </section>

    </div>
@endsection
