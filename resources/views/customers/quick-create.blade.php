@extends('layouts.app')

@section('title', 'Add Customer')

@section('content')
    <div class="page-heading">
        <div>
            <h1>Add Customer</h1>
            <p>Enter details to create a new customer.</p>
        </div>
    </div>

    <div class="customer-create-layout">
        <div class="form-card">
            <form action="{{ route('customers.store') }}" method="POST">
                @csrf

                <p class="form-hint">
                    Customer Code will be generated automatically.
                </p>

                <div class="form-group">
                    <label for="name">Customer Name *</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="phone">Phone *</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}" required
                        autocomplete="tel">
                    @error('phone')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}">
                    @error('email')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" name="address" rows="3">{{ old('address') }}</textarea>
                    @error('address')
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
                    <button type="submit" class="primary-btn">
                        Save New Customer
                    </button>

                    <a href="{{ route('customers.index') }}" class="secondary-btn">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
