@extends('layouts.app')

@section('title', 'New Purchase')

@section('content')
    <div class="page-heading">
        <div>
            <h1>New Purchase</h1>
            <p>Record stock received from a supplier.</p>
        </div>
    </div>

    <div class="form-card">
        <form action="{{ route('purchases.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="supplier_id">Supplier *</label>
                <select name="supplier_id" id="supplier_id" required>
                    <option value="">Select Supplier</option>
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->supplier_name }}
                        </option>
                    @endforeach
                </select>
                @error('supplier_id')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="purchase_date">Purchase Date *</label>
                <input type="date" name="purchase_date" id="purchase_date"
                    value="{{ old('purchase_date', date('Y-m-d')) }}" required>
                @error('purchase_date')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <h2>Purchase Items</h2>

            @error('items')
                <p class="field-error">{{ $message }}</p>
            @enderror

            <div id="purchase-items">
                <div class="purchase-item-row">
                    <div class="form-group">
                        <label>Product *</label>
                        <select name="items[0][product_id]" required>
                            <option value="">Select Product</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" data-cost="{{ $product->cost_price }}">
                                    {{ $product->name }} ({{ $product->code }})
                                </option>
                            @endforeach
                        </select>
                        @error('items.0.product_id')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Quantity *</label>
                        <input type="number" name="items[0][quantity]" min="1" step="1" value="1"
                            required>
                        @error('items.0.quantity')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Unit Cost *</label>
                        <input type="number" name="items[0][unit_cost]" min="0" step="0.01" required>
                        @error('items.0.unit_cost')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <button type="button" id="add-item" class="secondary-btn">
                + Add Another Product
            </button>

            <div class="form-group" style="margin-top: 20px;">
                <label for="notes">Notes</label>
                <textarea name="notes" id="notes" rows="3">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <p class="form-hint">
                Total amount is calculated on the server. Stock is updated only
                after the purchase is saved successfully.
            </p>

            <div class="form-actions">
                <button type="submit" class="primary-btn">Save Purchase</button>
                <a href="{{ route('purchases.index') }}" class="secondary-btn">Cancel</a>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const container = document.getElementById('purchase-items');
            const addButton = document.getElementById('add-item');
            const productOptions =
                {{ Illuminate\Support\Js::from(
                    $products->map(
                            fn($product) => [
                                'id' => $product->id,
                                'name' => $product->name,
                                'code' => $product->code,
                                'cost' => $product->cost_price,
                            ],
                        )->values(),
                ) }};

            let nextIndex = 1;

            addButton.addEventListener('click', () => {
                const row = document.createElement('div');
                row.className = 'purchase-item-row';

                const productGroup = document.createElement('div');
                productGroup.className = 'form-group';
                const productLabel = document.createElement('label');
                productLabel.textContent = 'Product *';
                const productSelect = document.createElement('select');
                productSelect.name = `items[${nextIndex}][product_id]`;
                productSelect.required = true;

                const placeholder = document.createElement('option');
                placeholder.value = '';
                placeholder.textContent = 'Select Product';
                productSelect.appendChild(placeholder);

                productOptions.forEach(product => {
                    const option = document.createElement('option');
                    option.value = product.id;
                    option.textContent = `${product.name} (${product.code})`;
                    option.dataset.cost = product.cost;
                    productSelect.appendChild(option);
                });

                productGroup.append(productLabel, productSelect);

                const quantityGroup = document.createElement('div');
                quantityGroup.className = 'form-group';
                const quantityLabel = document.createElement('label');
                quantityLabel.textContent = 'Quantity *';
                const quantityInput = document.createElement('input');
                quantityInput.type = 'number';
                quantityInput.name = `items[${nextIndex}][quantity]`;
                quantityInput.min = '1';
                quantityInput.step = '1';
                quantityInput.value = '1';
                quantityInput.required = true;
                quantityGroup.append(quantityLabel, quantityInput);

                const costGroup = document.createElement('div');
                costGroup.className = 'form-group';
                const costLabel = document.createElement('label');
                costLabel.textContent = 'Unit Cost *';
                const costInput = document.createElement('input');
                costInput.type = 'number';
                costInput.name = `items[${nextIndex}][unit_cost]`;
                costInput.min = '0';
                costInput.step = '0.01';
                costInput.required = true;
                costGroup.append(costLabel, costInput);

                productSelect.addEventListener('change', () => {
                    const selected = productSelect.selectedOptions[0];
                    if (selected.dataset.cost) {
                        costInput.value = selected.dataset.cost;
                    }
                });

                const removeButton = document.createElement('button');
                removeButton.type = 'button';
                removeButton.className = 'danger-btn';
                removeButton.textContent = 'Remove Item';
                removeButton.addEventListener('click', () => row.remove());

                row.append(productGroup, quantityGroup, costGroup, removeButton);
                container.appendChild(row);
                nextIndex++;
            });

            container.querySelector('select').addEventListener('change', function() {
                const selected = this.selectedOptions[0];
                const costInput = this.closest('.purchase-item-row')
                    .querySelector('input[name$="[unit_cost]"]');

                if (selected.dataset.cost) {
                    costInput.value = selected.dataset.cost;
                }
            });
        });
    </script>
@endsection
