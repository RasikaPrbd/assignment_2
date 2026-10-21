@extends('layouts.app')

@section('title', 'Purchase Details')

@section('content')
    <div class="page-heading">
        <div>
            <h1>Purchase Details</h1>
            <p>View purchase information and received items.</p>
        </div>
        <a href="{{ route('purchases.index') }}" class="secondary-btn">
            Back to Purchases
        </a>
    </div>

    <div class="form-card">
        <div class="form-group">
            <label>Purchase Number</label>
            <p>{{ $purchase->purchase_number }}</p>
        </div>

        <div class="form-group">
            <label>Supplier</label>
            <p>{{ $purchase->supplier->supplier_name }}</p>
        </div>

        <div class="form-group">
            <label>Purchase Date</label>
            <p>{{ $purchase->purchase_date->format('Y-m-d') }}</p>
        </div>

        <div class="form-group">
            <label>Created By</label>
            <p>{{ $purchase->user->name }}</p>
        </div>

        <div class="form-group">
            <label>Status</label>
            <p><span class="status-active">{{ ucfirst($purchase->status) }}</span></p>
        </div>

        @if ($purchase->notes)
            <div class="form-group">
                <label>Notes</label>
                <p>{{ $purchase->notes }}</p>
            </div>
        @endif
    </div>

    <div class="table-card" style="margin-top: 24px;">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Product Code</th>
                        <th>Quantity</th>
                        <th>Unit Cost</th>
                        <th>Line Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($purchase->items as $item)
                        <tr>
                            <td>{{ $item->product->name }}</td>
                            <td>{{ $item->product->code }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ number_format($item->unit_cost, 2) }}</td>
                            <td>{{ number_format($item->line_total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4" style="text-align: right;">Subtotal</th>
                        <th>{{ number_format($purchase->subtotal, 2) }}</th>
                    </tr>
                    <tr>
                        <th colspan="4" style="text-align: right;">Grand Total</th>
                        <th>{{ number_format($purchase->total_amount, 2) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection
