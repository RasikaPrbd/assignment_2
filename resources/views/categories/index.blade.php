@extends('layouts.app')

@section('title', 'Categories')

@section('page-title', 'Categories')

@section('content')

    <div class="page-heading">
        <div>
            <h1>Categories</h1>
            <p class="welcome-text">
                Manage your product categories.
            </p>
        </div>

        <a href="{{ route('categories.create') }}" class="primary-btn">
            + Add Category
        </a>
    </div>

    @if (session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert-error">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert-error">
            Please check your input and try again.
        </div>
    @endif

    <div class="table-card">
        <div class="table-header">
            <h3>Category List</h3>
            <span>{{ $categories->total() }} categories</span>
        </div>

        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Category Name</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Created Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($categories as $category)
                        <tr>
                            <td>{{ $categories->firstItem() + $loop->index }}</td>
                            <td>{{ $category->name }}</td>
                            <td>{{ $category->description ?: '—' }}</td>
                            <td>
                                @if ($category->is_active)
                                    <span class="status-active">Active</span>
                                @else
                                    <span class="status-inactive">Inactive</span>
                                @endif
                            </td>
                            <td>{{ $category->created_at->format('Y-m-d') }}</td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('categories.edit', $category) }}" class="edit-btn">
                                        Edit
                                    </a>

                                    <form action="{{ route('categories.destroy', $category) }}" method="POST"
                                        onsubmit="return confirm('Are you sure you want to delete this category?')">
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
                            <td colspan="6" class="empty-state">
                                No categories found. Add your first category.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrapper">
            {{ $categories->links() }}
        </div>
    </div>

@endsection
