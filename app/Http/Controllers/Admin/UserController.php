<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Unit;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use App\Models\MetaType;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $roleFilter = $request->input('role');
        $unitFilter = $request->input('unit');

        $usersQuery = User::with(['role', 'unit']);

        $usersQuery->whereHas('role', function ($q) {
            $q->where('name', '!=', 'admin');
        });
        
        if ($search) {
            $usersQuery->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%");
            });
        }

        if ($roleFilter) {
            $usersQuery->where('role_id', $roleFilter);
        }

        if ($unitFilter) {
            $usersQuery->where('unit_id', $unitFilter);
        }

        $users = $usersQuery->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        $roles = Role::all();
        $units = Unit::all();

        return view('admin.users.index', compact('users', 'roles', 'units', 'search', 'roleFilter', 'unitFilter'));
    }

    public function create()
    {
        $roles = Role::all();
        $units = Unit::all();
        $educationLevels = MetaType::where('category', 'education_level')->get();
        return view('admin.users.create', compact('roles', 'units', 'educationLevels'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'phone' => ['required', 'string', 'regex:/^[0-9]{10}$/', 'unique:users,phone'],
            'education_id' => ['required', 'exists:meta_types,id'],
            'role_id' => ['required', 'exists:roles,id'],
            'unit_id' => ['required', 'exists:units,id'],
            'password' => ['required', Rules\Password::defaults()],
        ]);

        User::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'date_of_birth' => $request->date_of_birth,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'unit_id' => $request->unit_id,
            'phone' => $request->phone,
            'education_id' => $request->education_id,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Tài khoản đã được tạo thành công.');
    }

    public function show(User $user)
    {
        $user->load(['role', 'unit']);
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $units = Unit::all();
        $educationLevels = MetaType::where('category', 'education_level')->get();
        return view('admin.users.edit', compact('user', 'roles', 'units', 'educationLevels'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'phone' => ['required', 'string', 'regex:/^[0-9]{10}$/'],
            'education_id' => ['required', 'exists:meta_types,id'],
            'role_id' => ['required', 'exists:roles,id'],
            'unit_id' => ['required', 'exists:units,id'],
            'password' => ['nullable', Rules\Password::defaults()],
        ]);

        $data = [
            'full_name' => $request->full_name,
            'email' => $request->email,
            'date_of_birth' => $request->date_of_birth,
            'role_id' => $request->role_id,
            'unit_id' => $request->unit_id,
            'phone' => $request->phone,
            'education_id' => $request->education_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'Thông tin tài khoản đã được cập nhật thành công.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Tài khoản đã được xóa thành công.');
    }
}
