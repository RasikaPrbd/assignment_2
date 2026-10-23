@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    <div class="dashboard-page">

        {{-- Welcome --}}
        <div class="dashboard-welcome">
            <div>
                <h1>Welcome, {{ auth()->user()->name }}!</h1>
                <p>Here is an overview of your business.</p>
            </div>

            <div class="dashboard-date">
                <span>Today's date</span>
                <strong>{{ now()->format('d M Y') }}</strong>
            </div>
        </div>

        {{-- Summary cards --}}
        <div class="dashboard-stats">

            <div class="dashboard-stat-card">
                <div class="dashboard-stat-icon stat-icon-blue">P</div>
                <div class="dashboard-stat-label">Total Products</div>
                <div class="dashboard-stat-value">
                    {{ number_format($totalProducts) }}
                </div>
                <div class="dashboard-stat-note">Products in inventory</div>
            </div>

            <div class="dashboard-stat-card">
                <div class="dashboard-stat-icon stat-icon-green">C</div>
                <div class="dashboard-stat-label">Total Customers</div>
                <div class="dashboard-stat-value">
                    {{ number_format($totalCustomers) }}
                </div>
                <div class="dashboard-stat-note">Registered customers</div>
            </div>

            <div class="dashboard-stat-card">
                <div class="dashboard-stat-icon stat-icon-purple">S</div>
                <div class="dashboard-stat-label">Total Suppliers</div>
                <div class="dashboard-stat-value">
                    {{ number_format($totalSuppliers) }}
                </div>
                <div class="dashboard-stat-note">Registered suppliers</div>
            </div>

            <div class="dashboard-stat-card">
                <div class="dashboard-stat-icon stat-icon-blue">↗</div>
                <div class="dashboard-stat-label">Total Sales</div>
                <div class="dashboard-stat-value money">
                    Rs. {{ number_format($totalSales, 2) }}
                </div>
                <div class="dashboard-stat-note">Total invoiced amount</div>
            </div>

            <div class="dashboard-stat-card">
                <div class="dashboard-stat-icon stat-icon-green">T</div>
                <div class="dashboard-stat-label">Today's Sales</div>
                <div class="dashboard-stat-value money">
                    Rs. {{ number_format($todaySales, 2) }}
                </div>
                <div class="dashboard-stat-note">Invoices dated today</div>
            </div>

            <div class="dashboard-stat-card">
                <div class="dashboard-stat-icon stat-icon-red">!</div>
                <div class="dashboard-stat-label">Outstanding Payments</div>
                <div class="dashboard-stat-value money">
                    Rs. {{ number_format($outstandingPayments, 2) }}
                </div>
                <div class="dashboard-stat-note">Remaining balances</div>
            </div>

            <div class="dashboard-stat-card">
                <div class="dashboard-stat-icon stat-icon-orange">!</div>
                <div class="dashboard-stat-label">Low Stock Products</div>
                <div class="dashboard-stat-value">
                    {{ number_format($lowStockProducts) }}
                </div>
                <div class="dashboard-stat-note">At or below reorder level</div>
            </div>

        </div>

        {{-- Recent invoices --}}
        <section class="dashboard-panel">
            <div class="dashboard-panel-heading">
                <div>
                    <h2>Recent Invoices</h2>
                    <p>Latest sales transactions</p>
                </div>

                <a href="{{ route('invoices.index') }}" class="dashboard-panel-link">
                    View all invoices &rarr;
                </a>
            </div>

            <div class="dashboard-table-wrap">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>Invoice No.</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($recentInvoices as $invoice)
                            @php
                                $status = strtolower(
                                    str_replace('_', ' ', $invoice->payment_status ?? ($invoice->status ?? 'unpaid')),
                                );
                            @endphp

                            <tr>
                                <td>
                                    <a href="{{ route('invoices.show', $invoice) }}" class="dashboard-invoice-link">
                                        {{ $invoice->invoice_number }}
                                    </a>
                                </td>

                                <td>
                                    {{ $invoice->customer?->name ?? 'N/A' }}
                                </td>

                                <td>
                                    {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d M Y') }}
                                </td>

                                <td class="dashboard-amount">
                                    Rs. {{ number_format($invoice->total_amount, 2) }}
                                </td>

                                <td>
                                    <span
                                        class="dashboard-status
                                    @if ($status === 'paid') dashboard-status-paid
                                    @elseif (in_array($status, ['partially paid', 'partial']))
                                        dashboard-status-partial
                                    @else
                                        dashboard-status-unpaid @endif">
                                        {{ ucfirst($status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="dashboard-empty-state">
                                    <strong>No invoices yet</strong>
                                    <span>New sales invoices will appear here.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        {{-- Low stock alerts --}}
        <section class="dashboard-panel">
            <div class="dashboard-panel-heading">
                <div>
                    <h2>Low Stock Alerts</h2>
                    <p>Products that may need restocking</p>
                </div>

                <a href="{{ route('products.index') }}" class="dashboard-panel-link">
                    View products &rarr;
                </a>
            </div>

            <div class="dashboard-table-wrap">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Current Stock</th>
                            <th>Reorder Level</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($lowStockList as $product)
                            <tr>
                                <td>{{ $product->name }}</td>

                                <td class="dashboard-amount">
                                    {{ number_format($product->quantity) }}
                                </td>

                                <td>
                                    {{ number_format($product->reorder_level) }}
                                </td>

                                <td>
                                    <span class="dashboard-status dashboard-status-stock">
                                        Low stock
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="dashboard-empty-state">
                                    <strong>Stock levels look good</strong>
                                    <span>No products are at or below their reorder level.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

    </div>
@endsection
