@extends('layouts.app')

@section('title', 'Edit Customer')

@section('content')
    <div class="page-heading">
        <div>
            <h1>Edit Customer</h1>
            <p>Update the customer's details.</p>
        </div>
    </div>

    <div class="form-card">
        <form action="{{ route('customers.update', $customer) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="customer_code">Customer Code</label>
                <input type="text" id="customer_code" value="{{ $customer->customer_code }}" readonly>
                <p class="form-hint">Customer Code cannot be changed.</p>
            </div>

            <div class="form-group">
                <label for="name">Customer Name *</label>
                <input type="text" id="name" name="name" value="{{ old('name', $customer->name) }}" required>
                @error('name')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="phone">Phone *</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone', $customer->phone) }}" required>
                @error('phone')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', $customer->email) }}">
                @error('email')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="address">Address</label>
                <textarea id="address" name="address" rows="3">{{ old('address', $customer->address) }}</textarea>
                @error('address')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="is_active">Status *</label>
                <select id="is_active" name="is_active" required>
                    <option value="1" {{ old('is_active', (string) $customer->is_active) == '1' ? 'selected' : '' }}>
                        Active
                    </option>
                    <option value="0" {{ old('is_active', (string) $customer->is_active) == '0' ? 'selected' : '' }}>
                        Inactive
                    </option>
                </select>
                @error('is_active')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="primary-btn">
                    Update Customer
                </button>

                <a href="{{ route('customers.index') }}" class="secondary-btn">
                    Cancel
                </a>
            </div>
        </form>
    </div>
@endsection
