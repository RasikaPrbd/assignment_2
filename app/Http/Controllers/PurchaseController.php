<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = Purchase::with(['supplier', 'user'])
            ->latest()
            ->paginate(10);

        return view('purchases.index', compact('purchases'));
    }

    public function create()
    {
        $suppliers = Supplier::where('is_active', true)
            ->orderBy('supplier_name')
            ->get();

        $products = Product::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('purchases.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'purchase_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => [
                'required',
                'integer',
                'distinct',
                'exists:products,id',
            ],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_cost' => ['required', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($validated) {
            $subtotal = 0;
            $preparedItems = [];

            foreach ($validated['items'] as $item) {
                $product = Product::whereKey($item['product_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                if (!$product->is_active) {
                    throw ValidationException::withMessages([
                        'items' => "Product {$product->name} is inactive.",
                    ]);
                }

                $lineTotal = round(
                    (float) $item['unit_cost'] * (int) $item['quantity'],
                    2
                );

                $subtotal += $lineTotal;

                $preparedItems[] = [
                    'product' => $product,
                    'quantity' => (int) $item['quantity'],
                    'unit_cost' => round((float) $item['unit_cost'], 2),
                    'line_total' => $lineTotal,
                ];
            }

            $purchase = Purchase::create([
                'supplier_id' => $validated['supplier_id'],
                'user_id' => \Illuminate\Support\Facades\Auth::id(),
                'purchase_number' => $this->generatePurchaseNumber(),
                'purchase_date' => $validated['purchase_date'],
                'subtotal' => $subtotal,
                'total_amount' => $subtotal,
                'status' => 'received',
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($preparedItems as $item) {
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product']->id,
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'line_total' => $item['line_total'],
                ]);

                $item['product']->increment('quantity', $item['quantity']);

                // Update the latest purchase cost.
                $item['product']->update([
                    'cost_price' => $item['unit_cost'],
                ]);
            }
        });

        return redirect()->route('purchases.index')
            ->with('success', 'Purchase saved and product stock updated successfully.');
    }

    private function generatePurchaseNumber(): string
    {
        do {
            $number = 'PUR-' . Str::upper(Str::random(10));
        } while (Purchase::where('purchase_number', $number)->exists());

        return $number;
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'user', 'items.product']);

        return view('purchases.show', compact('purchase'));
    }

    public function edit(Purchase $purchase)
    {
        return redirect()->route('purchases.show', $purchase);
    }

    public function update(Request $request, Purchase $purchase)
    {
        return redirect()->route('purchases.show', $purchase)
            ->with('error', 'Saved purchases cannot be edited. Create a new purchase to correct stock.');
    }

    public function destroy(Purchase $purchase)
    {
        return redirect()->route('purchases.index')
            ->with('error', 'Purchases cannot be deleted because stock has already been updated.');
    }
}
