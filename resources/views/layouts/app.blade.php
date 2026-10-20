<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Dashboard') | Sales & Inventory</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="dashboard-layout">

        @include('components.sidebar')

        <main class="main-content">
            @include('components.header')

            <section class="dashboard-content">
                @yield('content')
            </section>

            <footer class="dashboard-footer">
                Sales & Inventory Management System
            </footer>
        </main>

    </div>
</body>

</html>
