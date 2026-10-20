@extends('layouts.app')

@section('title', 'Add Category')

@section('page-title', 'Add Category')

@section('content')

    <div class="page-heading">
        <div>
            <h1>Add Category</h1>
            <p class="welcome-text">Create a new product category.</p>
        </div>

        <a href="{{ route('categories.index') }}" class="secondary-btn">
            Back to Categories
        </a>
    </div>

    <div class="form-card">

        <form action="{{ route('categories.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="name">Category Name <span>*</span></label>

                <input type="text" id="name" name="name" value="{{ old('name') }}"
                    placeholder="Enter category name" required maxlength="255">

                @error('name')
                    <small class="field-error">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label for="description">Description</label>

                <textarea id="description" name="description" rows="4" placeholder="Enter category description">{{ old('description') }}</textarea>

                @error('description')
                    <small class="field-error">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label for="is_active">Status <span>*</span></label>

                <select id="is_active" name="is_active" required>
                    <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>
                        Active
                    </option>

                    <option value="0" {{ old('is_active') === '0' ? 'selected' : '' }}>
                        Inactive
                    </option>
                </select>

                @error('is_active')
                    <small class="field-error">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="primary-btn">
                    Save Category
                </button>

                <a href="{{ route('categories.index') }}" class="secondary-btn">
                    Cancel
                </a>
            </div>

        </form>

    </div>

@endsection
