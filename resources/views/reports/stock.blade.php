@extends('layouts.app')

@section('content')
    <div class="stock-report-page">

        <div class="stock-report-header">
            <div>
                <h1>Stock Report</h1>
                <p>Monitor inventory levels and stock value.</p>
            </div>

            <button type="button" class="stock-print-btn" onclick="window.print()">
                Print Report
            </button>
        </div>

        <div class="stock-summary-grid">
            <div class="stock-summary-item">
                <span>Total Products</span>
                <strong>{{ number_format($totalProducts) }}</strong>
            </div>

            <div class="stock-summary-item">
                <span>Total Stock Units</span>
                <strong>{{ number_format($totalStockUnits) }}</strong>
            </div>

            <div class="stock-summary-item low-stock-summary">
                <span>Low Stock</span>
                <strong>{{ number_format($lowStockProducts) }}</strong>
            </div>

            <div class="stock-summary-item out-stock-summary">
                <span>Out of Stock</span>
                <strong>{{ number_format($outOfStockProducts) }}</strong>
            </div>

            <div class="stock-summary-item">
                <span>Total Stock Cost Value</span>
                <strong>Rs. {{ number_format($totalStockValue, 2) }}</strong>
            </div>
        </div>

        <form method="GET" action="{{ route('stock-report.index') }}" class="stock-filter-panel">

            <div class="stock-filter-field">
                <label for="search">Search Product</label>
                <input type="text" id="search" name="search" value="{{ request('search') }}"
                    placeholder="Product name or code...">
            </div>

            <div class="stock-filter-field">
                <label for="category_id">Category</label>
                <select id="category_id" name="category_id">
                    <option value="">All Categories</option>

                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="stock-filter-field">
                <label for="stock_status">Stock Status</label>
                <select id="stock_status" name="stock_status">
                    <option value="">All Statuses</option>
                    <option value="available" @selected(request('stock_status') === 'available')>
                        Available
                    </option>
                    <option value="low" @selected(request('stock_status') === 'low')>
                        Low Stock
                    </option>
                    <option value="out" @selected(request('stock_status') === 'out')>
                        Out of Stock
                    </option>
                </select>
            </div>

            <div class="stock-filter-actions">
                <button type="submit" class="stock-apply-btn">Apply Filters</button>
                <a href="{{ route('stock-report.index') }}" class="stock-reset-btn">Reset</a>
            </div>
        </form>

        <div class="stock-table-panel">
            <div class="stock-table-heading">
                <div>
                    <h2>Inventory Details</h2>
                    <p>Product quantities and current stock levels</p>
                </div>

                <span>{{ $products->total() }} products found</span>
            </div>

            <div class="stock-table-wrapper">
                <table class="stock-report-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Code</th>
                            <th>Category</th>
                            <th>Supplier</th>
                            <th>Cost Price</th>
                            <th>Selling Price</th>
                            <th>Quantity</th>
                            <th>Reorder Level</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td class="stock-product-name">
                                    {{ $product->name }}
                                </td>

                                <td>{{ $product->code }}</td>

                                <td>
                                    {{ $product->category?->name ?? 'N/A' }}
                                </td>

                                <td>
                                    {{ $product->supplier?->name ?? 'N/A' }}
                                </td>

                                <td>
                                    Rs. {{ number_format((float) $product->cost_price, 2) }}
                                </td>

                                <td>
                                    Rs. {{ number_format((float) $product->selling_price, 2) }}
                                </td>

                                <td class="stock-quantity">
                                    {{ number_format($product->quantity) }}
                                </td>

                                <td>{{ number_format($product->reorder_level) }}</td>

                                <td>
                                    @if ($product->quantity <= 0)
                                        <span class="stock-status-badge status-out">
                                            Out of Stock
                                        </span>
                                    @elseif($product->quantity <= $product->reorder_level)
                                        <span class="stock-status-badge status-low">
                                            Low Stock
                                        </span>
                                    @else
                                        <span class="stock-status-badge status-available">
                                            Available
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="stock-empty">
                                    No products match your search.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="stock-pagination">
                {{ $products->links() }}
            </div>
        </div>
    </div>
@endsection
