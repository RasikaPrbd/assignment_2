<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    private function ensureAdmin(): void
    {
        abort_unless(
            Auth::user()?->role?->name === 'Admin',
            403,
            'Only administrators can manage users.'
        );
    }


    public function index(Request $request)
    {
        $this->ensureAdmin();

        $search = trim((string) $request->query('search', ''));
        $roleId = $request->query('role_id');

        $users = User::with('role')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($roleId !== '' && $roleId !== null, function ($query) use ($roleId) {
                $query->where('role_id', $roleId);
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $roles = \App\Models\Role::orderBy('name')->get();

        return view('users.index', compact(
            'users',
            'search',
            'roles',
            'roleId'
        ));
    }


    public function create()
    {
        $this->ensureAdmin();

        $roles = Role::whereIn('name', [
            'Sales User',
            'Inventory User',
        ])->orderBy('name')->get();

        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $this->ensureAdmin();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
            'role_id' => [
                'required',
                Rule::exists('roles', 'id')->where(function ($query) {
                    $query->whereIn('name', [
                        'Sales User',
                        'Inventory User',
                    ]);
                }),
            ],
            'is_active' => ['required', 'boolean'],
        ]);

        // The User model's hashed cast hashes the password.
        User::create($validated);

        return redirect()
            ->route('users.index')
            ->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        $this->ensureAdmin();

        return redirect()->route('users.edit', $user);
    }

    public function edit(User $user)
    {
        $this->ensureAdmin();

        if ($user->role?->name === 'Admin') {
            $roles = Role::where('id', $user->role_id)->get();
        } else {
            $roles = Role::whereIn('name', [
                'Sales User',
                'Inventory User',
            ])->orderBy('name')->get();
        }

        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $this->ensureAdmin();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ],
            'role_id' => [
                'required',
                Rule::exists('roles', 'id')->where(function ($query) use ($user) {
                    if ($user->role?->name === 'Admin') {
                        $query->where('id', $user->role_id)
                            ->where('name', 'Admin');
                    } else {
                        $query->whereIn('name', [
                            'Sales User',
                            'Inventory User',
                        ]);
                    }
                }),
            ],
            'is_active' => ['required', 'boolean'],
        ]);

        // Do not change the password when the field is left blank.
        if (blank($validated['password'] ?? null)) {
            unset($validated['password']);
        }

        // Prevent an administrator from locking themselves out.
        if (
            $user->is(Auth::user()) &&
            (
                (int) $validated['role_id'] !== (int) $user->role_id ||
                (string) $validated['is_active'] === '0'
            )
        ) {
            return back()
                ->withInput()
                ->with('error', 'You cannot change your own role or deactivate your own account.');
        }

        // Protect the last active administrator.
        $adminRole = Role::where('name', 'Admin')->first();

        $willRemainActiveAdmin = $adminRole
            && (int) $validated['role_id'] === (int) $adminRole->id
            && (string) $validated['is_active'] === '1';

        $otherActiveAdmins = User::where('is_active', true)
            ->where('id', '!=', $user->id)
            ->whereHas('role', function ($query) {
                $query->where('name', 'Admin');
            })
            ->count();

        if (
            $user->role?->name === 'Admin' &&
            $user->is_active &&
            ! $willRemainActiveAdmin &&
            $otherActiveAdmins === 0
        ) {
            return back()
                ->withInput()
                ->with('error', 'At least one active administrator must remain.');
        }

        $user->update($validated);

        return redirect()
            ->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $this->ensureAdmin();

        if ($user->is(Auth::user())) {
            return redirect()
                ->route('users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        // Preserve at least one administrator account.
        if ($user->role?->name === 'Admin') {
            $adminCount = User::whereHas('role', function ($query) {
                $query->where('name', 'Admin');
            })->count();

            if ($adminCount <= 1) {
                return redirect()
                    ->route('users.index')
                    ->with('error', 'The last administrator cannot be deleted.');
            }
        }

        // Transaction records reference their creator.
        if (
            $user->invoices()->exists() ||
            $user->purchases()->exists() ||
            $user->payments()->exists()
        ) {
            return redirect()
                ->route('users.index')
                ->with('error', 'This user has transaction records. Deactivate the account instead.');
        }

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'User deleted successfully.');
    }
}
