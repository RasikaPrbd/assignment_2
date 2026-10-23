@extends('layouts.app')

@section('content')
    <div class="page-container invoice-list-page">

        {{-- Page Header --}}
        <div class="invoice-list-header">
            <div>
                <span class="invoice-list-eyebrow">SALES MANAGEMENT</span>
                <h1>Invoices</h1>
                <p>View and manage customer invoices and payments.</p>
            </div>

            <a href="{{ route('invoices.create') }}" class="btn btn-primary invoice-new-sale-button">
                <span aria-hidden="true">+</span> New Sale
            </a>
        </div>

        {{-- Success Message --}}
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        {{-- Invoice List --}}
        <section class="invoice-list-panel">

            <div class="invoice-list-panel-header">
                <div>
                    <h2>All Invoices</h2>
                    <p>Invoice records and payment summaries</p>
                </div>

                <span class="invoice-list-count">
                    {{ $invoices->total() }}
                    {{ $invoices->total() === 1 ? 'invoice' : 'invoices' }}
                </span>
            </div>

            <div class="table-responsive">
                <table class="data-table invoice-list-table">
                    <thead>
                        <tr>
                            <th>Invoice No.</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Total Amount</th>
                            <th>Paid Amount</th>
                            <th>Balance</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($invoices as $invoice)
                            <tr>
                                <td>
                                    <a href="{{ route('invoices.show', $invoice) }}" class="invoice-list-number">
                                        {{ $invoice->invoice_number }}
                                    </a>
                                </td>

                                <td>
                                    <span class="invoice-list-customer">
                                        {{ $invoice->customer->name ?? 'N/A' }}
                                    </span>
                                </td>

                                <td class="invoice-list-date">
                                    {{ $invoice->invoice_date?->format('Y-m-d') ?? 'N/A' }}
                                </td>

                                <td class="invoice-list-money">
                                    Rs. {{ number_format((float) $invoice->total_amount, 2) }}
                                </td>

                                <td class="invoice-list-money">
                                    Rs. {{ number_format((float) $invoice->paid_amount, 2) }}
                                </td>

                                <td>
                                    <span
                                        class="invoice-list-balance
                                    {{ (float) $invoice->balance_amount > 0 ? 'balance-pending' : 'balance-settled' }}">
                                        Rs. {{ number_format((float) $invoice->balance_amount, 2) }}
                                    </span>
                                </td>

                                <td>
                                    @if ($invoice->status === 'paid')
                                        <span class="invoice-status-label invoice-status-paid">
                                            Paid
                                        </span>
                                    @elseif ($invoice->status === 'partially_paid')
                                        <span class="invoice-status-label invoice-status-partial">
                                            Partially Paid
                                        </span>
                                    @else
                                        <span class="invoice-status-label invoice-status-unpaid">
                                            Unpaid
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    <a href="{{ route('invoices.show', $invoice) }}" class="invoice-list-view-button">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="invoice-list-empty">
                                    <strong>No invoices found</strong>
                                    <p>Invoices will appear here after a sale is created.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($invoices->hasPages())
                <div class="pagination-container invoice-list-pagination">
                    {{ $invoices->links() }}
                </div>
            @endif

        </section>
    </div>
@endsection
