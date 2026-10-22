<aside class="sidebar">

    <a href="{{ route('dashboard') }}" class="brand">
        Sales & Inventory
    </a>

    <p class="menu-heading">MAIN MENU</p>

    <nav class="sidebar-nav">

        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            Dashboard
        </a>

        <a href="#" class="nav-link">
            Users
        </a>

        <a href="{{ route('customers.index') }}"
            class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
            Customers
        </a>

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
            <a href="#" class="nav-link">Stock Report</a>
        </div>


        <div class="nav-group">
            <p class="menu-heading">TRANSACTIONS</p>

            <a href="{{ route('purchases.index') }}"
                class="nav-link {{ request()->routeIs('purchases.*') ? 'active' : '' }}">
                Purchases
            </a>

            <a href="{{ route('invoices.create') }}"
                class="nav-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
                Invoices
            </a>

            <a href="#" class="nav-link">Payments</a>
        </div>


        <div class="nav-group">
            <p class="menu-heading">ANALYTICS</p>

            <a href="#" class="nav-link">Reports</a>
        </div>

    </nav>

</aside>
