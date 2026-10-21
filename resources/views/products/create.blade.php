@extends('layouts.app')

@section('title', 'Add Product')

@section('content')
    <div class="page-heading">
        <div>
            <h1>Add Product</h1>
            <p>Enter product details and set the stock reorder level.</p>
        </div>
    </div>

    <div class="form-card">
        <form action="{{ route('products.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="name">Product Name *</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" maxlength="150" required>
                @error('name')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="code">Product Code / SKU *</label>
                <input type="text" id="code" name="code" value="{{ old('code') }}" maxlength="50" required>
                @error('code')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="category_id">Category *</label>
                <select id="category_id" name="category_id" required>
                    <option value="">Select Category</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="supplier_id">Supplier *</label>
                <select id="supplier_id" name="supplier_id" required>
                    <option value="">Select Supplier</option>
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->supplier_name }}
                        </option>
                    @endforeach
                </select>
                @error('supplier_id')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="cost_price">Cost Price *</label>
                <input type="number" id="cost_price" name="cost_price" value="{{ old('cost_price') }}" min="0"
                    step="0.01" required>
                @error('cost_price')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="selling_price">Selling Price *</label>
                <input type="number" id="selling_price" name="selling_price" value="{{ old('selling_price') }}"
                    min="0" step="0.01" required>
                @error('selling_price')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="quantity">Quantity *</label>
                <input type="number" id="quantity" name="quantity" value="{{ old('quantity', 0) }}" min="0"
                    step="1" required>
                @error('quantity')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="reorder_level">Reorder Level *</label>
                <input type="number" id="reorder_level" name="reorder_level" value="{{ old('reorder_level', 10) }}"
                    min="0" step="1" required>
                <p class="form-hint">
                    The product is marked Low Stock when quantity reaches this level or below.
                </p>
                @error('reorder_level')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="3">{{ old('description') }}</textarea>
                @error('description')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="is_active">Status *</label>
                <select id="is_active" name="is_active" required>
                    <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>
                        Active
                    </option>
                    <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>
                        Inactive
                    </option>
                </select>
                @error('is_active')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="primary-btn">Save Product</button>
                <a href="{{ route('products.index') }}" class="secondary-btn">Cancel</a>
            </div>
        </form>
    </div>
@endsection
