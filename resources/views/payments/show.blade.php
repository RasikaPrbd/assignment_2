@extends('layouts.app')

@section('title', 'Payment Details')
@section('page-title', 'Payment Details')

@section('content')
    <div class="page-heading">
        <div>
            <h1>Payment Details</h1>
            <p class="welcome-text">
                View the details of this payment transaction.
            </p>
        </div>

        <a href="{{ route('payments.index') }}" class="secondary-btn">
            Back to Payments
        </a>
    </div>

    <div class="form-card">
        <h2 style="margin-top: 0; margin-bottom: 24px;">
            {{ $payment->payment_number }}
        </h2>

        <div class="table-responsive">
            <table class="data-table">
                <tbody>
                    <tr>
                        <th>Payment Number</th>
                        <td>{{ $payment->payment_number }}</td>
                    </tr>

                    <tr>
                        <th>Invoice Number</th>
                        <td>
                            @if ($payment->invoice)
                                <a href="{{ route('invoices.show', $payment->invoice) }}">
                                    {{ $payment->invoice->invoice_number }}
                                </a>
                            @else
                                N/A
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <th>Customer</th>
                        <td>
                            {{ $payment->invoice?->customer?->name ?? 'N/A' }}
                        </td>
                    </tr>

                    <tr>
                        <th>Payment Date</th>
                        <td>
                            {{ $payment->payment_date?->format('d M Y') ?? 'N/A' }}
                        </td>
                    </tr>

                    <tr>
                        <th>Amount</th>
                        <td>
                            <strong>
                                Rs. {{ number_format((float) $payment->amount, 2) }}
                            </strong>
                        </td>
                    </tr>

                    <tr>
                        <th>Payment Method</th>
                        <td>
                            {{ ucwords(str_replace('_', ' ', $payment->payment_method)) }}
                        </td>
                    </tr>

                    <tr>
                        <th>Reference</th>
                        <td>{{ $payment->reference ?: 'N/A' }}</td>
                    </tr>

                    <tr>
                        <th>Notes</th>
                        <td>{{ $payment->notes ?: 'N/A' }}</td>
                    </tr>

                    <tr>
                        <th>Recorded By</th>
                        <td>{{ $payment->user?->name ?? 'N/A' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="form-actions">
            <a href="{{ route('payments.index') }}" class="secondary-btn">
                Back to Payments
            </a>
        </div>
    </div>
@endsection
