@extends('layouts.app')

@section('title', 'Add Customer')

@section('content')
    <div class="page-heading">
        <div>
            <h1>Add Customer</h1>
            <p>Enter details or select a matching existing customer.</p>
        </div>
    </div>

    <div class="customer-create-layout">
        <div class="form-card">
            <form id="customer-form" action="{{ route('customers.store') }}" method="POST">
                @if (request('return_to') === 'checkout')
                    <input type="hidden" name="return_to" value="checkout">
                @endif
                @csrf
                <input type="hidden" id="existing_customer_id" name="existing_customer_id">
                <p class="form-hint">
                    Customer Code will be generated automatically.
                </p>

                <div class="form-group">
                    <label for="name">Customer Name *</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="phone">Phone *</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}" required
                        autocomplete="tel">
                    @error('phone')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}">
                    @error('email')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" name="address" rows="3">{{ old('address') }}</textarea>
                    @error('address')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="is_active">Status *</label>
                    <select id="is_active" name="is_active" required>
                        <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('is_active')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-actions">
                    <button type="submit" class="primary-btn">Save New Customer</button>
                    <a href="{{ route('customers.index') }}" class="secondary-btn">Cancel</a>
                </div>
            </form>
        </div>

        <aside class="customer-matches">
            <h2>Existing Customers</h2>
            <p id="match-message" class="form-hint">
                Enter a phone number or email to find matching customers.
            </p>
            <div id="customer-match-results" aria-live="polite"></div>
        </aside>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const phoneInput = document.getElementById('phone');
            const emailInput = document.getElementById('email');
            const customerForm = document.getElementById('customer-form');
            const existingCustomerIdInput = document.getElementById('existing_customer_id');
            const nameInput = document.getElementById('name');
            const results = document.getElementById('customer-match-results');
            const message = document.getElementById('match-message');

            let timer;
            let controller;

            function normalizePhone(value) {
                return value.replace(/[\s\-()+]/g, '');
            }

            async function searchCustomers() {
                const phone = phoneInput.value.trim();
                const email = emailInput.value.trim();

                results.replaceChildren();

                if (!phone && !email) {
                    message.textContent = 'Enter a phone number or email to find matching customers.';
                    return;
                }

                message.textContent = 'Searching...';

                if (controller) controller.abort();
                controller = new AbortController();

                try {
                    const params = new URLSearchParams({
                        phone: normalizePhone(phone),
                        email: email
                    });

                    const response = await fetch(
                        `{{ route('customers.findMatches') }}?${params}`, {
                            headers: {
                                'Accept': 'application/json'
                            },
                            signal: controller.signal
                        }
                    );

                    if (!response.ok) {
                        throw new Error('Customer search failed.');
                    }

                    const data = await response.json();
                    const customers = data.customers || [];

                    if (!customers.length) {
                        message.textContent =
                            'No matching customers found. You can continue creating a new customer.';
                        return;
                    }

                    message.textContent =
                        'Possible matches found. Select a customer to view their existing record.';

                    customers.forEach(customer => {
                        const card = document.createElement('button');
                        card.type = 'button';
                        card.className = 'customer-match-card';

                        const code = document.createElement('strong');
                        code.textContent = customer.customer_code;

                        const name = document.createElement('p');
                        name.textContent = customer.name;

                        const phone = document.createElement('p');
                        phone.textContent = `Phone: ${customer.phone}`;

                        const email = document.createElement('p');
                        email.textContent = `Email: ${customer.email || 'Not provided'}`;

                        const action = document.createElement('p');
                        action.textContent = 'Use this customer for invoice →';
                        card.append(code, name, phone, email, action);

                        card.addEventListener('click', () => {
                            existingCustomerIdInput.value = customer.id;

                            nameInput.value = customer.name || '';
                            phoneInput.value = customer.phone || '';
                            emailInput.value = customer.email || '';

                            message.textContent =
                                `Using existing customer: ${customer.name}. Continuing...`;

                            customerForm.requestSubmit();
                        });

                        results.appendChild(card);
                    });
                } catch (error) {
                    if (error.name !== 'AbortError') {
                        message.textContent = 'Could not search customers. Please try again.';
                    }
                }
            }

            function scheduleSearch() {
                clearTimeout(timer);
                timer = setTimeout(searchCustomers, 350);
            }

            phoneInput.addEventListener('input', scheduleSearch);
            emailInput.addEventListener('input', scheduleSearch);
        });
    </script>
@endsection
