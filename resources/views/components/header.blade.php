<header class="top-header">

    <div class="header-title">
        <h2>@yield('page-title', 'Dashboard')</h2>
        <p>Sales & Inventory Management System</p>
    </div>

    <div class="user-section">

        <div class="user-info">
            <span class="user-name">
                {{ auth()->user()->name }}
            </span>
            <b>:</b>
            <span class="user-role">
                {{ auth()->user()->role?->name }}
            </span>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="logout-btn">
                Logout
            </button>
        </form>

    </div>

</header>
