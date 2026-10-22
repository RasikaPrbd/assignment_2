@extends('layouts.app')

@section('title', 'New Sale')

@section('content')
    <div class="page-heading">
        <div>
            <h1>New Sale</h1>
            <p>Add products, apply an optional discount and record payment.</p>
        </div>
    </div>

    <div class="form-card">
        @if ($errors->any())
            <div class="alert-error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('invoices.store') }}" method="POST" id="sale-form">
            @csrf

            <input type="hidden" name="invoice_date" value="{{ date('Y-m-d') }}">

            <h3>Products</h3>

            <div id="invoice-items">
                <div class="invoice-item">
                    <div class="form-group">
                        <label>Product</label>
                        <select name="items[0][product_id]" class="product-select" required>
                            <option value="">Select Product</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" data-price="{{ $product->selling_price }}"
                                    data-stock="{{ $product->quantity }}">
                                    {{ $product->name }} — Rs. {{ number_format($product->selling_price, 2) }}
                                    (Stock: {{ $product->quantity }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Quantity</label>
                        <input type="number" name="items[0][quantity]" class="quantity-input" min="1" value="1"
                            required>
                    </div>

                    <p class="line-total">Line Total: Rs. 0.00</p>
                </div>
            </div>

            <button type="button" id="add-item" class="secondary-btn">
                + Add Product
            </button>

            <hr>

            <h3>Order Summary</h3>

            <div class="form-group">
                <label>Subtotal (Rs.)</label>
                <input type="number" id="subtotal" value="0.00" readonly>
            </div>

            <div class="form-group">
                <label>Discount Type</label>
                <select name="discount_type" id="discount_type">
                    <option value="percentage">Percentage (%)</option>
                    <option value="fixed">Fixed Amount (Rs.)</option>
                </select>
            </div>

            <div class="form-group">
                <label>Discount Value</label>
                <input type="number" name="discount_value" id="discount_value" min="0" step="0.01" value="0"
                    required>
            </div>

            <div class="form-group">
                <label>Tax Rate (%)</label>
                <input type="number" name="tax_rate" id="tax_rate" min="0" max="100" step="0.01"
                    value="18" required>
            </div>

            <p>Discount Amount: Rs. <strong id="discount-amount">0.00</strong></p>
            <p>Tax Amount: Rs. <strong id="tax-amount">0.00</strong></p>
            <h3>Grand Total: Rs. <span id="grand-total">0.00</span></h3>

            <hr>

            <h3>Payment</h3>

            <div class="form-group">
                <label>Payment Method</label>
                <select name="payment_method" required>
                    <option value="cash">Cash</option>
                    <option value="card">Card</option>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="other">Other</option>
                </select>
            </div>

            <div class="form-group">
                <label>Paid Amount (Rs.)</label>
                <input type="number" name="paid_amount" id="paid_amount" min="0" step="0.01" value="0"
                    required>
            </div>

            <p>Outstanding Balance: Rs. <strong id="balance-amount">0.00</strong></p>
            <p>Payment Status: <strong id="payment-status">Unpaid</strong></p>

            <div class="form-group">
                <label>Notes</label>
                <textarea name="notes" rows="3">{{ old('notes') }}</textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="primary-btn">Continue to Customer Details</button>
                <a href="{{ route('dashboard') }}" class="secondary-btn">Cancel</a>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const container = document.getElementById('invoice-items');
            let itemIndex = 1;

            const money = value => Number(value).toFixed(2);

            function calculateTotals() {
                let subtotal = 0;

                container.querySelectorAll('.invoice-item').forEach(row => {
                    const select = row.querySelector('.product-select');
                    const quantityInput = row.querySelector('.quantity-input');
                    const option = select.options[select.selectedIndex];

                    const price = Number(option?.dataset.price || 0);
                    const stock = Number(option?.dataset.stock || 0);
                    const quantity = Number(quantityInput.value || 0);

                    quantityInput.max = stock;

                    const lineTotal = price * quantity;
                    row.querySelector('.line-total').textContent =
                        `Line Total: Rs. ${money(lineTotal)}`;

                    if (select.value && quantity > stock) {
                        quantityInput.setCustomValidity(`Only ${stock} units are available.`);
                    } else {
                        quantityInput.setCustomValidity('');
                    }

                    subtotal += lineTotal;
                });

                const discountValue = Math.max(
                    0, Number(document.getElementById('discount_value').value || 0)
                );

                const discountType = document.getElementById('discount_type').value;
                const discount = discountType === 'percentage' ?
                    subtotal * Math.min(discountValue, 100) / 100 :
                    Math.min(discountValue, subtotal);

                const taxable = Math.max(0, subtotal - discount);
                const taxRate = Math.min(
                    100, Math.max(0, Number(document.getElementById('tax_rate').value || 0))
                );
                const tax = taxable * taxRate / 100;
                const total = taxable + tax;

                const paid = Math.max(
                    0, Number(document.getElementById('paid_amount').value || 0)
                );
                const balance = Math.max(0, total - paid);

                document.getElementById('subtotal').value = money(subtotal);
                document.getElementById('discount-amount').textContent = money(discount);
                document.getElementById('tax-amount').textContent = money(tax);
                document.getElementById('grand-total').textContent = money(total);
                document.getElementById('balance-amount').textContent = money(balance);

                let status = 'Unpaid';
                if (paid >= total && total > 0) status = 'Paid';
                else if (paid > 0) status = 'Partially Paid';

                document.getElementById('payment-status').textContent = status;

                // The browser display is only a preview; the server must validate
                // the actual amount and calculate all totals again.
                document.getElementById('paid_amount').max = money(total);
                document.getElementById('paid_amount').setCustomValidity(
                    paid > total ? 'Paid amount cannot exceed the grand total.' : ''
                );
            }

            document.getElementById('add-item').addEventListener('click', () => {
                const first = container.querySelector('.invoice-item');
                const row = first.cloneNode(true);

                row.querySelectorAll('select, input').forEach(field => {
                    field.name = field.name.replace(/\[\d+\]/, `[${itemIndex}]`);
                    if (field.tagName === 'SELECT') field.selectedIndex = 0;
                    else field.value = 1;
                    field.setCustomValidity('');
                });

                const remove = document.createElement('button');
                remove.type = 'button';
                remove.className = 'danger-btn';
                remove.textContent = 'Remove Product';
                remove.addEventListener('click', () => {
                    row.remove();
                    calculateTotals();
                });

                row.appendChild(remove);
                container.appendChild(row);
                itemIndex++;
                calculateTotals();
            });

            container.addEventListener('input', calculateTotals);
            container.addEventListener('change', calculateTotals);
            document.getElementById('discount_type').addEventListener('change', calculateTotals);

            ['discount_value', 'tax_rate', 'paid_amount'].forEach(id => {
                document.getElementById(id).addEventListener('input', calculateTotals);
            });

            calculateTotals();
        });
    </script>
@endsection
