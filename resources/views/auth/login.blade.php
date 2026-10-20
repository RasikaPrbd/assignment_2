<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Sign In | Sales & Inventory</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="login-page">

    <main class="login-card">

        <header class="login-brand">
            <div class="brand-logo" aria-hidden="true">SI</div>

            <p class="brand-label">SALES & INVENTORY MANAGEMENT</p>

            <h1>Welcome Back</h1>

            <p class="brand-description">
                Sign in to access your account
            </p>
        </header>

        <form action="{{ route('login.submit') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="email">Email Address</label>

                <input type="email" id="email" name="email"
                    class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}"
                    placeholder="Enter your email address" autocomplete="username" required autofocus>

                @error('email')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Password</label>

                <input type="password" id="password" name="password"
                    class="form-control @error('password') is-invalid @enderror" placeholder="Enter your password"
                    autocomplete="current-password" required>

                @error('password')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="remember-row">
                <input type="checkbox" id="remember" name="remember" value="1" @checked(old('remember'))>

                <label for="remember">Remember me</label>
            </div>

            <button type="submit" class="btn-primary">
                Sign In
            </button>

            @if ($errors->has('email') && !$errors->has('password') && !$errors->has('email'))
                {{-- Reserved for additional authentication messages --}}
            @endif

            @if ($errors->any())
                <div class="error-alert" role="alert">
                    <strong>Unable to sign in.</strong>

                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif
        </form>

        <footer class="login-footer">
            Sales & Inventory Management System
        </footer>

    </main>

</body>

</html>
