@extends('layouts.app')

@section('title', 'Products')

@section('content')
    <div class="page-heading">
        <div>
            <h1>Products</h1>
            <p>Manage products and monitor stock levels.</p>
        </div>
        <a href="{{ route('products.create') }}" class="primary-btn">
            + Add Product
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
                        <th>Product</th>
                        <th>Code / SKU</th>
                        <th>Category</th>
                        <th>Supplier</th>
                        <th>Cost Price</th>
                        <th>Selling Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->code }}</td>
                            <td>{{ $product->category->name }}</td>
                            <td>{{ $product->supplier->supplier_name }}</td>
                            <td>{{ number_format($product->cost_price, 2) }}</td>
                            <td>{{ number_format($product->selling_price, 2) }}</td>
                            <td>
                                <strong>{{ $product->quantity }}</strong>
                                @if ($product->isLowStock())
                                    <span class="status-inactive">Low Stock</span>
                                @endif
                                <small>Reorder at: {{ $product->reorder_level }}</small>
                            </td>
                            <td>
                                @if ($product->is_active)
                                    <span class="status-active">Active</span>
                                @else
                                    <span class="status-inactive">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="button-actions">
                                    <a href="{{ route('products.edit', $product) }}" class="secondary-btn">Edit</a>

                                    <form action="{{ route('products.destroy', $product) }}" method="POST"
                                        onsubmit="return confirm('Delete this product?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="danger-btn">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">No products found. Add your first product.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrapper">
            {{ $products->links() }}
        </div>
    </div>
@endsection
