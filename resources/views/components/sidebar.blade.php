@php
    $userRole = auth()->user()?->role?->name;
    $isAdmin = $userRole === 'Admin';
    $isSalesUser = $userRole === 'Sales User';
    $isInventoryUser = $userRole === 'Inventory User';
@endphp

<aside class="sidebar">

    <a href="{{ route('dashboard') }}" class="brand">
        Sales & Inventory
    </a>

    <p class="menu-heading">MAIN MENU</p>

    <nav class="sidebar-nav">

        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            Dashboard
        </a>

        {{-- Admin only --}}
        @if ($isAdmin)
            <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                Users
            </a>
        @endif

        {{-- Admin and Sales User --}}
        @if ($isAdmin || $isSalesUser)
            <a href="{{ route('customers.index') }}"
                class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                Customers
            </a>
        @endif

        {{-- Inventory menu: Admin and Inventory User --}}
        @if ($isAdmin || $isInventoryUser)
            <div class="nav-group">
                <p class="menu-heading">INVENTORY</p>

                <a href="{{ route('products.index') }}"
                    class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                    Products
                </a>

                <a href="{{ route('categories.index') }}"
                    class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                    Categories
                </a>

                <a href="{{ route('suppliers.index') }}"
                    class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                    Suppliers
                </a>

                <a href="{{ route('stock-report.index') }}"
                    class="nav-link {{ request()->routeIs('stock-report.*') ? 'active' : '' }}">
                    Stock Report
                </a>
            </div>
        @endif

        {{-- Inventory purchases --}}
        @if ($isAdmin || $isInventoryUser)
            <div class="nav-group">
                <p class="menu-heading">PURCHASES</p>

                <a href="{{ route('purchases.index') }}"
                    class="nav-link {{ request()->routeIs('purchases.*') ? 'active' : '' }}">
                    Purchases
                </a>
            </div>
        @endif

        {{-- Sales transactions --}}
        @if ($isAdmin || $isSalesUser)
            <div class="nav-group">
                <p class="menu-heading">SALES</p>

                <a href="{{ route('invoices.index') }}"
                    class="nav-link {{ request()->routeIs('invoices.index', 'invoices.show', 'invoices.create', 'invoices.edit') ? 'active' : '' }}">
                    Invoices
                </a>

                <a href="{{ route('invoices.payment-status') }}"
                    class="nav-link {{ request()->routeIs('invoices.payment-status') ? 'active' : '' }}">
                    Payment Status
                </a>

                <a href="{{ route('payments.index') }}"
                    class="nav-link {{ request()->routeIs('payments.*') ? 'active' : '' }}">
                    Payment History
                </a>
            </div>
        @endif

        {{-- Reports --}}
        @if ($isAdmin || $isSalesUser)
            <div class="nav-group">
                <p class="menu-heading">ANALYTICS</p>

                <a href="{{ route('reports.index') }}"
                    class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    Sales Reports
                </a>
            </div>
        @endif

    </nav>

</aside>
