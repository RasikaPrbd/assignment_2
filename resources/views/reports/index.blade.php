@extends('layouts.app')

@section('content')
    <div class="reports-page">

        <div class="reports-header">
            <div>
                <h1>Business Reports</h1>
                <p>Review your sales, purchases and business performance.</p>
            </div>

            <button type="button" class="report-print-btn" onclick="window.print()">
                Print Report
            </button>
        </div>

        <form method="GET" action="{{ route('reports.index') }}" class="report-filter-panel">

            <div class="report-filter-field">
                <label for="start_date">Start Date</label>
                <input type="date" id="start_date" name="start_date" value="{{ $startDate }}">
            </div>

            <div class="report-filter-field">
                <label for="end_date">End Date</label>
                <input type="date" id="end_date" name="end_date" value="{{ $endDate }}">
            </div>

            <div class="report-filter-actions">
                <button type="submit" class="report-primary-btn">
                    Apply Filter
                </button>

                <a href="{{ route('reports.index') }}" class="report-reset-btn">
                    Reset
                </a>
            </div>
        </form>

        <div class="report-section-heading">
            <h2>Sales Overview</h2>
            <p>Invoice totals for the selected period.</p>
        </div>

        <div class="report-summary-grid">
            <div class="report-summary-card">
                <span>Total Sales</span>
                <strong>Rs. {{ number_format($salesRevenue, 2) }}</strong>
                <small>Sales revenue</small>
            </div>

            <div class="report-summary-card">
                <span>Total Invoices</span>
                <strong>{{ number_format($salesSummary['invoice_count']) }}</strong>
                <small>Invoices created</small>
            </div>

            <div class="report-summary-card">
                <span>Amount Received</span>
                <strong>Rs. {{ number_format($salesSummary['paid_amount'], 2) }}</strong>
                <small>Payments recorded</small>
            </div>

            <div class="report-summary-card">
                <span>Outstanding Balance</span>
                <strong>Rs. {{ number_format($salesSummary['outstanding_amount'], 2) }}</strong>
                <small>Remaining invoice balances</small>
            </div>
        </div>

        <div class="report-section-heading">
            <h2>Purchase Overview</h2>
            <p>Summary of received purchases.</p>
        </div>

        <div class="report-summary-grid report-purchase-grid">
            <div class="report-summary-card">
                <span>Total Purchases</span>
                <strong>Rs. {{ number_format($purchaseSummary['total_purchases'], 2) }}</strong>
                <small>Received purchase value</small>
            </div>

            <div class="report-summary-card">
                <span>Purchase Count</span>
                <strong>{{ number_format($purchaseSummary['purchase_count']) }}</strong>
                <small>Purchase records</small>
            </div>
        </div>

        <div class="report-section-heading">
            <h2>Profit Overview</h2>
            <p>Estimated gross profit based on current product costs.</p>
        </div>

        <div class="report-profit-panel">
            <div class="report-profit-item">
                <span>Sales Revenue</span>
                <strong>Rs. {{ number_format($salesRevenue, 2) }}</strong>
            </div>

            <div class="report-profit-item">
                <span>Estimated Cost of Sold Items</span>
                <strong>Rs. {{ number_format($salesCost, 2) }}</strong>
            </div>

            <div class="report-profit-result">
                <span>Estimated Gross Profit</span>
                <strong>Rs. {{ number_format($grossProfit, 2) }}</strong>
            </div>
        </div>

        <p class="report-note">
            Note: Gross profit is an estimate. It uses current product costs
            and does not include operating expenses.
        </p>

    </div>
@endsection
