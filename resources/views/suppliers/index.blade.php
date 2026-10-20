@extends('layouts.app')

@section('title', 'Suppliers')

@section('page-title', 'Suppliers')

@section('content')

    <div class="page-heading">
        <div>
            <h1>Suppliers</h1>
            <p class="welcome-text">
                Manage your suppliers and their contact details.
            </p>
        </div>

        <a href="{{ route('suppliers.create') }}" class="primary-btn">
            + Add Supplier
        </a>
    </div>

    @if (session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert-error">{{ session('error') }}</div>
    @endif

    <div class="table-card">
        <div class="table-header">
            <h3>Supplier List</h3>
            <span>{{ $suppliers->total() }} suppliers</span>
        </div>

        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Supplier Name</th>
                        <th>Company</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($suppliers as $supplier)
                        <tr>
                            <td>{{ $suppliers->firstItem() + $loop->index }}</td>
                            <td>{{ $supplier->supplier_name }}</td>
                            <td>{{ $supplier->company_name ?: '—' }}</td>
                            <td>{{ $supplier->phone }}</td>
                            <td>{{ $supplier->email ?: '—' }}</td>

                            <td>
                                @if ($supplier->is_active)
                                    <span class="status-active">Active</span>
                                @else
                                    <span class="status-inactive">Inactive</span>
                                @endif
                            </td>

                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('suppliers.edit', $supplier) }}" class="edit-btn">
                                        Edit
                                    </a>

                                    <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST"
                                        onsubmit="return confirm('Are you sure you want to delete this supplier?')">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="delete-btn">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty-state">
                                No suppliers found. Add your first supplier.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrapper">
            {{ $suppliers->links() }}
        </div>
    </div>

@endsection
