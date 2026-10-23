@extends('layouts.app')

@section('title', 'Add User')

@section('content')
    <div class="page-heading">
        <div>
            <h1>Add User</h1>
            <p>Create a system user and assign access permissions.</p>
        </div>
    </div>

    <div class="form-card">
        <form action="{{ route('users.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="name">Full Name *</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required maxlength="255">
                @error('name')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">Email Address *</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required maxlength="255">
                @error('email')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Password *</label>
                <input type="password" id="password" name="password" required minlength="8" autocomplete="new-password">
                <small>Use at least 8 characters.</small>
                @error('password')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">
                    Confirm Password *
                </label>
                <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8"
                    autocomplete="new-password">
            </div>

            <div class="form-group">
                <label for="role_id">User Role *</label>
                <select id="role_id" name="role_id" required>
                    <option value="">Select a role</option>

                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>

                @error('role_id')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="is_active">Account Status *</label>
                <select id="is_active" name="is_active" required>
                    <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>
                        Active
                    </option>

                    <option value="0" {{ old('is_active') === '0' ? 'selected' : '' }}>
                        Inactive
                    </option>
                </select>

                @error('is_active')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="primary-btn">
                    Create User
                </button>

                <a href="{{ route('users.index') }}" class="secondary-btn">
                    Cancel
                </a>
            </div>
        </form>
    </div>
@endsection
