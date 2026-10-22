<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::latest()->paginate(10);

        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_name' => ['required', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^(?:0|94|\+94)7[0-9]{8}$/', 'max:30'],
            'email' => ['required', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ], [
            'phone.regex' => 'Enter a valid Sri Lankan mobile number (e.g. 0771234567 or +94771234567).',
        ]);

        Supplier::create($validated);

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'Supplier created successfully.');
    }

    public function show(Supplier $supplier)
    {
        return redirect()->route('suppliers.index');
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'supplier_name' => ['required', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^(?:0|94|\+94)7[0-9]{8}$/', 'max:30'],
            'email' => ['required', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],

        ], [
            'phone.regex' => 'Enter a valid Sri Lankan mobile number (e.g. 0771234567 or +94771234567).',
        ]);

        $supplier->update($validated);

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        try {
            $supplier->delete();

            return redirect()
                ->route('suppliers.index')
                ->with('success', 'Supplier deleted successfully.');
        } catch (QueryException $e) {
            return redirect()
                ->route('suppliers.index')
                ->with('error', 'Cannot delete this supplier because it is linked to existing products or purchases.');
        }
    }
}
