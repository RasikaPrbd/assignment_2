@extends('layouts.app')

@section('title', 'Dashboard')

@section('page-title', 'Dashboard')

@section('content')

    <h1>Welcome, {{ auth()->user()->name }}!</h1>

    <p class="welcome-text">
        Here is an overview of your business.
    </p>

    <div class="summary-grid">

        <div class="summary-card">
            <h3>Total Products</h3>
            <p>0</p>
        </div>

        <div class="summary-card">
            <h3>Total Customers</h3>
            <p>0</p>
        </div>

        <div class="summary-card">
            <h3>Total Suppliers</h3>
            <p>0</p>
        </div>

        <div class="summary-card">
            <h3>Total Sales</h3>
            <p>Rs. 0.00</p>
        </div>

    </div>

    <div class="dashboard-panel">
        <h3>Quick Overview</h3>

        <p>
            Manage your products, customers, suppliers,
            purchases, sales and payments from the sidebar.
        </p>
    </div>

@endsection
