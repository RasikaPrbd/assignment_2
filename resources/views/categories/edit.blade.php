@extends('layouts.app')

@section('title', 'Edit Category')

@section('page-title', 'Edit Category')

@section('content')

    <div class="page-heading">
        <div>
            <h1>Edit Category</h1>
            <p class="welcome-text">
                Update the details of this category.
            </p>
        </div>

        <a href="{{ route('categories.index') }}" class="secondary-btn">
            Back to Categories
        </a>
    </div>

    <div class="form-card">

        <form action="{{ route('categories.update', $category) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">Category Name <span>*</span></label>

                <input type="text" id="name" name="name" value="{{ old('name', $category->name) }}"
                    maxlength="255" required>

                @error('name')
                    <small class="field-error">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label for="description">Description</label>

                <textarea id="description" name="description" rows="4">{{ old('description', $category->description) }}</textarea>

                @error('description')
                    <small class="field-error">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label for="is_active">Status <span>*</span></label>

                <select id="is_active" name="is_active" required>
                    <option value="1"
                        {{ (string) old('is_active', (int) $category->is_active) === '1' ? 'selected' : '' }}>
                        Active
                    </option>

                    <option value="0"
                        {{ (string) old('is_active', (int) $category->is_active) === '0' ? 'selected' : '' }}>
                        Inactive
                    </option>
                </select>

                @error('is_active')
                    <small class="field-error">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="primary-btn">
                    Update Category
                </button>

                <a href="{{ route('categories.index') }}" class="secondary-btn">
                    Cancel
                </a>
            </div>

        </form>

    </div>

@endsection
