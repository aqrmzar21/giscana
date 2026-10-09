<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\PartialRenderable;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;

class StaffController extends Controller
{
    use PartialRenderable;

    /**
     * Daftar role yang dapat dipilih pada form user.
     */
    public const ROLES = [
        'admin' => 'Admin',
        'staff' => 'Staff',
        'pimpinan' => 'Pimpinan',
    ];

    public function index(Request $request)
    {
        $roleNames = array_keys(self::ROLES);

        $query = User::with('roles')->where(function($q) use ($roleNames) {
            $q->whereIn('role', $roleNames)->orWhereHas('roles', fn($rq) => $rq->whereIn('name', $roleNames));
        });

        if ($request->filled('role') && array_key_exists($request->role, self::ROLES)) {
            $role = $request->role;
            $query->where(function($q) use ($role) {
                $q->where('role', $role)->orWhereHas('roles', fn($rq) => $rq->where('name', $role));
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $perPage = $request->get('per_page', 10);
        $staffs = $query->latest()->paginate($perPage)->withQueryString();
        $roles = self::ROLES;
        
        return $this->partialView('admin.staff.index', compact('staffs', 'roles'));
    }

    public function create()
    {
        $roles = self::ROLES;

        return $this->partialView('admin.staff.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', Rule::in(array_keys(self::ROLES))],
            'phone' => ['nullable', 'string', 'max:20'],
            'organization' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'phone' => $request->phone,
            'organization' => $request->organization,
            'is_active' => $request->has('is_active'),
        ]);

        $user->syncRoles([Role::findOrCreate($request->role, 'web')]);

        return redirect()->route('admin.staff.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $staff)
    {
        $roles = self::ROLES;

        return $this->partialView('admin.staff.edit', compact('staff', 'roles'));
    }

    public function update(Request $request, User $staff)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', \Illuminate\Validation\Rule::unique('users')->ignore($staff->id)],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', Rule::in(array_keys(self::ROLES))],
            'phone' => ['nullable', 'string', 'max:20'],
            'organization' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ]);

        // Cegah admin menurunkan role dirinya sendiri
        if ($staff->is(auth()->user()) && $request->role !== 'admin' && $staff->isAdmin()) {
            return back()->withInput()
                ->withErrors(['role' => 'Anda tidak dapat mengubah role akun Anda sendiri.']);
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'phone' => $request->phone,
            'organization' => $request->organization,
            'is_active' => $request->has('is_active'),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $staff->update($data);
        $staff->syncRoles([Role::findOrCreate($request->role, 'web')]);

        return redirect()->route('admin.staff.index')
            ->with('success', 'Data user berhasil diperbarui.');
    }

    public function destroy(User $staff)
    {
        if ($staff->is(auth()->user())) {
            return redirect()->route('admin.staff.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $staff->delete();

        return redirect()->route('admin.staff.index')
            ->with('success', 'User berhasil dihapus.');
    }
}
