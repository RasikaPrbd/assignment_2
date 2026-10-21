<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::latest()->paginate(10);

        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }


    public function findMatches(Request $request)
    {
        $validated = $request->validate([
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
        ]);

        $phone = preg_replace(
            '/[\s\-()+]/',
            '',
            $validated['phone'] ?? ''
        );

        $email = strtolower(trim($validated['email'] ?? ''));

        if ($phone === '' && $email === '') {
            return response()->json(['customers' => []]);
        }

        $query = Customer::query();

        $query->where(function ($q) use ($phone, $email) {
            if ($phone !== '') {
                $q->whereRaw(
                    "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(phone, ' ', ''), '-', ''), '(', ''), ')', ''), '+', '') = ?",
                    [$phone]
                );
            }

            if ($email !== '') {
                $method = $phone !== '' ? 'orWhereRaw' : 'whereRaw';

                $q->{$method}(
                    'LOWER(email) = ?',
                    [$email]
                );
            }
        });

        $customers = $query
            ->where('is_active', true)
            ->limit(10)
            ->get([
                'id',
                'customer_code',
                'name',
                'phone',
                'email',
                'address',
            ]);

        return response()->json([
            'customers' => $customers,
        ]);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ]);


        do {
            $customerCode = 'CUS-' . \Illuminate\Support\Str::upper(
                \Illuminate\Support\Str::random(8)
            );
        } while (
            Customer::where('customer_code', $customerCode)->exists()
        );

        $validated['customer_code'] = $customerCode;

        Customer::create($validated);

        return redirect()->route('customers.index')
            ->with('success', 'Customer created successfully.');
    }



    public function show(Customer $customer)
    {
        return redirect()->route('customers.index');
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([

            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ]);

        $customer->update($validated);

        return redirect()->route('customers.index')
            ->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        try {
            $customer->delete();

            return redirect()->route('customers.index')
                ->with('success', 'Customer deleted successfully.');
        } catch (QueryException $e) {
            return redirect()->route('customers.index')
                ->with('error', 'Cannot delete this customer because they are linked to existing invoices.');
        }
    }
}
