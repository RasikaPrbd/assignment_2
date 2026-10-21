@extends('layouts.app')

@section('title', 'Purchases')

@section('content')
    <div class="page-heading">
        <div>
            <h1>Purchases</h1>
            <p>View supplier purchases and received stock.</p>
        </div>
        <a href="{{ route('purchases.create') }}" class="primary-btn">
            + New Purchase
        </a>
    </div>

    @if (session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert-error">{{ session('error') }}</div>
    @endif

    <div class="table-card">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Purchase No.</th>
                        <th>Date</th>
                        <th>Supplier</th>
                        <th>Created By</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($purchases as $purchase)
                        <tr>
                            <td>{{ $purchase->purchase_number }}</td>
                            <td>{{ $purchase->purchase_date->format('Y-m-d') }}</td>
                            <td>{{ $purchase->supplier->supplier_name }}</td>
                            <td>{{ $purchase->user->name }}</td>
                            <td>{{ number_format($purchase->total_amount, 2) }}</td>
                            <td>
                                <span class="status-active">
                                    {{ ucfirst($purchase->status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('purchases.show', $purchase) }}" class="secondary-btn">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">No purchases found. Create your first purchase.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrapper">
            {{ $purchases->links() }}
        </div>
    </div>
@endsection
