@extends('layouts.app')

@section('title', 'User Management')
@section('page-title', 'User Management')

@section('content')
    <div class="user-management-page">

        <div class="page-heading">
            <div>
                <h1>User Management</h1>
                <p>Manage system users, roles and account status.</p>
            </div>

            <a href="{{ route('users.create') }}" class="primary-btn">
                Add User
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

        <div class="form-card user-search-card">
            <form action="{{ route('users.index') }}" method="GET">
                <div class="form-group">
                    <label for="search">Search Users</label>

                    <div class="user-search-form">
                        <div class="user-search-field">
                            <input type="text" id="search" name="search" value="{{ $search }}"
                                placeholder="Search by name or email">
                        </div>

                        <div class="user-search-field">
                            <label for="role_id">Filter by Role</label>

                            <select id="role_id" name="role_id">
                                <option value="">All Roles</option>

                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}"
                                        {{ (string) $roleId === (string) $role->id ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>


                        <button type="submit" class="primary-btn">
                            Search
                        </button>

                        <a href="{{ route('users.index') }}" class="secondary-btn">
                            Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="table-card user-table-wrap">
            <table class="data-table user-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>{{ $users->firstItem() + $loop->index }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->role?->name ?? 'No role' }}</td>

                            <td>
                                @if ($user->is_active)
                                    <span class="status-active">Active</span>
                                @else
                                    <span class="status-inactive">Inactive</span>
                                @endif
                            </td>

                            <td>{{ $user->created_at?->format('Y-m-d') }}</td>

                            <td>
                                <div class="user-actions">
                                    <a href="{{ route('users.edit', $user) }}" class="secondary-btn">
                                        Edit
                                    </a>

                                    @if (auth()->id() !== $user->id)
                                        <form action="{{ route('users.destroy', $user) }}" method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete this user?')">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="danger-btn">
                                                Delete
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="user-empty-state">
                                No users found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrapper">
            {{ $users->links() }}
        </div>

    </div>
@endsection
