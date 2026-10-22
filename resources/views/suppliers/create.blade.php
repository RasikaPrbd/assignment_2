@extends('layouts.app')

@section('title', 'Add Supplier')

@section('content')
    <div class="page-heading">
        <div>
            <h1>Add Supplier</h1>
            <p>Enter the supplier's contact details.</p>
        </div>
    </div>

    <div class="form-card">
        <form action="{{ route('suppliers.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="supplier_name">Supplier Name *</label>
                <input type="text" id="supplier_name" name="supplier_name" value="{{ old('supplier_name') }}" required>
                @error('supplier_name')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="company_name">Company Name</label>
                <input type="text" id="company_name" name="company_name" value="{{ old('company_name') }}">
                @error('company_name')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="phone">Phone *</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone') }}" required>
                @error('phone')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required>
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
                    Save Supplier
                </button>

                <a href="{{ route('suppliers.index') }}" class="secondary-btn">
                    Cancel
                </a>
            </div>
        </form>
    </div>
@endsection
